<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengumuman;
use App\Models\User;

class PengumumanReadSeeder extends Seeder
{
    public function run(): void
    {
        $published = Pengumuman::where('is_published',true)->take(30)->pluck('id');
        $anggota = User::role('anggota')->take(20)->get();
        foreach ($anggota as $u) {
            if ($published->isEmpty()) continue;
            $subset = $published->random(min($published->count(), rand(5,15)));
            $attach = [];
            foreach ($subset as $pid) { $attach[$pid] = ['read_at' => now()->subDays(rand(0,7))]; }
            $u->pengumumanTerbaca()->syncWithoutDetaching($attach);
        }
    }
}

