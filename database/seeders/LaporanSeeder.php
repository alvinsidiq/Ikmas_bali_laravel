<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laporan;
use App\Models\User;
use App\Support\LaporanCode;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        $u = User::role('anggota')->first() ?? User::first();
        if (!$u) return;
        $kats = ['Laporan Kegiatan','Laporan Pengumuman'];
        for ($i=1; $i<=8; $i++) {
            Laporan::create([
                'kode' => LaporanCode::generate(),
                'reporter_id' => $u->id,
                'judul' => 'Laporan Contoh '.$i,
                'kategori' => $kats[array_rand($kats)],
                'deskripsi' => 'Ini adalah contoh deskripsi laporan '.$i,
                'status' => ['open','in_progress','resolved'][array_rand([0,1,2])],
                'attachments_count' => 0,
                'comments_count' => 0,
            ]);
        }
    }
}
