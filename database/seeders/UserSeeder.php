<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password123')]
        );
        $admin->assignRole('admin');

        // Bendahara
        $bend = User::firstOrCreate(
            ['email' => 'bendahara@example.com'],
            ['name' => 'Bendahara', 'password' => Hash::make('password123')]
        );
        $bend->assignRole('bendahara');

        // Anggota
        $angg = User::firstOrCreate(
            ['email' => 'anggota@example.com'],
            ['name' => 'Anggota', 'password' => Hash::make('password123')]
        );
        $angg->assignRole('anggota');
    }
}
