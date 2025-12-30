<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Support\Arr;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first() ?? User::first();
        if (!$admin) return;

        $kats = ['Umum','Teknis','Organisasi'];
        $banners = [
            'https://images.unsplash.com/photo-1454165205744-3b78555e5572?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1485217988980-11786ced9454?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1502764613149-7f1d229e230f?auto=format&fit=crop&w=1000&q=80',
        ];
        for ($i=1; $i<=8; $i++) {
            $judul = "Topik Diskusi {$i}";
            $slug = ForumTopic::uniqueSlug($judul);
            $t = ForumTopic::updateOrCreate(
                ['slug' => $slug],
                [
                    'judul' => $judul,
                    'kategori' => $kats[array_rand($kats)],
                    'body' => 'Pembuka topik diskusi.',
                    'author_id' => $admin->id,
                    'is_open' => (bool)random_int(0,1),
                    'is_pinned' => (bool)random_int(0,1),
                    'last_post_at' => now()->subDays(rand(0,5)),
                    'banner_url' => Arr::random($banners),
                ]
            );
            $count = rand(0,5);
            for ($j=1; $j<=$count; $j++) {
                ForumPost::firstOrCreate(
                    ['topic_id' => $t->id, 'user_id' => $admin->id, 'content' => "Balasan {$j} untuk topik {$i}"],
                    ['is_solution' => false]
                );
            }
            $t->posts_count = $count; $t->save();
        }
    }
}
