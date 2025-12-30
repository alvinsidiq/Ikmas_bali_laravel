<?php
namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\BulkGenerateTagihanRequest;
use App\Models\IuranTagihan;
use App\Models\User;
use App\Services\IuranStatusService;
use App\Support\IuranCode;
use Illuminate\Support\Facades\Schema;

class IuranBulkController extends Controller
{
    public function index()
    {
        $anggota = User::role('anggota')->orderBy('name')->get(['id','name']);
        return view('bendahara.iuran.bulk.index', compact('anggota'));
    }

    public function generate(BulkGenerateTagihanRequest $request, IuranStatusService $svc)
    {
        if (!Schema::hasTable('iuran_tagihans')) {
            return back()->with('error','Tabel iuran belum dimigrasi. Jalankan migrasi terlebih dahulu.');
        }
        $d = $request->validated();
        $targets = $d['target'] === 'all' ? User::role('anggota')->pluck('id') : collect($d['user_ids'] ?? []);
        $created = 0; $skipped = 0;
        foreach ($targets as $uid) {
            $exists = IuranTagihan::where('user_id',$uid)->where('periode',$d['periode'])->exists();
            if ($exists && ($d['skip_if_exists'] ?? false)) { $skipped++; continue; }
            $t = IuranTagihan::updateOrCreate([
                'user_id' => $uid,
                'periode' => $d['periode'],
            ], [
                'kode' => IuranCode::inv(),
                'judul' => $d['judul'],
                'nominal' => $d['nominal'],
                'denda' => $d['denda'] ?? 0,
                'diskon' => $d['diskon'] ?? 0,
                'jatuh_tempo' => $d['jatuh_tempo'] ?? null,
                'status' => 'unpaid',
            ]);
            $svc->refreshTagihan($t); $created++;
        }
        return redirect()->route('bendahara.tagihan.index', ['q' => $d['periode']])
            ->with('success',"Bulk generate selesai: dibuat $created, dilewati $skipped. Menampilkan data dengan filter periode.");
    }
}
