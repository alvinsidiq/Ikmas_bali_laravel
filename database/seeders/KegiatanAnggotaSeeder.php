<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Support\Str;

class KegiatanAnggotaSeeder extends Seeder
{
    public function run(): void
    {
        $kegs = Kegiatan::published()->take(5)->get();
        $anggota = User::role('anggota')->take(10)->get();
        foreach ($kegs as $k) {
            foreach ($anggota->random(min(max(2, $anggota->count() ? 2 : 0), min(6, $anggota->count()))) as $u) {
                $u->kegiatanDiikuti()->syncWithoutDetaching([
                    $k->id => [
                        'status' => 'registered',
                        'kode' => strtoupper(Str::random(8)),
                        'registered_at' => now()->subDays(rand(0,5)),
                    ]
                ]);
            }
        }
    }
}

