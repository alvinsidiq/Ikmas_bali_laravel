<?php
namespace App\Services;

use App\Models\IuranTagihan;
use App\Models\IuranPembayaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IuranReportService
{
    /** Parse tanggal dari request (from/to) dengan default: bulan ini */
    public function range(?string $from, ?string $to): array
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $end   = $to   ? Carbon::parse($to)->endOfDay()   : now()->endOfMonth();
        return [$start, $end];
    }

    /** Hitung total tagihan berdasarkan jatuh_tempo dalam range */
    public function totalTagihan(Carbon $start, Carbon $end, ?int $userId = null): int
    {
        return (int) IuranTagihan::when($userId, fn($q)=>$q->where('user_id',$userId))
            ->whereBetween('jatuh_tempo', [$start, $end])
            ->select(DB::raw('COALESCE(SUM(nominal + denda - diskon),0) as total'))
            ->value('total');
    }

    /** Total pembayaran terverifikasi berdasar paid_at dalam range */
    public function totalPembayaranVerified(Carbon $start, Carbon $end, ?int $userId = null, ?string $method = null): int
    {
        return (int) IuranPembayaran::where('status','verified')
            ->when($userId, fn($q)=>$q->where('user_id',$userId))
            ->when($method, fn($q)=>$q->where('method',$method))
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');
    }

    /** Outstanding (piutang) per tagihan pada tanggal asOf: total - pembayaran verified hingga asOf */
    public function outstandingAsOf(Carbon $asOf, ?int $userId = null): int
    {
        $tagihans = IuranTagihan::when($userId, fn($q)=>$q->where('user_id',$userId))->get();
        $sum = 0;
        foreach ($tagihans as $t) {
            $total = max(0, ($t->nominal + $t->denda) - $t->diskon);
            $paid = (int) IuranPembayaran::where('tagihan_id',$t->id)
                ->where('status','verified')->where('paid_at','<=',$asOf)->sum('amount');
            $sum += max(0, $total - $paid);
        }
        return $sum;
    }

    /** Aging buckets per-anggota pada asOf */
    public function agingByUser(Carbon $asOf): array
    {
        $rows = IuranTagihan::with('user')
            ->whereIn('status',['unpaid','partial','overdue'])
            ->get();
        $out = [];
        foreach ($rows as $t) {
            $total = max(0, ($t->nominal + $t->denda) - $t->diskon);
            $paid = (int) $t->payments()->where('status','verified')->where('paid_at','<=',$asOf)->sum('amount');
            $sisa = max(0, $total - $paid);
            if ($sisa <= 0) continue;
            $days = $t->jatuh_tempo ? $t->jatuh_tempo->diffInDays($asOf, false) : 0;
            $bucket = '0-30';
            if ($days > 90) $bucket = '90+'; elseif ($days > 60) $bucket = '61-90'; elseif ($days > 30) $bucket = '31-60';
            $uid = $t->user_id;
            $name = optional($t->user)->name ?? '—';
            $out[$uid]['name'] = $name;
            $out[$uid]['total'] = ($out[$uid]['total'] ?? 0) + $sisa;
            $out[$uid]['buckets'][$bucket] = ($out[$uid]['buckets'][$bucket] ?? 0) + $sisa;
        }
        uasort($out, fn($a,$b)=>($b['total'] <=> $a['total']));
        return $out;
    }
}

