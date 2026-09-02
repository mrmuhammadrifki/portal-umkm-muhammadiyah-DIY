# Sistem Informasi UMKM Muhammadiyah DIY

Platform web untuk memusatkan data UMKM binaan LP UMKM Muhammadiyah Daerah Istimewa Yogyakarta, mengelola profil usaha dan produk, serta menyajikannya sebagai katalog digital publik. Bukan marketplace — tidak ada transaksi di dalam sistem, kontak lanjutan diarahkan ke WhatsApp masing-masing UMKM.

Dikembangkan sebagai bagian dari skripsi, dengan tiga peran pengguna: admin, UMKM, dan pengunjung publik.

## Fitur

**Admin**
- Approve/reject registrasi UMKM baru
- Kelola dan moderasi data UMKM dan produk
- Kelola kategori produk
- Dashboard ringkasan (total UMKM, total produk, menunggu approval)

**UMKM**
- Registrasi mandiri
- Kelola profil usaha (deskripsi, alamat, kontak, logo)
- Kelola produk sendiri (tambah, edit, hapus, kategori)

**Pengunjung**
- Browse katalog UMKM dan produk
- Filter produk berdasarkan kategori
- Pencarian berdasarkan kata kunci
- Lihat detail UMKM dan produk, termasuk kontak WhatsApp

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend | PHP 8.1+, Laravel |
| Database | MySQL |
| Frontend | Blade, Tailwind (opsional Alpine.js untuk interaksi ringan) |
| Auth | Laravel Breeze |
| Local dev server | XAMPP / `php artisan serve` |

## Requirement

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & npm (untuk compile asset frontend)

## Instalasi

```bash
git clone <repo-url>
cd sistem-informasi-umkm-diy

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Set koneksi database di `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=umkm_diy
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

Seeder akan membuat satu akun admin default untuk keperluan development (kredensial ada di `database/seeders/AdminSeeder.php` — ganti sebelum deploy ke production).

Compile asset dan jalankan server:

```bash
npm run dev
php artisan serve
```

Aplikasi berjalan di `http://localhost:8000`.

## Struktur Data Utama

- `users` — akun admin dan UMKM (role: `admin`, `umkm`)
- `umkm_profiles` — profil usaha, relasi ke `users`, status `pending/approved/rejected`
- `categories` — kategori produk
- `products` — produk, relasi ke `umkm_profiles` dan `categories`

Detail skema dan penjelasan keputusan desain ada di `PRD-Sistem-Informasi-UMKM-Muhammadiyah-DIY.md`.

## Struktur Folder yang Relevan

```
app/Models/            model Eloquent (User, UmkmProfile, Product, Category)
app/Http/Controllers/  controller per modul (Auth, UmkmProfile, Product, Catalog, Admin)
app/Policies/          otorisasi per role
database/migrations/   skema database
database/seeders/      seeder admin & kategori awal
resources/views/       Blade templates
routes/web.php         routing
```

## Testing

Pengujian fungsional mengikuti pendekatan Black Box Testing — setiap fitur di PRD dicek terhadap skenario input/output yang diharapkan, termasuk memastikan satu UMKM tidak bisa mengakses/mengubah data UMKM lain.

```bash
php artisan test
```

## Status Pengembangan

- [ ] Setup project & migration
- [ ] Auth (registrasi UMKM, login admin/UMKM)
- [ ] CRUD profil UMKM + alur approval
- [ ] CRUD produk & kategori
- [ ] Katalog publik (listing, filter, search, detail)
- [ ] Dashboard admin
- [ ] Integrasi dengan website utama UMKM DIY

## Dokumentasi Terkait

- `PRD-Sistem-Informasi-UMKM-Muhammadiyah-DIY.md` — requirement lengkap, data model, dan asumsi desain
