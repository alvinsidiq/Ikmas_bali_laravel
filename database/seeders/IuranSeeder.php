<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IuranTagihan;
use App\Models\IuranPembayaran;
use App\Models\User;
use App\Support\IuranCode;

class IuranSeeder extends Seeder
{
    public function run(): void
    {
        $u = User::role('anggota')->first() ?? User::first();
        if (!$u) return;

        // 3 tagihan, satu lunas, satu partial, satu unpaid
        $t1 = IuranTagihan::firstOrNew([
            'user_id' => $u->id,
            'periode' => '2025-09',
        ]);
        if (!$t1->exists) {
            $t1->kode = IuranCode::inv();
        }
        $t1->judul = 'Iuran Bulanan — Sep 2025';
        $t1->nominal = 50000;
        $t1->denda = 0;
        $t1->diskon = 0;
        $t1->jatuh_tempo = now()->subMonths(1)->endOfMonth();
        $t1->status = 'paid';
        $t1->paid_at = now()->subDays(20);
        $t1->terbayar_verified = 50000;
        $t1->save();

        $p1 = IuranPembayaran::firstOrNew([
            'tagihan_id' => $t1->id,
        ]);
        if (!$p1->exists) {
            $p1->kode = IuranCode::pay();
        }
        $p1->user_id = $u->id;
        $p1->amount = 50000;
        $p1->paid_at = now()->subDays(21);
        $p1->method = 'transfer';
        $p1->status = 'verified';
        $p1->verified_at = now()->subDays(20);
        $p1->save();

        $t2 = IuranTagihan::firstOrNew([
            'user_id' => $u->id,
            'periode' => '2025-10',
        ]);
        if (!$t2->exists) {
            $t2->kode = IuranCode::inv();
        }
        $t2->judul = 'Iuran Bulanan — Okt 2025';
        $t2->nominal = 50000;
        $t2->denda = 0;
        $t2->diskon = 0;
        $t2->jatuh_tempo = now()->endOfMonth();
        $t2->status = 'partial';
        $t2->terbayar_verified = 20000;
        $t2->save();

        $p2 = IuranPembayaran::firstOrNew([
            'tagihan_id' => $t2->id,
        ]);
        if (!$p2->exists) {
            $p2->kode = IuranCode::pay();
        }
        $p2->user_id = $u->id;
        $p2->amount = 20000;
        $p2->paid_at = now()->subDays(2);
        $p2->method = 'transfer';
        $p2->status = 'verified';
        $p2->verified_at = now()->subDays(1);
        $p2->save();

        $t3 = IuranTagihan::firstOrNew([
            'user_id' => $u->id,
            'periode' => '2025-11',
        ]);
        if (!$t3->exists) {
            $t3->kode = IuranCode::inv();
        }
        $t3->judul = 'Iuran Bulanan — Nov 2025';
        $t3->nominal = 50000;
        $t3->denda = 0;
        $t3->diskon = 0;
        $t3->jatuh_tempo = now()->addMonth()->endOfMonth();
        $t3->status = 'unpaid';
        $t3->terbayar_verified = 0;
        $t3->paid_at = null;
        $t3->save();
    }
}
