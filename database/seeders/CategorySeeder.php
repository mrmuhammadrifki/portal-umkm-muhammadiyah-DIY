<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Mikro',
                'slug'        => 'mikro',
                'min_revenue' => 0,
                'max_revenue' => 25_000_000,
                'description' => 'Rata-rata pendapatan di bawah Rp 25 juta per bulan (< Rp 300 juta/tahun).',
            ],
            [
                'name'        => 'Kecil',
                'slug'        => 'kecil',
                'min_revenue' => 25_000_000,
                'max_revenue' => 208_000_000,
                'description' => 'Rata-rata pendapatan Rp 25 juta s/d Rp 208 juta per bulan (Rp 300 juta – Rp 2,5 miliar/tahun).',
            ],
            [
                'name'        => 'Menengah',
                'slug'        => 'menengah',
                'min_revenue' => 208_000_000,
                'max_revenue' => null,
                'description' => 'Rata-rata pendapatan di atas Rp 208 juta per bulan (> Rp 2,5 miliar/tahun).',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name'        => $cat['name'],
                    'min_revenue' => $cat['min_revenue'],
                    'max_revenue' => $cat['max_revenue'],
                    'description' => $cat['description'],
                ]
            );
        }
    }
}
