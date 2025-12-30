<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Semester Ganjil',
                'tahun_ajaran' => '2024/2025',
                'mulai' => '2024-07-01',
                'selesai' => '2024-12-31',
                'is_active' => true,
            ],
            [
                'nama' => 'Semester Genap',
                'tahun_ajaran' => '2023/2024',
                'mulai' => '2024-01-01',
                'selesai' => '2024-06-30',
                'is_active' => false,
            ],
            [
                'nama' => 'Semester Pendek',
                'tahun_ajaran' => '2023/2024',
                'mulai' => '2024-07-15',
                'selesai' => '2024-08-31',
                'is_active' => false,
            ],
        ];

        foreach ($data as $row) {
            // Ensure only one active semester; deactivate others when seeding an active one
            if (!empty($row['is_active'])) {
                Semester::query()->update(['is_active' => false]);
            }
            Semester::updateOrCreate(
                ['nama' => $row['nama'], 'tahun_ajaran' => $row['tahun_ajaran']],
                $row
            );
        }
    }
}
