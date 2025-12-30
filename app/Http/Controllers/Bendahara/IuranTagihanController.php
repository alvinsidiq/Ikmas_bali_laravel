<?php
namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\StoreTagihanRequest;
use App\Http\Requests\Bendahara\UpdateTagihanRequest;
use App\Models\IuranTagihan;
use App\Models\User;
use App\Support\IuranCode;
use App\Services\IuranStatusService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class IuranTagihanController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('iuran_tagihans')) {
            $q = $request->get('q'); $st = $request->get('status'); $yr = $request->get('year'); $mo = $request->get('month');
            $items = new LengthAwarePaginator([], 0, 20, 1, [ 'path' => $request->url(), 'query' => $request->query() ]);
            return view('bendahara.iuran.tagihan.index', compact('items','q','st','yr','mo'));
        }

        $q = trim((string)$request->get('q'));
        $st = $request->get('status');
        $yr = $request->get('year');
        $mo = $request->get('month');
        $items = IuranTagihan::with('user')
            ->when($q, fn($qr)=>$qr->where(function($x) use ($q){
                $x->where('judul','like',"%$q%")
                  ->orWhere('kode','like',"%$q%")
                  ->orWhere('periode','like',"%$q%")
                  ->orWhereHas('user', fn($u)=>$u->where('name','like',"%$q%"));
            }))
            ->when($st, fn($qr)=>$qr->where('status',$st))
            ->when($yr, fn($qr)=>$qr->whereYear('jatuh_tempo',$yr))
            ->when($mo, fn($qr)=>$qr->whereMonth('jatuh_tempo',$mo))
            ->latest('jatuh_tempo')->latest('id')
            ->paginate(20)->withQueryString();
        return view('bendahara.iuran.tagihan.index', compact('items','q','st','yr','mo'));
    }

    public function create(){ $users = User::role('anggota')->orderBy('name')->get(['id','name']); return view('bendahara.iuran.tagihan.create', compact('users')); }

    public function store(StoreTagihanRequest $request, IuranStatusService $svc)
    {
        $data = $request->validated();
        $t = new IuranTagihan($data);
        $t->kode = IuranCode::inv();
        $t->status = 'unpaid';
        $t->save();
        $svc->refreshTagihan($t);
        return redirect()->route('bendahara.tagihan.index')->with('success','Tagihan dibuat.');
    }

    public function show(IuranTagihan $tagihan)
    { $tagihan->load(['user','payments'=>fn($q)=>$q->latest()]); return view('bendahara.iuran.tagihan.show', compact('tagihan')); }

    public function edit(IuranTagihan $tagihan)
    { $users = User::role('anggota')->orderBy('name')->get(['id','name']); return view('bendahara.iuran.tagihan.edit', compact('tagihan','users')); }

    public function update(UpdateTagihanRequest $request, IuranTagihan $tagihan, IuranStatusService $svc)
    {
        $tagihan->fill($request->validated());
        $tagihan->save();
        $svc->refreshTagihan($tagihan);
        return redirect()->route('bendahara.tagihan.show',$tagihan)->with('success','Tagihan diperbarui.');
    }

    public function destroy(IuranTagihan $tagihan)
    { $tagihan->delete(); return back()->with('success','Tagihan dihapus.'); }

    public function export(Request $request): StreamedResponse
    {
        if (!Schema::hasTable('iuran_tagihans')) {
            $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="iuran-tagihan.csv"'];
            return response()->stream(function(){
                $out = fopen('php://output','w');
                fputcsv($out, ['Kode','Nama','Judul','Periode','Jatuh Tempo','Total','Terbayar','Status']);
                fclose($out);
            }, 200, $headers);
        }
        $items = IuranTagihan::with('user')->latest('jatuh_tempo')->get();
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="iuran-tagihan.csv"'];
        return response()->stream(function() use ($items){
            $out = fopen('php://output','w');
            fputcsv($out, ['Kode','Nama','Judul','Periode','Jatuh Tempo','Total','Terbayar','Status']);
            foreach ($items as $t) {
                $total = max(0,($t->nominal+$t->denda)-$t->diskon);
                fputcsv($out, [$t->kode, optional($t->user)->name, $t->judul, $t->periode, optional($t->jatuh_tempo)->format('Y-m-d'), $total, $t->terbayar_verified, $t->status]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
