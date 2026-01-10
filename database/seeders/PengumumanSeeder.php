<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Support\Arr;

class PengumumanSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::role('admin')->first() ?? User::first();
        if (!$author) return;

        $kats = array_keys(\App\Models\Pengumuman::CATEGORY_OPTIONS);
        $covers = [
            'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1529339950860-19e12c0a5a52?auto=format&fit=crop&w=1000&q=80',
        ];
        for ($i=1; $i<=15; $i++) {
            $judul = "Pengumuman Penting {$i}";
            $slug = \App\Models\Pengumuman::uniqueSlug($judul);
            Pengumuman::updateOrCreate(
                ['slug' => $slug],
                [
                    'judul' => $judul,
                    'kategori' => $kats[array_rand($kats)],
                    'isi' => 'Isi pengumuman contoh. Silakan sesuaikan.',
                    'is_published' => (bool)random_int(0,1),
                    'published_at' => now()->subDays(rand(0,10)),
                    'is_pinned' => (bool)random_int(0,1),
                    'author_id' => $author->id,
                    'cover_path' => Arr::random($covers),
                ]
            );
        }
    }
}
