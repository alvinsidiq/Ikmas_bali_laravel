<?php
namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\IuranPembayaran;
use App\Models\IuranTagihan;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index() {
        $stats = [
            'tagihan_total' => 0,
            'tagihan_unpaid' => 0,
            'tagihan_overdue' => 0,
            'tagihan_outstanding' => 0,
            'pembayaran_total' => 0,
            'pembayaran_verified_amount' => 0,
            'pembayaran_pending' => 0,
        ];

        if (Schema::hasTable('iuran_tagihans')) {
            $tagihan = IuranTagihan::select(['nominal','denda','diskon','terbayar_verified','status','jatuh_tempo'])->get();
            $stats['tagihan_total'] = $tagihan->count();
            $stats['tagihan_unpaid'] = $tagihan->where('status','unpaid')->count();
            $stats['tagihan_overdue'] = $tagihan->filter(function($t){
                return $t->status === 'overdue' || ($t->status !== 'paid' && $t->jatuh_tempo && $t->jatuh_tempo->isPast());
            })->count();
            $stats['tagihan_outstanding'] = $tagihan->sum(function($t){
                $total = (int)$t->nominal + (int)$t->denda - (int)$t->diskon;
                return max(0, $total - (int)$t->terbayar_verified);
            });
        }

        if (Schema::hasTable('iuran_pembayarans')) {
            $payments = IuranPembayaran::select(['amount','status'])->get();
            $stats['pembayaran_total'] = $payments->count();
            $stats['pembayaran_verified_amount'] = $payments->where('status','verified')->sum('amount');
            $stats['pembayaran_pending'] = $payments->whereIn('status',['submitted','pending_gateway'])->count();
        }

        return view('bendahara.dashboard', compact('stats'));
    }
}
