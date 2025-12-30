<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Support\Arr;

class KegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::role('admin')->first() ?? User::first();
        if (!$creator) return;

        $images = [
            'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1487528278747-ba99ed528ebc?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=900&q=80',
        ];

        for ($i=1; $i<=10; $i++) {
            $mulai = now()->addDays(rand(-15, 30))->setTime(rand(8,18), [0,15,30,45][array_rand([0,1,2,3])]);
            $selesai = (clone $mulai)->addHours(rand(1,4));
            $judul = "Kegiatan Contoh {$i}";
            $slug = Kegiatan::uniqueSlug($judul);
            Kegiatan::updateOrCreate(
                ['slug' => $slug],
                [
                    'judul' => $judul,
                    'lokasi' => ['Denpasar','Badung','Gianyar','Tabanan'][array_rand([0,1,2,3])],
                    'deskripsi' => 'Deskripsi singkat kegiatan contoh.',
                    'waktu_mulai' => $mulai,
                    'waktu_selesai' => $selesai,
                    'is_published' => (bool)random_int(0,1),
                    'published_at' => now(),
                    'created_by' => $creator->id,
                    'poster_path' => Arr::random($images),
                ]
            );
        }
    }
}
