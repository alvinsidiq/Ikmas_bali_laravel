<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Arsip;
use App\Models\User;
use Illuminate\Support\Arr;

class ArsipSeeder extends Seeder
{
    public function run(): void
    {
        $uploader = User::role('admin')->first() ?? User::first();
        if (!$uploader) return;

        $kats = ['SK','Surat Masuk','Surat Keluar','Keuangan'];
        $thumbnails = [
            'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1472289065668-ce650ac443d2?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1484417894907-623942c8ee29?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=800&q=80',
        ];
        for ($i=1; $i<=20; $i++) {
            $judul = "Dokumen Arsip {$i}";
            $slug = Arsip::uniqueSlug($judul);
            Arsip::updateOrCreate(
                ['slug' => $slug],
                [
                    'judul' => $judul,
                    'kategori' => $kats[array_rand($kats)],
                    'tahun' => rand(2018, 2025),
                    'nomor_dokumen' => 'DOC-'.str_pad($i,4,'0',STR_PAD_LEFT),
                    'tags' => 'contoh,arsip',
                    'ringkasan' => 'Ringkasan singkat dokumen. (tanpa file)',
                    'thumbnail_url' => Arr::random($thumbnails),
                    'is_published' => (bool)random_int(0,1),
                    'published_at' => now()->subDays(rand(0,30)),
                    'uploaded_by' => $uploader->id,
                ]
            );
        }
    }
}
