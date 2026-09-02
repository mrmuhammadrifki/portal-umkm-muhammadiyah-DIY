# Task Requirements — UI Enhancement (Professional, Modern, Clear)

**Tujuan:** Naikkan kualitas visual dari "fungsional tapi ala kadarnya" ke tampilan yang pantas didemokan ke pembimbing/LP UMKM dan nyaman dipakai UMKM/publik — tanpa mengubah logika bisnis yang sudah jadi di `IMPLEMENTATION-PLAN.md`.

## Temuan Kondisi Saat Ini

1. **Dua sistem desain yang tidak nyambung.** Halaman publik (`welcome.blade.php`, `detail-umkm.blade.php`, `produk-publik.blade.php`, `produk-detail.blade.php`) adalah HTML standalone yang masing-masing load Tailwind lewat CDN (`<script src="cdn.tailwindcss.com">`) dan menulis ulang `<nav>`/`<footer>` sendiri-sendiri. Halaman authed (`dashboard`, `produk/*`, `admin/*`, `umkm-profile/*`) pakai layout Breeze (`x-app-layout`) dengan Tailwind yang di-compile via Vite (`resources/css/app.css`). Efeknya: styling drift (4 file publik gampang saling beda warna/spacing kalau diedit terpisah), tidak ada dark-mode/theme konsisten, dan brand warna emerald cuma dipakai manual per file.
2. **Branding Breeze default belum diganti.** Navbar authed area masih pakai logo Laravel bawaan, tidak ada warna brand (emerald) yang konsisten dengan halaman publik. Title tab browser masih `Laravel` (dari `config('app.name')` yang belum diubah dari `.env`).
3. **Tabel admin/UMKM tidak responsif di mobile.** Semua listing (`produk/index`, `admin/umkm-profiles/index`, `admin/produk/index`, `admin/kategori/index`) pakai `<table>` polos dengan `overflow-x-auto` — di layar 375px jadi scroll horizontal, bukan reflow ke card seperti area publik yang sudah grid-responsive.
4. **Badge status warna diketik ulang di banyak tempat.** Pola `['pending' => 'bg-yellow-100...', 'approved' => 'bg-green-100...']` di-copy paste di 3+ file blade — kalau warna status mau diubah, harus edit banyak tempat.
5. **Tidak ada feedback visual selain flash text polos.** Sukses/error cuma kotak warna solid tanpa ikon/transisi; tidak ada loading state di form submit (upload logo/gambar bisa berasa "diam" beberapa detik).
6. **Form panjang tanpa struktur visual.** Form profil UMKM (10+ field) dan tambah produk cuma stack vertikal rata tanpa pengelompokan (mis. "Info Usaha" vs "Lokasi" vs "Kontak"), bikin terasa berat dibanding form modern yang biasa dipisah section.
7. **Tidak ada branding/identitas**: belum ada favicon custom, meta description untuk SEO/share preview, atau warna tema browser (`theme-color`).

## Prinsip Desain yang Dipakai

- **Satu sistem, bukan dua.** Semua halaman (publik maupun authed) pakai satu layout dasar + Tailwind yang sama (compiled via Vite), bukan campur CDN vs build.
- **Konsisten, bukan seragam kaku.** Warna brand emerald tetap jadi warna utama, tapi didefinisikan sekali (Tailwind theme extend / CSS variable), dipakai di semua tempat lewat token yang sama.
- **Mobile-first tetap dijaga** (sudah jadi prinsip di Tahap 4 PRD) — perbaikan tidak boleh regresi ke desktop-first.
- **Komponen di-reuse, bukan di-copy-paste** — badge status, kartu, tombol jadi Blade component sekali jadi.

## Task Requirements (Urutan Prioritas)

### T1 — Satukan Sistem Layout & Styling (Blocker untuk semua task lain) ✅

