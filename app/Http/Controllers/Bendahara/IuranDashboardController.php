<?php
namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\IuranTagihan;
use App\Models\IuranPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IuranDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('iuran_tagihans') || !Schema::hasTable('iuran_pembayarans')) {
            $stats = [
                'total_tagihan' => 0,
                'total_terbayar' => 0,
                'sisa' => 0,
                'unpaid' => 0,
                'partial' => 0,
                'overdue' => 0,
                'paid' => 0,
            ];
            $submitted = collect();
            return view('bendahara.iuran.dashboard', compact('stats','submitted'));
        }

        $totalTagihan = (int) IuranTagihan::sum(DB::raw('(nominal + denda - diskon)'));
        $totalTerbayar = (int) IuranTagihan::sum('terbayar_verified');
        $sisa = max(0, $totalTagihan - $totalTerbayar);
        $submitted = IuranPembayaran::where('status','submitted')->latest()->take(10)->get();
        $stats = [
            'total_tagihan' => $totalTagihan,
            'total_terbayar' => $totalTerbayar,
            'sisa' => $sisa,
            'unpaid' => IuranTagihan::where('status','unpaid')->count(),
            'partial' => IuranTagihan::where('status','partial')->count(),
            'overdue' => IuranTagihan::where('status','overdue')->count(),
            'paid' => IuranTagihan::where('status','paid')->count(),
        ];
        return view('bendahara.iuran.dashboard', compact('stats','submitted'));
    }
}
