<?php
namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\IuranTagihan;
use App\Models\IuranPembayaran;
use App\Models\User;
use App\Services\IuranReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IuranReportController extends Controller
{
    public function __construct(private IuranReportService $svc){}

    // --- Halaman Ringkasan ---
    public function index(Request $r)
    {
        if (!Schema::hasTable('iuran_tagihans') || !Schema::hasTable('iuran_pembayarans')) {
            [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
            $users = User::role('anggota')->orderBy('name')->get(['id','name']);
            $userId = null; $method = null; $arus = collect(); $topPiutang = [];
            $totalTagihan = 0; $totalBayar = 0; $outstanding = 0;
            return view('bendahara.laporan.index', compact('start','end','method','userId','users','totalTagihan','totalBayar','outstanding','arus','topPiutang'));
        }

        [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
        $userId = $r->integer('user_id') ?: null;
        $method = $r->get('method');

        $totalTagihan = $this->svc->totalTagihan($start,$end,$userId);
        $totalBayar = $this->svc->totalPembayaranVerified($start,$end,$userId,$method);
        $outstanding = $this->svc->outstandingAsOf($end, $userId);

        $arus = IuranPembayaran::select(DB::raw('DATE(paid_at) d'), DB::raw('SUM(amount) total'))
            ->where('status','verified')
            ->when($userId, fn($q)=>$q->where('user_id',$userId))
            ->when($method, fn($q)=>$q->where('method',$method))
            ->whereBetween('paid_at',[$start,$end])
            ->groupBy('d')->orderBy('d')
            ->get();

        $aging = $this->svc->agingByUser($end);
        $topPiutang = array_slice($aging, 0, 10, true);

        $users = User::role('anggota')->orderBy('name')->get(['id','name']);
        return view('bendahara.laporan.index', compact('start','end','method','userId','users','totalTagihan','totalBayar','outstanding','arus','topPiutang'));
    }

    // --- Arus Kas ---
    public function arusKas(Request $r)
    {
        [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
        $method = $r->get('method');
        $userId = $r->integer('user_id') ?: null;
        if (!Schema::hasTable('iuran_pembayarans')) {
            $items = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1, [ 'path' => $r->url(), 'query' => $r->query() ]);
            $total = 0; $users = User::role('anggota')->orderBy('name')->get(['id','name']);
            return view('bendahara.laporan.arus-kas', compact('items','total','start','end','method','userId','users'));
        }
        $items = IuranPembayaran::with(['user','tagihan'])
            ->where('status','verified')
            ->when($userId, fn($q)=>$q->where('user_id',$userId))
            ->when($method, fn($q)=>$q->where('method',$method))
            ->whereBetween('paid_at',[$start,$end])
            ->latest('paid_at')->paginate(20)->withQueryString();
        $total = $items->getCollection()->sum('amount');
        $users = User::role('anggota')->orderBy('name')->get(['id','name']);
        return view('bendahara.laporan.arus-kas', compact('items','total','start','end','method','userId','users'));
    }

    // --- Piutang & Aging ---
    public function piutang(Request $r)
    {
        $asOf = $r->get('as_of') ? now()->parse($r->get('as_of'))->endOfDay() : now();
        if (!Schema::hasTable('iuran_tagihans')) {
            return view('bendahara.laporan.piutang', [ 'asOf'=>$asOf, 'rows'=>[] ]);
        }
        $data = $this->svc->agingByUser($asOf);
        return view('bendahara.laporan.piutang', [ 'asOf'=>$asOf, 'rows'=>$data ]);
    }

    // --- Per Anggota (ringkas & ledger) ---
    public function anggotaIndex(Request $r)
    {
        [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
        $rows = User::role('anggota')->withCount([])->get(['id','name']);
        $out = [];
        if (Schema::hasTable('iuran_tagihans') && Schema::hasTable('iuran_pembayarans')) {
            foreach ($rows as $u) {
                $tagihan = $this->svc->totalTagihan($start,$end,$u->id);
                $bayar   = $this->svc->totalPembayaranVerified($start,$end,$u->id);
                $sisa    = $this->svc->outstandingAsOf($end,$u->id);
                $out[] = ['id'=>$u->id,'name'=>$u->name,'tagihan'=>$tagihan,'bayar'=>$bayar,'sisa'=>$sisa];
            }
        }
        usort($out, fn($a,$b)=>$b['sisa'] <=> $a['sisa']);
        return view('bendahara.laporan.anggota.index', compact('out','start','end'));
    }

    public function anggotaShow(User $user, Request $r)
    {
        [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
        $tagihan = collect(); $pays = collect();
        if (Schema::hasTable('iuran_tagihans')) {
            $tagihan = IuranTagihan::where('user_id',$user->id)->whereBetween('jatuh_tempo',[$start,$end])->get();
        }
        if (Schema::hasTable('iuran_pembayarans')) {
            $pays = IuranPembayaran::where('user_id',$user->id)->where('status','verified')->whereBetween('paid_at',[$start,$end])->get();
        }
        return view('bendahara.laporan.anggota.show', [
            'user'=>$user,
            'start'=>$start,
            'end'=>$end,
            'tagihan'=>$tagihan,
            'pays'=>$pays,
        ]);
    }

    // --- Export CSV ---
    public function exportIndexCsv(Request $r): StreamedResponse
    {
        [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
        $method = $r->get('method'); $userId = $r->integer('user_id') ?: null;
        $totalTagihan = Schema::hasTable('iuran_tagihans') ? $this->svc->totalTagihan($start,$end,$userId) : 0;
        $totalBayar = Schema::hasTable('iuran_pembayarans') ? $this->svc->totalPembayaranVerified($start,$end,$userId,$method) : 0;
        $outstanding = Schema::hasTable('iuran_tagihans') && Schema::hasTable('iuran_pembayarans') ? $this->svc->outstandingAsOf($end,$userId) : 0;
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="laporan-ringkasan.csv"'];
        return response()->stream(function() use ($start,$end,$totalTagihan,$totalBayar,$outstanding){
            $out = fopen('php://output','w');
            fputcsv($out,['Dari','Sampai','Total Tagihan','Total Pembayaran (verified)','Outstanding per Akhir']);
            fputcsv($out,[$start->toDateString(),$end->toDateString(),$totalTagihan,$totalBayar,$outstanding]);
            fclose($out);
        },200,$headers);
    }

    public function exportArusCsv(Request $r): StreamedResponse
    {
        [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
        $method = $r->get('method'); $userId = $r->integer('user_id') ?: null;
        $items = Schema::hasTable('iuran_pembayarans')
            ? IuranPembayaran::with(['user','tagihan'])
                ->where('status','verified')
                ->when($userId, fn($q)=>$q->where('user_id',$userId))
                ->when($method, fn($q)=>$q->where('method',$method))
                ->whereBetween('paid_at',[$start,$end])->orderBy('paid_at')->get()
            : collect();
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="arus-kas.csv"'];
        return response()->stream(function() use ($items){
            $out = fopen('php://output','w');
            fputcsv($out,['Tanggal','Kode Pembayaran','Anggota','Tagihan','Jumlah','Metode']);
            foreach ($items as $p) {
                fputcsv($out,[optional($p->paid_at)->format('Y-m-d H:i'),$p->kode,$p->user?->name,$p->tagihan?->kode,$p->amount,strtoupper($p->method ?? '-')]);
            }
            fclose($out);
        },200,$headers);
    }

    public function exportPiutangCsv(Request $r): StreamedResponse
    {
        $asOf = $r->get('as_of') ? now()->parse($r->get('as_of'))->endOfDay() : now();
        $rows = Schema::hasTable('iuran_tagihans') && Schema::hasTable('iuran_pembayarans') ? $this->svc->agingByUser($asOf) : [];
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="piutang-aging.csv"'];
        return response()->stream(function() use ($rows,$asOf){
            $out = fopen('php://output','w');
            fputcsv($out,['As Of', $asOf->toDateString()]);
            fputcsv($out,['Nama','Total','0-30','31-60','61-90','90+']);
            foreach ($rows as $uid=>$row) {
                $b = $row['buckets'] ?? [];
                fputcsv($out,[$row['name'],$row['total'],$b['0-30'] ?? 0,$b['31-60'] ?? 0,$b['61-90'] ?? 0,$b['90+'] ?? 0]);
            }
            fclose($out);
        },200,$headers);
    }

    public function exportAnggotaCsv(Request $r): StreamedResponse
    {
        [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
        $rows = User::role('anggota')->orderBy('name')->get(['id','name']);
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="laporan-anggota.csv"'];
        return response()->stream(function() use ($rows,$start,$end){
            $svc = app(\App\Services\IuranReportService::class);
            $out = fopen('php://output','w');
            fputcsv($out,['Nama','Total Tagihan','Total Pembayaran (verified)','Outstanding per Akhir']);
            foreach ($rows as $u) {
                $tagihan = $svc->totalTagihan($start,$end,$u->id);
                $bayar = $svc->totalPembayaranVerified($start,$end,$u->id);
                $sisa = $svc->outstandingAsOf($end,$u->id);
                fputcsv($out,[$u->name,$tagihan,$bayar,$sisa]);
            }
            fclose($out);
        },200,$headers);
    }

    public function exportLedgerCsv(User $user, Request $r): StreamedResponse
    {
        [$start,$end] = $this->svc->range($r->get('from'), $r->get('to'));
        $tagihan = Schema::hasTable('iuran_tagihans') ? IuranTagihan::where('user_id',$user->id)->whereBetween('jatuh_tempo',[$start,$end])->get() : collect();
        $pays = Schema::hasTable('iuran_pembayarans') ? IuranPembayaran::where('user_id',$user->id)->where('status','verified')->whereBetween('paid_at',[$start,$end])->get() : collect();
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="ledger-'.$user->id.'.csv"'];
        return response()->stream(function() use ($user,$tagihan,$pays,$start,$end){
            $out = fopen('php://output','w');
            fputcsv($out,['Ledger Anggota',$user->name,'Periode',$start->toDateString().' s/d '.$end->toDateString()]);
            fputcsv($out,[]);
            fputcsv($out,['TAGIHAN']);
            fputcsv($out,['Kode','Judul','Jatuh Tempo','Total']);
            foreach ($tagihan as $t) {
                $total = max(0,($t->nominal+$t->denda)-$t->diskon);
                fputcsv($out,[$t->kode,$t->judul,optional($t->jatuh_tempo)->format('Y-m-d'),$total]);
            }
            fputcsv($out,[]);
            fputcsv($out,['PEMBAYARAN (VERIFIED)']);
            fputcsv($out,['Kode','Tanggal','Jumlah','Metode']);
            foreach ($pays as $p) {
                fputcsv($out,[$p->kode,optional($p->paid_at)->format('Y-m-d H:i'),$p->amount,strtoupper($p->method ?? '-')]);
            }
            fclose($out);
        },200,$headers);
    }
}

