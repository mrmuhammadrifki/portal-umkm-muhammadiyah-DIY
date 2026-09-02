# Deploy ke Railway

Status: kode sudah di-push ke `https://github.com/mrmuhammadrifki/portal-umkm-muhammadiyah-DIY` (branch `main`). Konfigurasi build (`nixpacks.toml`) sudah disiapkan di root project.

Bagian di bawah ini **harus dilakukan sendiri lewat dashboard Railway** (butuh login akun kamu) — Claude tidak bisa login ke akun Railway siapapun.

## 1. Buat Project dari GitHub Repo

1. Buka [railway.app](https://railway.app), login (bisa pakai akun GitHub yang sama).
2. **New Project** → **Deploy from GitHub repo** → pilih `mrmuhammadrifki/portal-umkm-muhammadiyah-DIY`.
3. Railway akan otomatis mendeteksi `nixpacks.toml` dan mulai build. **Build pertama akan gagal** — itu wajar, karena environment variable & database belum diset. Lanjut ke langkah 2–4 dulu.

## 2. Tambah Database MySQL

1. Di dashboard project, klik **+ New** → **Database** → **Add MySQL**.
2. Railway otomatis membuat service MySQL dengan variabel `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD` — kamu tidak perlu isi manual, tinggal direferensikan di langkah 4.

## 3. Tambah Volume (Penyimpanan Foto Permanen)

Tanpa ini, semua foto yang di-upload UMKM (logo, foto produk) **akan hilang setiap redeploy**.

1. Klik service aplikasi (bukan MySQL) → tab **Settings** → scroll ke **Volumes** → **+ New Volume**.
2. Mount path: `/app/storage`
3. Simpan.

## 4. Set Environment Variables

Di service aplikasi → tab **Variables** → **Raw Editor** (lebih cepat dari isi satu-satu), paste ini lalu sesuaikan yang ditandai:

```env
APP_NAME="Portal UMKM Muhammadiyah DIY"
APP_ENV=production
APP_KEY=base64:n4+Xy9GbRgfMun9KAfzRb5+igXb8FaUOkrgGpKP2jZc=
APP_DEBUG=false
APP_URL=https://GANTI-SETELAH-DAPAT-DOMAIN.up.railway.app

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
```

**Penting soal `APP_KEY`**: value di atas sudah di-generate khusus untuk production (beda dari `.env` lokal kamu — jangan pernah pakai APP_KEY yang sama antara local dan production). Jangan generate ulang setelah aplikasi jalan — mengganti `APP_KEY` di kemudian hari akan membuat semua session aktif logout paksa dan data terenkripsi (kalau ada) tidak terbaca.

**Soal `${{MySQL.MYSQLHOST}}` dkk**: itu sintaks *reference variable* Railway — otomatis terisi dari service MySQL yang kamu tambahkan di langkah 2, asalkan nama service-nya persis `MySQL` (default). Kalau kamu rename service MySQL-nya, sesuaikan nama di sini juga.

## 5. Redeploy & Dapatkan Domain

1. Tab **Deployments** → **Redeploy** (supaya build ulang dengan env vars yang baru diisi).
2. Setelah build sukses, tab **Settings** → **Networking** → **Generate Domain** untuk dapat URL publik (`xxxxx.up.railway.app`).
3. Copy domain itu, balik ke tab **Variables**, update `APP_URL` jadi domain asli tsb (dengan `https://`).
4. **Redeploy sekali lagi** supaya `APP_URL` yang baru dipakai (perlu build ulang karena `config:cache` di-cache saat build).

## 6. Buat Akun Admin (Seeder)

Migration jalan otomatis tiap deploy (`php artisan migrate --force` ada di start command), **tapi seeder (akun admin + kategori awal) tidak otomatis jalan** — sengaja, supaya tidak re-seed data tiap redeploy.

Jalankan sekali manual lewat Railway CLI:

```bash
# Install Railway CLI (sekali saja, di komputer manapun)
npm install -g @railway/cli

# Login (buka browser)
railway login

# Link ke project ini (jalankan dari folder project)
railway link

# Jalankan seeder di server production
railway run php artisan db:seed
```

Setelah ini, akun admin `admin@umkm-muhammadiyah-diy.test` / `password` bisa dipakai login di domain production — **segera ganti password-nya** lewat halaman profil setelah login pertama kali, karena password default ini ada di kode (bukan rahasia).

## Checklist Verifikasi Setelah Deploy

- [ ] Buka domain Railway → halaman katalog publik tampil (bukan error 500)
- [ ] Login admin berhasil, dashboard menampilkan metrik
- [ ] Coba upload logo/foto produk → refresh halaman → foto tetap ada (bukti Volume kepasang benar)
- [ ] Redeploy sekali (tanpa perubahan kode) → foto yang di-upload sebelumnya **masih ada** (bukti persistence beneran jalan, bukan cuma cache browser)

## Catatan Jujur soal Keterbatasan Setup Ini

- **`php artisan serve` dipakai sebagai web server** di start command — ini server bawaan Laravel untuk development, bukan web server production standar (Nginx/Apache/Caddy + PHP-FPM). Untuk skala project ini (demo skripsi, traffic rendah), ini cukup dan jadi opsi paling sederhana tanpa perlu konfigurasi web server terpisah. Kalau nanti traffic besar, pertimbangkan pindah ke setup Docker + PHP-FPM + Caddy.
- **Single instance only** — kalau nanti perlu multi-instance/auto-scaling, Volume tidak ter-share otomatis antar instance; harus pindah ke S3-compatible storage (mis. Cloudflare R2) untuk foto.
