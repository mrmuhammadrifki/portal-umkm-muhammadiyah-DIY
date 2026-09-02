# Cara Menjalankan Project (How to Run)

Project ini adalah aplikasi **Katalog UMKM** berbasis **Laravel 10** dengan autentikasi **Laravel Breeze** (Blade + Alpine.js + Tailwind CSS) dan **Laravel Sanctum**.

## Requirement

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL (atau database lain yang didukung Laravel)

## 1. Clone & Install Dependency

```bash
composer install
npm install
```

## 2. Konfigurasi Environment

Salin file environment lalu generate application key:

```bash
cp .env.example .env
php artisan key:generate
```

Buka `.env` dan sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_umkm_mu
DB_USERNAME=root
DB_PASSWORD=
```

> Pastikan database `db_umkm_mu` (atau nama lain sesuai `.env`) sudah dibuat di MySQL terlebih dahulu.

## 3. Migrasi Database

```bash
php artisan migrate
```

Migrasi yang akan dijalankan:
- `users` — tabel pengguna
- `password_reset_tokens`
- `failed_jobs`
- `personal_access_tokens` (Sanctum)
- `umkms` — data UMKM
- Penambahan kolom `role` dan `identitas` pada tabel `users`

Jika ingin data awal (seeder), jalankan:

```bash
php artisan db:seed
```

atau sekaligus migrasi + seed:

```bash
php artisan migrate --seed
```

## 4. Jalankan Aplikasi

Buka **dua terminal**, jalankan masing-masing perintah berikut secara bersamaan:

**Terminal 1 — Laravel server:**
```bash
php artisan serve
```

**Terminal 2 — Vite (asset frontend: Tailwind/JS):**
```bash
npm run dev
```

Akses aplikasi di browser: **http://localhost:8000**

## 5. Build Aset untuk Produksi

```bash
npm run build
```

## Struktur Rute Utama

| Rute | Method | Keterangan | Middleware |
|---|---|---|---|
| `/` | GET | Halaman katalog publik | - |
| `/katalog/{id}` | GET | Detail UMKM (publik) | - |
| `/dashboard` | GET | Dashboard manajemen UMKM (list) | `auth` |
| `/umkm` | POST | Tambah UMKM | `auth` |
| `/umkm/{id}/edit` | GET | Form edit UMKM | `auth` |
| `/umkm/{id}` | PUT | Update UMKM | `auth` |
| `/umkm/{id}` | DELETE | Hapus UMKM | `auth` |
| `/user/identitas` | POST | Update identitas user | `auth` |
| `/profile` | GET/PATCH/DELETE | Profil user (bawaan Breeze) | `auth` |

Rute login/register/logout tersedia di `routes/auth.php` (bawaan Laravel Breeze).

## Menjalankan Test

```bash
php artisan test
```
atau
```bash
./vendor/bin/phpunit
```

## Troubleshooting

- **`APP_KEY` kosong / error enkripsi** → jalankan `php artisan key:generate`.
- **Error koneksi database** → cek kembali kredensial di `.env` dan pastikan service MySQL berjalan.
- **Tampilan tidak ada style/CSS** → pastikan `npm run dev` sedang berjalan (mode development) atau jalankan `npm run build` (mode production).
- **Perubahan `.env` tidak terbaca** → jalankan `php artisan config:clear`.
