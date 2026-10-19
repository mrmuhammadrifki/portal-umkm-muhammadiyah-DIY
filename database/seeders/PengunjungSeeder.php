<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun pengunjung (pembeli) contoh untuk mengetes pemesanan via WhatsApp.
 * Semua akun berpassword "password". Aman dijalankan ulang.
 */
class PengunjungSeeder extends Seeder
{
    public function run(): void
    {
        $visitors = [
            ['Pengunjung Contoh', 'pengunjung@umkm.test', '081200000001'],
            ['Andi Wicaksono', 'andi.wicaksono@pengunjung.test', '081200000002'],
            ['Sari Puspitasari', 'sari.puspita@pengunjung.test', '081200000003'],
            ['Muhammad Rizky', 'rizky.m@pengunjung.test', '081200000004'],
        ];

        foreach ($visitors as [$name, $email, $phone]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'      => $name,
                    'password'  => Hash::make('password'),
                    'role'      => 'pengunjung',
                    'phone'     => $phone,
                    'is_active' => true,
                ]
            );
        }
    }
}
