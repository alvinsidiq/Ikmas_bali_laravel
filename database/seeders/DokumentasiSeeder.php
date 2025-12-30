<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DokumentasiAlbum;
use App\Models\DokumentasiMedia;
use App\Models\User;
use Illuminate\Support\Arr;

class DokumentasiSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::role('admin')->first() ?? User::first();
        if (!$creator) return;

        $covers = [
            'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1515165562835-c4c1bfa1f60b?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1000&q=80',
        ];
        $photos = [
            'https://images.unsplash.com/photo-1454165205744-3b78555e5572?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1487528278747-ba99ed528ebc?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1502764613149-7f1d229e230f?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1545239351-1141bd82e8a6?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1485217988980-11786ced9454?auto=format&fit=crop&w=800&q=80',
        ];

        for ($i=1; $i<=6; $i++) {
            $judul = "Album Kegiatan {$i}";
            $slug = DokumentasiAlbum::uniqueSlug($judul);
            $a = DokumentasiAlbum::updateOrCreate(
                ['slug' => $slug],
                [
                    'judul' => $judul,
                    'tanggal_kegiatan' => now()->subDays(rand(1,120))->toDateString(),
                    'lokasi' => ['Denpasar','Badung','Gianyar','Tabanan'][array_rand([0,1,2,3])],
                    'deskripsi' => 'Album dokumentasi contoh.',
                    'tags' => 'contoh,album',
                    'is_published' => (bool)random_int(0,1),
                    'published_at' => now()->subDays(rand(0,10)),
                    'created_by' => $creator->id,
                    'cover_path' => Arr::random($covers),
                ]
            );

            // Media dummy tanpa file fisik; untuk demo listing saja
            $count = rand(3,8);
            for ($j=1; $j<=$count; $j++) {
                $mediaPath = Arr::random($photos);
                DokumentasiMedia::updateOrCreate(
                    ['album_id' => $a->id, 'sort_order' => $j],
                    [
                        'media_path' => $mediaPath,
                        'mime' => 'image/jpeg',
                        'size' => 0,
                        'caption' => "Foto {$j}",
                        'uploaded_by' => $creator->id,
                        'is_cover' => $j===1,
                    ]
                );
            }
            $a->media_count = $count; $a->save();
        }
    }
}
