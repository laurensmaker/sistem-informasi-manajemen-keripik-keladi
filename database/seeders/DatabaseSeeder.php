<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         User::create([
            'nama' => 'Admin Owner',
            'username' => 'owner',
            'password' => bcrypt('owner123'),
            'role' => 'owner',
            'no_hp' => '081234567890',
        ]);

        // Data Penjual (2 data)
        User::create([
            'nama' => 'Ahmad Penjual',
            'username' => 'penjual1',
            'password' => bcrypt('penjual123'),
            'role' => 'penjual',
            'no_hp' => '081234567891',
        ]);

        User::create([
            'nama' => 'Siti Penjual',
            'username' => 'penjual2',
            'password' => bcrypt('penjual123'),
            'role' => 'penjual',
            'no_hp' => '081234567892',
        ]);

        // Tambahan: Owner kedua (opsional)
        User::create([
            'nama' => 'Budi Owner',
            'username' => 'owner2',
            'password' => bcrypt('owner123'),
            'role' => 'owner',
            'no_hp' => '081234567893',
        ]);
    }
}