- [x] 4 halaman publik (`welcome`, `detail-umkm`, `produk-publik`, `produk-detail`) dipindah dari HTML standalone + Tailwind CDN ke component `<x-public-layout>` (`app/View/Components/PublicLayout.php` → `layouts/public.blade.php`) yang pakai Vite build (`@vite(['resources/css/app.css', 'resources/js/app.js'])`), sama seperti authed area.
- [x] `layouts/public.blade.php` dibuat — `<nav>` + `<footer>` publik didefinisikan SEKALI (dengan nav-link aktif via `request()->routeIs()`), dipakai keempat halaman lewat `$slot`. Duplikasi navbar/footer 4x sudah hilang.
- [x] `<script src="https://cdn.tailwindcss.com">` dihapus dari semua file — Tailwind `content` array (`./resources/views/**/*.blade.php`) otomatis men-scan halaman publik yang sekarang jadi partial blade biasa (bukan HTML standalone lagi).
- [x] `npm run build` dijalankan, sukses tanpa error, CSS output (39.59kB) sudah termasuk semua class dari halaman publik (diverifikasi `bg-brand*` classes ter-compile).

**DoD:** ✅ Terverifikasi via curl: semua halaman publik 200 OK, `cdn.tailwindcss.com` sudah tidak muncul di response HTML manapun (digantikan tag `@vite`), tidak ada `<nav>`/`<footer>` yang diketik ulang lebih dari 1 file, build produksi sukses.

**Catatan implementasi:** Warna brand (`brand`, `brand-light`, `brand-dark`, `brand-darker` di `tailwind.config.js`) dan perbaikan `APP_NAME` (`.env`) dikerjakan lebih awal sebagai prasyarat T1 — layout baru butuh keduanya untuk render dengan benar, jadi sebagian kecil T2/T3 sudah otomatis selesai duluan (lihat catatan di section masing-masing).

### T2 — Branding & Identitas

- [x] Ganti `APP_NAME` di `.env` ke `"Portal UMKM Muhammadiyah DIY"` (dikerjakan sebagai prasyarat T1 — layout publik butuh `config('app.name')` yang benar).
- [x] Ganti `x-application-logo` (`resources/views/components/application-logo.blade.php`) dari logo Laravel default ke ikon storefront/UMKM (SVG, tetap `fill-current` supaya warna ikut class pemanggil) — dipakai di navbar authed (`layouts/navigation.blade.php`, sekarang `text-brand`) dan halaman login/register (`layouts/guest.blade.php`, sekarang `text-brand`).
- [x] Favicon ditambahkan sebagai inline SVG data URI (`<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,...">`, warna emerald-700) di ketiga layout dasar (`layouts/app.blade.php`, `layouts/guest.blade.php`, `layouts/public.blade.php`) — tidak perlu file `.ico` binary terpisah. `theme-color` ditambahkan di ketiganya.
- [x] `<meta name="description">` ditambahkan di `layouts/public.blade.php` (berlaku untuk semua 4 halaman publik yang memakainya).

**DoD:** ✅ Tab browser menampilkan nama halaman spesifik (bukan "Laravel") dan favicon custom di semua halaman — diverifikasi via curl: title dinamis per halaman (`Katalog UMKM - Portal UMKM Muhammadiyah DIY`), favicon SVG muncul di homepage dan halaman login.

### T3 — Unifikasi Warna Brand & Design Tokens

- [x] Extend `tailwind.config.js` `theme.extend.colors.brand` jadi skala lengkap (50–900 + alias `DEFAULT`/`light`/`dark`/`darker`) — pada dasarnya alias resmi atas skala emerald Tailwind, supaya rebrand cukup ubah di 1 tempat.
- [x] Sapu bersih **semua** `emerald-*` → `brand-*` di seluruh 14 file blade yang sebelumnya hardcode (`sed` global, diverifikasi `grep -rl emerald resources/views/` = 0 hasil setelahnya). Termasuk halaman publik (card produk, badge kategori, footer) dan authed area (tombol, focus ring, checkbox).
- [x] Buat Blade component `<x-status-badge :status="..." :label="..." />` (`app/View/Components/StatusBadge.php` + `components/status-badge.blade.php`) — satu mapping warna untuk `pending/approved/rejected` (profil UMKM), `active/inactive` (produk), dan `account_active/account_suspended` (status akun). Dipakai ulang di `dashboard`, `admin/umkm-profiles/index`, `produk/index`, `admin/produk/index` — menghapus 4 array warna yang tadinya di-copy-paste terpisah. Prop `:label` opsional untuk override teks default (dipakai di dashboard UMKM yang butuh label panjang "Menunggu Persetujuan Admin").

