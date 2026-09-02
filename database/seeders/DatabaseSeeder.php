<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin LP UMKM',
            'email' => 'admin@umkm-muhammadiyah-diy.test',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        foreach (['Kuliner', 'Fashion', 'Kerajinan', 'Jasa', 'Pertanian', 'Lainnya'] as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }
    }
}
