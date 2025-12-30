<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IuranTagihan;
use App\Services\IuranStatusService;
use Illuminate\Support\Facades\Schema;

class IuranMarkOverdue extends Command
{
    protected $signature = 'iuran:overdue';
    protected $description = 'Tandai tagihan iuran menjadi overdue jika melewati jatuh tempo dan belum lunas.';

    public function handle(IuranStatusService $svc): int
    {
        if (!Schema::hasTable('iuran_tagihans')) {
            $this->info('Tabel iuran_tagihans belum ada. Lewati.');
            return self::SUCCESS;
        }
        $rows = IuranTagihan::whereDate('jatuh_tempo','<', now()->toDateString())
                ->whereIn('status',['unpaid','partial','overdue'])
                ->get();
        foreach ($rows as $t) { $svc->refreshTagihan($t); }
        $this->info('Overdue refresh: '.$rows->count().' tagihan.');
        return self::SUCCESS;
    }
}
