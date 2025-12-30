<?php
namespace App\Services;

use App\Models\IuranTagihan;
use App\Models\IuranPembayaran;

class IuranStatusService
{
    /** Hitung ulang terbayar_verified & status tagihan */
    public function refreshTagihan(IuranTagihan $t): IuranTagihan
    {
        $sum = (int) IuranPembayaran::where('tagihan_id',$t->id)->where('status','verified')->sum('amount');
        $t->terbayar_verified = $sum;
        $total = max(0, ($t->nominal + $t->denda) - $t->diskon);
        if ($sum >= $total && $total > 0) {
            $t->status = 'paid';
            $last = IuranPembayaran::where('tagihan_id',$t->id)->where('status','verified')->latest('paid_at')->first();
            $t->paid_at = optional($last)->paid_at ?? now();
        } elseif ($sum > 0) {
            $t->status = 'partial';
            $t->paid_at = null;
        } else {
            $t->status = 'unpaid';
            $t->paid_at = null;
        }
        if ($t->status !== 'paid' && $t->jatuh_tempo && now()->isAfter($t->jatuh_tempo)) {
            $t->status = 'overdue';
        }
        $t->save();
        return $t;
    }
}