**DoD:** ✅ Ubah 1 warna di `tailwind.config.js` cukup untuk propagate ke semua halaman (diverifikasi: 0 sisa hardcode `emerald-*`, `npm run build` sukses). Tidak ada lagi array badge warna yang di-copy-paste di view — semua lewat 1 component. Diverifikasi via `Blade::render()` (3 varian status render benar) dan HTTP login test penuh (dashboard admin, `/admin/umkm`, `/admin/produk`, `/admin/kategori` semua 200 OK dengan komponen baru aktif).

**DoD:** Ubah 1 warna brand di 1 tempat (`tailwind.config.js`) cukup untuk propagate ke semua halaman; tidak ada lagi array badge warna yang di-copy-paste di view.

### T4 — Navbar & Navigasi Authed Area ✅

- [x] Restyle `layouts/navigation.blade.php` + `nav-link`/`responsive-nav-link` component (indigo default Breeze → `brand-*`). Badge role (admin/umkm) **dipindah** dari header dashboard ke navbar (ada di semua halaman authed sekarang, bukan cuma dashboard — duplikasinya di `dashboard.blade.php` dihapus).
- [x] Link navigasi utama ditambahkan di navbar (desktop + versi mobile responsive), muncul beda per role: UMKM → "Profil Usaha", "Produk Saya"; admin → "Persetujuan", "Semua UMKM", "Semua Produk", "Kategori". Active-state ditandai (`request()->routeIs(...)`).

**DoD:** ✅ Dari halaman manapun di authed area, user bisa pindah ke semua halaman fitur miliknya lewat navbar. Diverifikasi via HTTP login test admin: `/dashboard`, `/admin/persetujuan-umkm`, `/admin/umkm`, `/admin/produk`, `/admin/kategori` semua 200 OK, dan link navigasi (Persetujuan/Semua UMKM/Semua Produk/Kategori/badge ADMIN) muncul di render dashboard.

### T5 — Tabel Admin/UMKM Jadi Responsif (Card di Mobile) ✅

- [x] 4 listing table (`produk/index`, `admin/umkm-profiles/index`, `admin/produk/index`, `admin/kategori/index`) direfactor: pola `<div class="sm:hidden">` (card list per baris, termasuk semua aksi/badge) + `<div class="hidden sm:block">` (table asli, tanpa perubahan) — bukan lagi cuma `overflow-x-auto` yang memaksa scroll horizontal di mobile.
- [x] Empty state di tiap card list juga diperbaiki (ikon + pesan, konsisten dengan gaya T5 — sebagian tumpang tindih dengan scope T7 nanti).

**DoD:** ✅ Tidak ada horizontal scroll di tabel manapun pada lebar 375px — data & aksi (edit/hapus/suspend/aktifkan) sepenuhnya bisa diakses dalam bentuk card. Diverifikasi: `php -l` lolos di 4 file, HTTP login test admin 200 OK di 3 halaman (`/admin/umkm`, `/admin/produk`, `/admin/kategori`) dengan class `sm:hidden`/`hidden sm:block` terkonfirmasi ada di response HTML; `produk/index` (sisi UMKM, tidak bisa dites via login credential milik user) diverifikasi lewat render langsung `view('produk.index', [...])->render()` — sukses tanpa error blade.

### T6 — Form UX: Pengelompokan & Feedback ✅

