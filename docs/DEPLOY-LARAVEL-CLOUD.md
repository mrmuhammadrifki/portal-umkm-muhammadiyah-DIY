# Deploy ke Laravel Cloud

Alternatif dari Railway (yang trial-nya sudah habis). Laravel Cloud dipilih karena dibuat langsung oleh tim Laravel, dan cocok untuk demo skripsi karena **scale-to-zero** (compute "tidur" saat tidak ada yang akses, jadi tidak kena biaya saat idle).

## Perbedaan Penting dari Setup Railway Sebelumnya

Laravel Cloud **hanya menyediakan database Postgres** (bukan MySQL). Project ini sudah diverifikasi kompatibel penuh dengan Postgres — sudah dites lokal: migration jalan tanpa error (termasuk kolom `enum`), CRUD lengkap (user → profil UMKM → produk → foto) berhasil, dan constraint `enum` benar-benar menolak nilai tidak valid. **Tidak ada perubahan skema/migration yang diperlukan** — Laravel sudah otomatis mendukung kedua database lewat `DB_CONNECTION` env var saja.

Perubahan yang sudah disiapkan di kode:
- `composer.json`: ditambah `league/flysystem-aws-s3-v3` — dibutuhkan untuk pakai storage bucket Laravel Cloud (setara "Volume" di Railway, tapi berbasis S3-compatible object storage, bukan disk biasa).

## 1. Daftar & Buat Project

1. Buka [cloud.laravel.com](https://cloud.laravel.com), sign up (bulan pertama gratis di plan Starter).
2. **New Project** → **Import from GitHub** → pilih repo `mrmuhammadrifki/portal-umkm-muhammadiyah-DIY`, branch `main`.
3. Laravel Cloud otomatis mendeteksi ini project Laravel dan set build command standar (composer install, npm build, dsb) — biasanya tidak perlu config manual seperti Nixpacks di Railway.

## 2. Attach Database (Serverless Postgres)

1. Di dashboard project → tab **Database** → **Attach Database** → pilih **Serverless Postgres**.
2. Laravel Cloud otomatis mengisi environment variable `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` ke aplikasi — tidak perlu diisi manual.

## 3. Attach Bucket (Storage Foto Permanen)

Tanpa ini, foto yang di-upload UMKM (logo, foto produk) tidak akan tersimpan permanen antar deploy.

1. Tab **Storage** → **Attach Bucket** → buat bucket baru (visibility: **Public** — supaya foto bisa diakses langsung via URL tanpa signed-URL, sesuai kebutuhan katalog publik).
2. Laravel Cloud akan mengisi environment variable S3-compatible secara otomatis (nama persis env var-nya akan ditampilkan di dashboard saat attach — biasanya `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT`, `AWS_URL`). **Cek nama variabel yang muncul di dashboard kamu** — kalau beda, sesuaikan dengan yang ditampilkan, karena ini bisa berubah sewaktu-waktu di sisi Laravel Cloud.
3. Set environment variable tambahan:
   ```env
   FILESYSTEM_DISK=s3
   ```

## 4. Set Environment Variables Lainnya

Tab **Environment** → tambahkan (selain yang otomatis dari Database & Bucket di atas):

```env
APP_NAME="Portal UMKM Muhammadiyah DIY"
APP_ENV=production
APP_KEY=base64:n4+Xy9GbRgfMun9KAfzRb5+igXb8FaUOkrgGpKP2jZc=
APP_DEBUG=false

LOG_CHANNEL=stack
LOG_LEVEL=error

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_DRIVER=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=s3
```

**Catatan `APP_KEY`**: sama seperti panduan Railway sebelumnya — value ini sudah di-generate khusus untuk production, beda dari `.env` lokal. Jangan generate ulang setelah live (bikin semua session logout paksa).

**Catatan `SESSION_DRIVER=database`**: beda dari saran Railway sebelumnya (`file`) — karena di Laravel Cloud tidak ada disk lokal persisten seperti Volume Railway untuk `storage/framework/sessions`, jadi session disimpan di tabel database (`sessions` table, sudah ada migration-nya dari Breeze bawaan Laravel).

## 5. Migration

Laravel Cloud biasanya punya opsi "Run migrations on deploy" di tab **Deployments** → **Settings** — aktifkan supaya `php artisan migrate --force` otomatis jalan tiap deploy sukses.

## 6. Buat Akun Admin (Seeder)

Sama seperti Railway, seeder tidak otomatis jalan (supaya tidak re-seed data tiap deploy). Laravel Cloud punya **Cloud Shell** / **Remote CLI** di dashboard (tab **Console** atau sejenisnya, cek nama pastinya di dashboard kamu) — jalankan:

```bash
php artisan db:seed
```

Setelah itu, akun admin `admin@umkm-muhammadiyah-diy.test` / `password` bisa dipakai — **segera ganti password** setelah login pertama.

## Checklist Verifikasi

- [ ] Domain Laravel Cloud (`xxxxx.laravel.cloud` atau custom domain) menampilkan katalog publik
- [ ] Login admin berhasil
- [ ] Upload logo/foto produk → refresh → foto tetap ada
- [ ] Redeploy (tanpa ubah kode) → foto sebelumnya masih ada (bukti bucket storage kepasang benar, bukan cache browser)

## Yang Perlu Dicek Manual (di luar kendali saya)

- Nama environment variable dari fitur **Attach Bucket** — dashboard Laravel Cloud yang menentukan nama pastinya, saya tidak bisa memverifikasi tanpa akses dashboard kamu.
- Biaya aktual — plan Starter $5/bulan sudah termasuk kredit $5/bulan, jadi untuk app dengan traffic rendah (demo skripsi) kemungkinan besar tetap $0 efektif, tapi tetap perlu kartu pembayaran terdaftar di akun.
