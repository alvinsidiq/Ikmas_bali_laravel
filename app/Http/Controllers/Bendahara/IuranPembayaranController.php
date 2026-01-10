<?php
namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\VerifyOrRejectPembayaranRequest;
use App\Models\IuranPembayaran;
use App\Services\IuranStatusService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class IuranPembayaranController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('iuran_pembayarans')) {
            $st = $request->get('status','submitted');
            $items = new LengthAwarePaginator([], 0, 20, 1, [ 'path' => $request->url(), 'query' => $request->query() ]);
            return view('bendahara.iuran.pembayaran.index', compact('items','st'));
        }
        $st = $request->get('status','submitted');
        $items = IuranPembayaran::with(['user','tagihan'])
            ->when($st, fn($q)=>$q->where('status',$st))
            ->latest()->paginate(20)->withQueryString();
        return view('bendahara.iuran.pembayaran.index', compact('items','st'));
    }

    public function show(IuranPembayaran $pembayaran)
    { $pembayaran->load(['user','tagihan']); return view('bendahara.iuran.pembayaran.show', compact('pembayaran')); }

    public function buktiShow(IuranPembayaran $pembayaran)
    {
        abort_unless($pembayaran->bukti_path && Storage::disk('public')->exists($pembayaran->bukti_path), 404);
        return Storage::disk('public')->response(
            $pembayaran->bukti_path,
            basename($pembayaran->bukti_path),
            [ 'Content-Type' => $pembayaran->bukti_mime ?? 'application/octet-stream' ]
        );
    }

    public function verify(VerifyOrRejectPembayaranRequest $request, IuranPembayaran $pembayaran, IuranStatusService $svc)
    {
        if ($pembayaran->status === 'verified') return back()->with('info','Sudah diverifikasi.');
        $pembayaran->status = 'verified';
        $pembayaran->verified_by = auth()->id();
        $pembayaran->verified_at = now();
        $pembayaran->rejection_reason = null;
        $pembayaran->save();
        $svc->refreshTagihan($pembayaran->tagihan);
        return back()->with('success','Pembayaran diverifikasi.');
    }

    public function reject(VerifyOrRejectPembayaranRequest $request, IuranPembayaran $pembayaran, IuranStatusService $svc)
    {
        if ($pembayaran->status === 'verified') return back()->with('error','Tidak dapat menolak pembayaran yang sudah diverifikasi.');
        $pembayaran->status = 'rejected';
        $pembayaran->rejection_reason = $request->validated()['rejection_reason'] ?? 'Tidak valid';
        $pembayaran->verified_by = auth()->id();
        $pembayaran->verified_at = now();
        $pembayaran->save();
        $svc->refreshTagihan($pembayaran->tagihan);
        return back()->with('success','Pembayaran ditolak.');
    }

    public function destroy(IuranPembayaran $pembayaran, IuranStatusService $svc)
    {
        if ($pembayaran->bukti_path) Storage::disk('public')->delete($pembayaran->bukti_path);
        $tagihan = $pembayaran->tagihan; $pembayaran->delete();
        $svc->refreshTagihan($tagihan);
        return back()->with('success','Pembayaran dihapus.');
    }
}
