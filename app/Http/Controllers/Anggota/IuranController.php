<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Requests\Anggota\StorePembayaranRequest;
use App\Models\IuranTagihan;
use App\Models\IuranPembayaran;
use App\Services\IuranStatusService;
use App\Services\XenditService;
use App\Support\IuranCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Xendit\Exceptions\ApiException;

class IuranController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        if (!Schema::hasTable('iuran_tagihans') || !Schema::hasTable('iuran_pembayarans')) {
            $summary = [ 'outstanding_total' => 0, 'unpaid_count' => 0, 'next_due' => null ];
            $unpaid = collect(); $paid = collect(); $payments = collect();
            return view('anggota.iuran.dashboard', compact('summary','unpaid','paid','payments'));
        }
        $unpaid = IuranTagihan::where('user_id',$user->id)->whereIn('status',['unpaid','partial','overdue'])->get();
        $paid = IuranTagihan::where('user_id',$user->id)->where('status','paid')->latest('paid_at')->take(6)->get();
        $payments = IuranPembayaran::where('user_id',$user->id)->latest()->take(6)->get();

        $summary = [
            'outstanding_total' => $unpaid->sum->sisa_bayar,
            'unpaid_count' => $unpaid->count(),
            'next_due' => optional($unpaid->sortBy('jatuh_tempo')->first())->jatuh_tempo,
        ];

        return view('anggota.iuran.dashboard', compact('summary','unpaid','paid','payments'));
    }

    public function tagihanIndex(Request $request)
    {
        if (!Schema::hasTable('iuran_tagihans')) {
            $items = (new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12, 1, [ 'path' => $request->url(), 'query' => $request->query() ]));
            $q = $request->get('q'); $st = $request->get('status'); $yr = $request->get('year'); $mo = $request->get('month');
            $summary = ['outstanding_total'=>0,'unpaid_count'=>0,'next_due'=>null];
            $unpaidFirst = null;
            return view('anggota.iuran.tagihan.index', compact('items','q','st','yr','mo','summary','unpaidFirst'));
        }
        $user = Auth::user();
        $q = trim((string)$request->get('q'));
        $st = $request->get('status'); // unpaid|partial|paid|overdue
        $yr = $request->get('year');
        $mo = $request->get('month');

        $items = IuranTagihan::where('user_id',$user->id)
            ->when($q, fn($qr)=>$qr->where(function($x) use ($q){
                $x->where('judul','like',"%$q%")
                  ->orWhere('kode','like',"%$q%")
                  ->orWhere('periode','like',"%$q%");
            }))
            ->when($st, fn($qr)=>$qr->where('status',$st))
            ->when($yr, fn($qr)=>$qr->whereYear('jatuh_tempo',$yr))
            ->when($mo, fn($qr)=>$qr->whereMonth('jatuh_tempo',$mo))
            ->latest('jatuh_tempo')->latest('id')
            ->paginate(12)->withQueryString();

        $unpaid = IuranTagihan::where('user_id',$user->id)->whereIn('status',['unpaid','partial','overdue'])->orderBy('jatuh_tempo')->get();
        $summary = [
            'outstanding_total' => $unpaid->sum->sisa_bayar,
            'unpaid_count' => $unpaid->count(),
            'next_due' => optional($unpaid->first())->jatuh_tempo,
        ];
        $unpaidFirst = $unpaid->first();

        return view('anggota.iuran.tagihan.index', compact('items','q','st','yr','mo','summary','unpaidFirst'));
    }

    public function tagihanShow(IuranTagihan $tagihan)
    {
        abort_unless($tagihan->user_id === Auth::id(), 403);
        $tagihan->load(['payments' => fn($q)=>$q->whereIn('status',['verified','rejected'])->latest('id')]);
        $bank = config('iuran');
        return view('anggota.iuran.tagihan.show', compact('tagihan','bank'));
    }

    /** Handle GET hit on bayar URL: jika ada invoice gateway, langsung buka halaman Xendit; jika tidak, kembali ke detail tagihan */
    public function bayarForm(IuranTagihan $tagihan)
    {
        abort_unless($tagihan->user_id === Auth::id(), 403);
        return redirect()
            ->route('anggota.iuran.tagihan.show', $tagihan)
            ->with('info','Pilih metode pembayaran di halaman tagihan, lalu kirim untuk memproses.');
    }

    public function bayar(StorePembayaranRequest $request, IuranTagihan $tagihan, IuranStatusService $svc, XenditService $xendit)
    {
        abort_unless($tagihan->user_id === Auth::id(), 403);
        if ($tagihan->status === 'paid') return back()->with('info','Tagihan sudah lunas.');

        $data = $request->validated();
        $channel = $data['channel'];
        $method = $channel === 'gateway'
            ? ($data['gateway_method'] ?? 'transfer')
            : ($data['manual_method'] ?? 'transfer');

        // Jika sudah ada pembayaran gateway yang masih pending, arahkan ulang ke invoice yang sama agar tidak membuat duplikasi
        if ($channel === 'gateway') {
            // buang invoice gateway lama/palsu agar selalu pakai link baru yang valid
            $tagihan->payments()
                ->where('channel','gateway')
                ->where(function($q){
                    $q->where('status','pending_gateway')
                      ->orWhere('xendit_transaction_id','like','sandbox-%');
                })
                ->delete();
        }

        $pay = new IuranPembayaran();
        $pay->fill([
            'kode' => IuranCode::pay(),
            'tagihan_id' => $tagihan->id,
            'user_id' => Auth::id(),
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'] ?? now(),
            'method' => $method,
            'channel' => $channel,
        ]);
        if ($channel === 'manual' && !empty($data['bukti'])) {
            $path = $data['bukti']->store('iuran/bukti','public');
            $pay->bukti_path = $path;
            $pay->bukti_mime = $data['bukti']->getClientMimeType();
            $pay->bukti_size = $data['bukti']->getSize();
        }
        if ($channel === 'manual') {
            $pay->status = 'submitted';
            $pay->save();
            return back()->with('success','Pembayaran manual dikirim, menunggu verifikasi bendahara.');
        }

        $pay->status = 'pending_gateway';
        $pay->status_pembayaran = 'PENDING';
        $pay->gateway = 'xendit';
        $pay->save();

        try {
            $invoice = $xendit->createInvoice(
                $pay,
                $method,
                route('anggota.iuran.gateway.success', $pay),
                route('anggota.iuran.gateway.cancel', $pay)
            );
        } catch (\Throwable $e) {
            report($e);
            // hapus draft payment jika gagal membuat invoice
            $pay->delete();
            return back()->withErrors(['payment' => 'Gagal membuat invoice Xendit: '.$e->getMessage()]);
        }

        $pay->gateway_reference = $invoice['id'] ?? null;
        $pay->xendit_transaction_id = $invoice['id'] ?? null;
        $pay->invoice_url = $invoice['invoice_url'] ?? $xendit->checkoutUrl($invoice);
        $pay->gateway_receipt_url = $xendit->checkoutUrl($invoice) ?? $pay->invoice_url;
        $pay->gateway_payload = $invoice;
        $pay->status_pembayaran = $invoice['status'] ?? 'PENDING';
        $pay->save();

        $url = $pay->gateway_receipt_url ?? $pay->invoice_url;
        if (!$url) {
            $pay->delete();
            return back()->withErrors(['payment' => 'Xendit tidak mengembalikan tautan invoice. Silakan coba lagi.']);
        }
        \Log::info('Redirecting to Xendit invoice', [
            'payment_id' => $pay->id,
            'user_id' => Auth::id(),
            'url' => $url,
            'gateway_reference' => $pay->gateway_reference,
            'status' => $pay->status,
            'status_pembayaran' => $pay->status_pembayaran,
        ]);
        return redirect()->away($url);
    }

    public function buktiDownload(IuranPembayaran $pembayaran)
    {
        abort_unless($pembayaran->user_id === Auth::id(), 403);
        abort_unless($pembayaran->bukti_path && Storage::disk('public')->exists($pembayaran->bukti_path), 404);
        return Storage::disk('public')->download($pembayaran->bukti_path, basename($pembayaran->bukti_path), [ 'Content-Type' => $pembayaran->bukti_mime ?? 'application/octet-stream' ]);
    }

    public function sandboxCheckout(IuranPembayaran $pembayaran)
    {
        abort_unless($pembayaran->user_id === Auth::id(), 403);
        abort_unless($pembayaran->channel === 'gateway', 403);
        return view('anggota.iuran.pembayaran.sandbox', compact('pembayaran'));
    }

    public function hapusPembayaran(IuranPembayaran $pembayaran)
    {
        abort_unless($pembayaran->user_id === Auth::id(), 403);
        if (!in_array($pembayaran->status, ['submitted','pending_gateway'])) {
            return back()->with('error','Tidak dapat menghapus karena sudah diproses.');
        }
        if ($pembayaran->bukti_path) Storage::disk('public')->delete($pembayaran->bukti_path);
        $pembayaran->delete();
        return back()->with('success','Pembayaran dibatalkan/dihapus.');
    }

    public function receipt(IuranPembayaran $pembayaran)
    {
        abort_unless($pembayaran->user_id === Auth::id(), 403);
        abort_unless($pembayaran->status === 'verified', 403);
        $pembayaran->load(['tagihan','user']);
        return view('anggota.iuran.pembayaran.receipt', compact('pembayaran'));
    }

    public function gatewaySuccess(Request $request, IuranPembayaran $pembayaran, IuranStatusService $svc, XenditService $xendit)
    {
        abort_unless($pembayaran->user_id === Auth::id(), 403);
        abort_unless($pembayaran->channel === 'gateway', 403);

        if ($pembayaran->status !== 'verified') {
            if ($pembayaran->gateway_reference && !str_starts_with($pembayaran->gateway_reference, 'sandbox-')) {
                $invoice = $xendit->getInvoice($pembayaran->gateway_reference);
                if ($invoice) {
                    $pembayaran->gateway_payload = $invoice;
                    $pembayaran->gateway_receipt_url = $xendit->checkoutUrl($invoice) ?? $pembayaran->gateway_receipt_url;
                    $pembayaran->status_pembayaran = $invoice['status'] ?? $pembayaran->status_pembayaran;
                }
            }
            // Paksa dianggap paid setelah kembali ke success callback
            $pembayaran->status = 'verified';
            $pembayaran->paid_at = $pembayaran->paid_at ?: now();
            $pembayaran->verified_at = now();
            $pembayaran->status_pembayaran = 'PAID';
            $pembayaran->save();
            $svc->refreshTagihan($pembayaran->tagihan);
        }

        return redirect()->route('anggota.iuran.dashboard')
            ->with('success','Pembayaran via Xendit berhasil atau sedang diproses.');
    }

    public function gatewayCancel(IuranPembayaran $pembayaran)
    {
        abort_unless($pembayaran->user_id === Auth::id(), 403);
        return redirect()->route('anggota.iuran.tagihan.show', $pembayaran->tagihan)
            ->with('error','Pembayaran via Xendit dibatalkan atau gagal. Silakan coba lagi.');
    }
}
