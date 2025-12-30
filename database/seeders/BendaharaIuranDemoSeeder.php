<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\IuranTagihan;
use App\Models\IuranPembayaran;
use App\Support\IuranCode;
use App\Services\IuranStatusService;
use Spatie\Permission\Models\Role;

class BendaharaIuranDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist
        foreach (['admin','bendahara','anggota'] as $r) { Role::firstOrCreate(['name'=>$r]); }

        // Create Bendahara user
        $bend = User::firstOrCreate(
            ['email' => 'bendahara@example.com'],
            ['name' => 'Bendahara', 'password' => Hash::make('password123')]
        );
        if (!$bend->hasRole('bendahara')) { $bend->assignRole('bendahara'); }

        // Ensure we have some anggota users to manage
        $anggotaCount = User::role('anggota')->count();
        if ($anggotaCount < 5) {
            for ($i = $anggotaCount + 1; $i <= 5; $i++) {
                $u = User::firstOrCreate(
                    ['email' => "anggota{$i}@example.com"],
                    ['name' => "Anggota {$i}", 'password' => Hash::make('password123')]
                );
                if (!$u->hasRole('anggota')) { $u->assignRole('anggota'); }
            }
        }

        $svc = new IuranStatusService();

        // Seed sample tagihan for recent periods, and some payments (submitted + verified)
        $anggota = User::role('anggota')->take(5)->get();
        foreach ($anggota as $idx => $u) {
            // Last month, current month, next month periods
            $periods = [now()->subMonth()->format('Y-m'), now()->format('Y-m'), now()->addMonth()->format('Y-m')];
            foreach ($periods as $pi => $periode) {
                $judul = 'Iuran Bulanan — '.Str::of($periode)->replace('-', ' / ');
                $jatuh = \Carbon\Carbon::createFromFormat('Y-m', $periode)->endOfMonth();
                $t = IuranTagihan::firstOrNew([
                    'user_id' => $u->id,
                    'periode' => $periode,
                ]);

                if (!$t->exists) {
                    $t->kode = IuranCode::inv();
                }

                $t->judul = (string) $judul;
                $t->nominal = 50000;
                $t->denda = 0;
                $t->diskon = 0;
                $t->jatuh_tempo = $jatuh;
                $t->status = 'unpaid';
                $t->save();

                // For the first two periods, drop in some payments
                if ($pi === 0) {
                    // Verified full payment
                    IuranPembayaran::firstOrCreate([
                        'kode' => IuranCode::pay(),
                    ], [
                        'tagihan_id' => $t->id,
                        'user_id' => $u->id,
                        'amount' => 50000,
                        'paid_at' => now()->subDays(10),
                        'method' => 'transfer',
                        'status' => 'verified',
                        'verified_at' => now()->subDays(9),
                    ]);
                } elseif ($pi === 1) {
                    // One submitted (queue) and one small verified to make it partial
                    IuranPembayaran::create([
                        'kode' => IuranCode::pay(),
                        'tagihan_id' => $t->id,
                        'user_id' => $u->id,
                        'amount' => 10000,
                        'paid_at' => now()->subDays(2),
                        'method' => 'qris',
                        'status' => 'submitted',
                    ]);
                    IuranPembayaran::create([
                        'kode' => IuranCode::pay(),
                        'tagihan_id' => $t->id,
                        'user_id' => $u->id,
                        'amount' => 5000,
                        'paid_at' => now()->subDays(3),
                        'method' => 'cash',
                        'status' => 'verified',
                        'verified_at' => now()->subDays(1),
                    ]);
                }

                // Refresh status cache
                $svc->refreshTagihan($t);
            }
        }
    }
}