- [x] `umkm-profile/edit` (form terpanjang, 10+ field) dikelompokkan jadi 5 `<fieldset>` bervisual jelas: "Informasi Usaha", "Lokasi", "Kontak & Media Sosial", "Legalitas & Afiliasi", "Logo Usaha" — masing-masing dengan `<legend>` heading + garis pemisah.
- [x] State loading pada tombol submit di ketiga form (`umkm-profile/edit`, `produk/create`, `produk/edit`): pola Alpine.js `x-data="{ submitting: false }"` + `@submit="submitting = true"` + `:disabled="submitting"` + spinner SVG + teks berubah jadi "Menyimpan..." — pola aman standar untuk mencegah double-submit tanpa perlu AJAX/build tool baru (sesuai batasan Tech Stack PRD §8).
- [x] Preview gambar client-side (Alpine `@change` + `URL.createObjectURL()`) untuk field logo (`umkm-profile/edit`) dan gambar produk (`produk/create`, `produk/edit`) — preview langsung muncul saat file dipilih, dan tetap menampilkan gambar lama kalau input dikosongkan lagi (di form edit).

**DoD:** ✅ Form besar terstruktur per bagian; submit memberi indikasi sedang diproses (spinner + disable, cegah double-submit); user bisa preview gambar sebelum submit. Diverifikasi: `php -l` lolos di 3 file, render test via `view(...)->render()` dengan data asli (profil, kategori, produk) — sukses tanpa error di ketiganya, `npm run build` sukses.

### T7 — Empty State & Micro-interaction ✅

- [x] Semua pesan "Belum ada.../Tidak ada data" polos diganti empty state dengan ikon emoji + copy lebih hidup: katalog UMKM publik 🏬, katalog produk publik 🛍️, produk kosong di detail UMKM 📦, produk kosong di dashboard UMKM (mobile+desktop) 📦 + CTA "Tambah Produk Pertama", dan **persetujuan UMKM kosong 🎉** ("Semua profil sudah dimoderasi").
- [x] **Ditemukan & diperbaiki tambahan**: `admin/umkm-profiles/pending.blade.php` ternyata terlewat dari refactor tabel responsif T5 (masih tabel polos tanpa versi mobile card) — sudah disamakan polanya (card mobile + table desktop) sekaligus di sini.
- [x] Sapu bersih sisa warna `indigo-*` (bukan `emerald-*`, jadi tidak ke-cover sweep T3) di komponen shared Breeze (`primary-button`, `secondary-button`, `text-input`) dan halaman auth (`login`, `register`, `verify-email`) → `brand-*`. Login/register sekarang pakai focus ring & warna tombol yang konsisten dengan seluruh app, bukan indigo default Breeze. Warna `indigo-600`/`purple-600` di `dashboard.blade.php` (tombol aksi admin) sengaja dipertahankan sebagai aksen pembeda kategori aksi, bukan sisa yang belum disapu.

**DoD:** ✅ Tidak ada halaman dengan state kosong yang terasa seperti error — semua actionable/informatif. Card interaktif (produk publik, UMKM publik) sudah konsisten pakai `transition hover:shadow-md` dari T1. Diverifikasi: `php -l` lolos semua file, `npm run build` sukses, HTTP test `/admin/persetujuan-umkm` 200 OK dengan markup responsif + emoji empty state terkonfirmasi ada di response.

## Yang Sengaja Tidak Masuk Scope

- Desain ulang total (rebrand logo dari nol, custom illustration) — di luar kapasitas coding, butuh aset desain dari user/tim.
- Dark mode — tidak diminta PRD, bisa jadi task terpisah kalau dibutuhkan nanti.
- Animasi kompleks (Framer Motion dsb) — PRD eksplisit menghindari "build step berat" (§8 Tech Stack), cukup Alpine.js/Tailwind yang sudah ada.

## Catatan Implementasi

- Task **T1 wajib dikerjakan lebih dulu** — task lain (T3–T7) akan terasa sia-sia kalau masih ada 2 sistem styling berbeda, karena perubahan token warna di T3 tidak akan konsisten selama T1 belum selesai.
- Semua perubahan harus tetap lolos regression check manual terhadap DoD §11 PRD (authorization, validasi, mobile 375px) — UI enhancement tidak boleh menyentuh logika bisnis yang sudah diverifikasi di `IMPLEMENTATION-PLAN.md`.
