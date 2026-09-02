# Deploy ke Clever Cloud

Alternatif Railway/Laravel Cloud — dipilih karena **MySQL native** (tidak perlu ganti database sama sekali, beda dari Laravel Cloud yang Postgres-only) dan ada tier gratis.

## 1. Buat Add-on MySQL

Sudah kamu mulai — di layar "What type of resource do you want to create?":

1. Klik **Add-on** → cari **MySQL** → pilih plan terkecil/gratis (biasanya berlabel "Dev")
2. Nama bebas, misal `portal-umkm-db`
3. Setelah dibuat, buka add-on ini → catat nilai **Host, Port, Database, User, Password** yang ditampilkan (dipakai manual di langkah 4, bukan otomatis ter-inject dengan nama variabel yang Laravel kenali).

## 2. Buat Add-on FS Bucket (Storage Foto Permanen)

Tanpa ini, foto upload (logo UMKM, foto produk) hilang tiap deploy — sama seperti alasan Volume di Railway.

1. **Create** → **Add-on** → cari **FS Bucket**
2. Nama bebas, misal `portal-umkm-storage`

## 3. Buat Application dari GitHub Repo

1. **Create** → **Application** → **Import from a Git repository**
2. Login/authorize GitHub kalau diminta → pilih repo `mrmuhammadrifki/portal-umkm-muhammadiyah-DIY`, branch `main`
3. Pilih runtime **PHP**
4. Pilih region terdekat (mis. Paris/Warsaw — Clever Cloud tidak punya region Asia, jadi latency ke Indonesia agak lebih tinggi dari Railway/Laravel Cloud; untuk demo skripsi biasanya masih cukup responsif)

## 4. Link Add-on ke Application

Di halaman Application → tab **Service dependencies** (atau sejenis) → link kedua add-on yang dibuat di langkah 1 & 2 ke application ini.

## 5. Set Environment Variables

Tab **Environment variables** di Application, tambahkan:

```env
# Wajib — arahkan ke folder public Laravel
CC_WEBROOT=/public

# Hook build & deploy
CC_PRE_RUN_HOOK=npm install && npm run build
CC_POST_BUILD_HOOK=php artisan migrate --force && php artisan storage:link --force && php artisan config:cache

# App
APP_NAME="Portal UMKM Muhammadiyah DIY"
APP_ENV=production
APP_KEY=base64:GENERATE_BARU_LIHAT_CATATAN_DI_BAWAH
APP_DEBUG=false
LOG_CHANNEL=stack

# Database — isi manual pakai nilai asli dari add-on MySQL (langkah 1), BUKAN nama variabel di bawah ini
DB_CONNECTION=mysql
DB_HOST=isi-dari-dashboard-mysql-addon
DB_PORT=isi-dari-dashboard-mysql-addon
DB_DATABASE=isi-dari-dashboard-mysql-addon
DB_USERNAME=isi-dari-dashboard-mysql-addon
DB_PASSWORD=isi-dari-dashboard-mysql-addon

# Storage — FS Bucket dari langkah 2 di-mount ke storage/app, jadi disk 'public' Laravel otomatis persisten
FILESYSTEM_DISK=public
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

**Generate APP_KEY baru khusus Clever Cloud** (jangan pakai ulang yang dari Railway/Laravel Cloud):
```bash
php artisan key:generate --show
```
Jalankan itu di terminal lokal kamu, copy hasilnya ke `APP_KEY` di atas.

**`SESSION_DRIVER=database`**: sama alasannya seperti Laravel Cloud — FS Bucket cuma mount ke `storage/app`, bukan seluruh folder `storage/`, jadi `storage/framework/sessions` tidak otomatis persisten kalau pakai driver `file`.

## 6. Deploy

Clever Cloud auto-deploy tiap push ke branch yang dipilih (mirip Railway). Karena kode sudah ada di GitHub, harusnya deploy pertama otomatis jalan setelah linking selesai — kalau tidak, cari tombol **Deploy**/**Restart** di dashboard Application.

## 7. Aktifkan Domain

Tab **Domain names** → aktifkan domain `.cleverapps.io` bawaan (gratis) untuk dapat URL publik. Update `APP_URL` di environment variables dengan domain ini, lalu redeploy.

## 8. Buat Akun Admin (Seeder)

```bash
# Install CLI Clever Cloud (sekali saja)
npm install -g clever-tools

# Login (buka browser)
clever login

# Link ke application ini (jalankan dari folder project)
clever link

# Buka SSH ke instance yang jalan, lalu jalankan seeder
clever ssh
# setelah masuk shell:
php artisan db:seed
exit
```

Setelah itu, akun admin `admin@umkm-muhammadiyah-diy.test` / `password` bisa dipakai — **segera ganti password** setelah login pertama.

## Checklist Verifikasi

- [ ] Domain `.cleverapps.io` menampilkan katalog publik (bukan error)
- [ ] Login admin berhasil
- [ ] Upload foto → refresh → foto tetap ada
- [ ] Redeploy tanpa ubah kode → foto sebelumnya masih ada

## Catatan Jujur — Bagian yang Perlu Kamu Verifikasi Sendiri

Beberapa detail teknis Clever Cloud berubah dari waktu ke waktu dan saya tidak bisa memastikan 100% tanpa akses dashboard langsung:

1. **`CC_PRE_RUN_HOOK` untuk `npm run build`** — saya asumsikan Node.js tersedia di image PHP Clever Cloud saat build. Kalau build gagal karena `npm: command not found`, kemungkinan perlu setting tambahan (mis. `CC_NODE_VERSION` atau runtime terpisah) — cek dokumentasi Clever Cloud PHP runtime saat itu terjadi.
2. **Region tidak ada di Asia** — semua region Clever Cloud ada di Eropa. Untuk demo ke pembimbing di Indonesia, latency mungkin terasa (biasanya masih dalam batas wajar untuk browsing biasa, cuma bukan yang tercepat).
3. Kalau ada langkah di dashboard yang labelnya beda dari yang saya sebut di sini (UI Clever Cloud bisa berubah), ambil browser screenshot dan kirim ke saya, saya bantu sesuaikan.
