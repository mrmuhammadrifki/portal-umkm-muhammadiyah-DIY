# Rencana Implementasi — Sistem Informasi UMKM Muhammadiyah DIY

**Sumber:** `PRD-Sistem-Informasi-UMKM-Muhammadiyah-DIY.md` v1.0
**Status kondisi awal:** dievaluasi terhadap codebase saat ini (skema `umkms` flat, belum sesuai §5 PRD)

Dokumen ini memecah urutan development di PRD §10 menjadi task teknis konkret, per tahap, sesuai prioritas.

---

## Tahap 0 — Migrasi Skema ke Model Data PRD §5

Prasyarat untuk semua tahap berikutnya. Skema saat ini (tabel `umkms` flat + kolom custom di `users`) tidak punya pemisahan profil UMKM vs produk, tidak ada kategori relasional, dan tidak ada status approval.

- [x] Migration `users` — `role` enum admin/umkm, `phone`, `is_active` ditambahkan langsung di base migration (belum ada data produksi, jadi migration lama di-rebuild bersih, bukan ditumpuk alter). Kolom lama (`nama_pemilik`, `alamat_pemilik`, `no_ktp`, `status_akun`) dibuang — datanya nanti masuk `umkm_profiles`.
- [x] Migration `create umkm_profiles table`: sesuai skema §5 persis.
- [x] Migration `create categories table`.
- [x] Migration `create products table`.
- [x] Model `UmkmProfile` (relasi `belongsTo User`, `hasMany Product`).
- [x] Model `Category` (relasi `hasMany Product`).
- [x] Model `Product` (relasi `belongsTo UmkmProfile` via `umkm_id`, `belongsTo Category`).
- [x] Model `Umkm` lama dihapus (tidak ada data produksi untuk dimigrasikan — masih tahap dev).
- [x] Seeder: 1 akun admin (`admin@umkm-muhammadiyah-diy.test` / `password`) + 6 kategori awal (Kuliner, Fashion, Kerajinan, Jasa, Pertanian, Lainnya — placeholder, perlu dikonfirmasi ke LP UMKM).

**Definition of done:** `php artisan migrate:fresh --seed` menghasilkan skema sesuai §5, ada 1 akun admin siap login. ✅ Terverifikasi jalan.

**Efek samping yang diketahui (ditangani di Tahap 1–4):** `UmkmController` dan view (`welcome.blade.php`, `dashboard.blade.php`, `edit-umkm.blade.php`, `detail-umkm.blade.php`) masih merujuk model `Umkm` lama yang sudah dihapus — sekarang fatal error saat diakses. Ini disengaja: refactor controller/view ke `UmkmProfile`/`Product` adalah scope Tahap 1–4, bukan Tahap 0.

---

## Tahap 1 — Auth (F1, F2 — P0)

- [x] Registrasi mandiri untuk role `umkm`: form register Breeze ditambah field `business_name` + `whatsapp`, `RegisteredUserController@store` membuat `User` (role dipaksa `umkm`, tidak bisa diubah dari form) + `UmkmProfile` (`status = pending`) dalam satu DB transaction.
- [x] Tidak ada jalur publik untuk membuat akun `admin` — role di-hardcode `'umkm'` di controller, field role tidak pernah diekspos ke form. Admin hanya via `DatabaseSeeder`.
- [x] Middleware `admin` (`EnsureUserIsAdmin`, alias di `Kernel.php`) dibuat dan siap dipasang di route admin-only (belum ada route yang butuh, karena `UmkmController` masih menunggu refactor Tahap 2–4). Menggantikan pola `if (auth()->user()->role == 'admin')` manual di controller.
- [x] Rate limit `throttle:6,1` ditambahkan di route `POST register` dan `POST login` (NFR §7) — login juga sudah punya rate limit per-email bawaan Breeze (`LoginRequest`, 5x percobaan).
- [x] Login/logout sudah ada dari Breeze, redirect tunggal ke `/dashboard` untuk semua role — cukup karena konten dashboard dibedakan di controller (bukan level redirect), sesuai rencana Tahap 5.

**Definition of done:** UMKM baru bisa daftar dan langsung dapat baris `umkm_profiles` berstatus `pending`. ✅ Diverifikasi via tinker (create User+UmkmProfile, relasi `user->umkmProfile` jalan, cascade delete jalan). Middleware admin siap pakai untuk Tahap 2+.

---

## Tahap 2 — CRUD Profil UMKM + Approval (F3, F4 — P0)

- [x] `UmkmProfileController`: `edit`/`update` untuk pemilik profil sendiri saja (`UmkmProfilePolicy@update` — cek `umkm_profile->user_id === auth()->id()`). Route `GET/PUT /profil-usaha`.
- [x] Form lengkapi profil: deskripsi, alamat, kecamatan, kabupaten/kota, WA, IG (opsional), NIB (opsional), status afiliasi, logo (upload, validasi jpg/png maks 2MB via `Storage::disk('public')`, `storage:link` sudah dibuat).
- [x] Admin: halaman list profil `pending` (`GET /admin/persetujuan-umkm`, ter-paginate) + aksi approve/reject, diproteksi middleware route `admin` DAN `UmkmProfilePolicy@moderate`.
- [ ] Profil `pending`/`rejected` disembunyikan dari katalog publik — **ditunda ke Tahap 4** karena katalog publik (`UmkmController@halamanUtama`) masih placeholder kosong sampai direfactor pakai `UmkmProfile`/`Product`.

**Definition of done:** UMKM edit profil sendiri, tidak bisa edit punya orang lain. ✅ Diverifikasi via tinker: admin `can('moderate')` = true, UMKM biasa = false; owner `can('update', $profile)` = true, UMKM lain = false. Approve mengubah `status` jadi `approved`.

**Halaman/route baru:** `dashboard` (role-aware, via `DashboardController`), `umkm-profile.edit/update`, `admin.umkm-profiles.pending/approve/reject`. View lama `edit-umkm.blade.php` dan alur "Tambah Data UMKM" di dashboard dihapus — konsep "banyak UMKM per user" digantikan "satu profil per user, banyak produk" (produk menyusul Tahap 3).

---

## Tahap 3 — CRUD Produk + Kategori (F5, F6, F7 — P0)

- [x] `CategoryController` (`/admin/kategori`, resource CRUD, admin-only via middleware `admin`): name → auto-generate slug. Hapus kategori diblok kalau masih dipakai produk (dicek eksplisit di controller + `onDelete('restrict')` di DB sebagai fallback).
- [x] `ProductController` (`/produk`): `index/create/store/edit/update/destroy` scoped ke `umkm_profile` milik user login via `ProductPolicy` (`update`/`delete` cek `product->umkm_id === user->umkmProfile->id`). User tanpa profil (mis. belum lengkapi profil) diblok 403 dengan pesan jelas.
- [x] Form tambah/edit produk: name, description, category (dropdown dari `categories`), status, image upload (validasi jpg/png maks 2MB, sama seperti logo — pola konsisten dengan Tahap 2).
- [ ] Produk `status = inactive` tidak muncul di katalog publik — **ditunda ke Tahap 4** (sama alasannya seperti approval filter di Tahap 2, katalog publik masih placeholder).
- [x] Seeder kategori (sudah dibuat di Tahap 0): Kuliner, Fashion, Kerajinan, Jasa, Pertanian, Lainnya.

**Definition of done:** UMKM bisa CRUD produk miliknya sendiri saja, tiap produk wajib pilih 1 kategori dari daftar admin. ✅ Diverifikasi via tinker: owner `can('update')` = true, UMKM lain = false; hapus kategori yang masih dipakai produk diblok DB constraint.

**Catatan desain:** Produk bisa ditambah UMKM meski profilnya masih `pending` (belum di-approve admin) — pembatasan "hanya approved yang bisa kelola produk" tidak diminta PRD secara eksplisit, cukup filter visibilitas di katalog publik saja (Tahap 4).

---

## Tahap 4 — Katalog Publik (F8–F12 — P0)

- [x] Refactor `halamanUtama` → listing `umkm_profiles` (`status = approved`) dengan **pagination** (`paginate(9)->withQueryString()`, NFR §7).
- [x] Listing produk publik (`/produk-publik`, halaman terpisah dari listing UMKM) dengan filter kategori dari tabel `categories`, hanya produk `active` milik UMKM `approved`.
- [x] Search kata kunci — dua context terpisah sesuai UX: search nama usaha di halaman UMKM (`/`), search nama produk di halaman produk (`/produk-publik`). Filter wilayah (kabupaten/kota, dropdown dinamis dari data approved) juga ada di halaman UMKM.
- [x] Halaman detail UMKM (`/katalog/{id}`): profil lengkap + daftar produk aktif milik UMKM tsb + tombol kontak WA (`wa.me/<nomor>`, nomor di-sanitize `preg_replace` sebelum dipakai di URL).
- [x] Halaman detail produk (`/produk-publik/{product}`): info produk + link balik ke UMKM pemilik + tombol WA langsung dengan pesan berisi nama produk.
- [x] Layout mobile-first: `grid-cols-1` default → `sm:grid-cols-2` → `lg:grid-cols-3/4`; navbar `flex-wrap` untuk layar sempit.

**Definition of done:** Pengunjung tanpa akun bisa browse, cari, filter, dan lihat detail UMKM/produk approved saja; semua listing punya pagination. ✅ Diverifikasi via curl: homepage 200 + data tampil, search match/no-match berfungsi, detail UMKM 200 + produk ter-load, detail produk 200 + link WA ter-generate. Profil/produk `pending`/`inactive`/`rejected` **tidak** query-able publik (`abort_unless` di `produkDetail`, `where('status', ...)` konsisten di semua query).

> Tahap 0–4 = milestone demo end-to-end (sesuai PRD §10: "Langkah 1–5 sudah cukup untuk demo").

---

## Tahap 5 — Dashboard Admin (F13, F14 — P1)

- [x] `DashboardController@index` (admin branch): 3 metrik dasar — total UMKM approved, total produk active, jumlah pending approval. Ditampilkan sebagai kartu ringkasan di dashboard.
- [x] `UmkmProfileController@index` (`/admin/umkm`): listing + search (nama usaha) + filter status (pending/approved/rejected), semua status tampil (beda dari katalog publik yang cuma approved).
- [x] `ProductController@adminIndex` (`/admin/produk`): listing + search (nama produk) + filter kategori + filter status, lintas semua UMKM. Otorisasi memakai gate `moderate` yang sama dengan `UmkmProfilePolicy` (satu gate admin-only, dipakai untuk semua aksi moderasi — tidak perlu policy terpisah per model).

**Definition of done:** Admin login langsung lihat ringkasan angka + bisa cari/filter semua data untuk moderasi. ✅ Diverifikasi via tinker: gate `moderate` benar (admin ya, UMKM tidak), query metrik menghasilkan angka akurat sesuai data di DB.

---

## Tahap 6 — Fitur Sekunder (F15, F16 — P1)

- [x] Admin suspend/aktifkan UMKM (`UmkmProfileController@suspend/@reactivate`, `/admin/umkm/{id}/suspend|reactivate`). **Keputusan desain**: reuse kolom `users.is_active` (sudah ada dari Tahap 0) alih-alih menambah state baru di `umkm_profiles.status` — supaya semantik "approval workflow" (pending/approved/rejected) tetap terpisah bersih dari "aktif/nonaktifnya akun". Efek suspend: **blokir login** (`LoginRequest::authenticate()` cek `is_active` setelah `Auth::attempt` sukses, logout paksa + pesan error kalau nonaktif) **dan** hilang dari semua query katalog publik (`whereHas('user', fn($q) => $q->where('is_active', true))` ditambahkan di 4 query: listing UMKM, listing wilayah, detail UMKM, listing+detail produk).
- [x] Tombol Suspend/Aktifkan + badge status akun ditambahkan di `/admin/umkm` (listing moderasi F14).
- [x] Link-out ke website utama UMKM DIY: **tidak butuh kode tambahan** sesuai asumsi PRD §9.4 (cukup link, bukan integrasi API) — URL katalog publik stabil di `/` (route `katalog.publik`). Tinggal diberikan ke tim website utama untuk dipasang sebagai link keluar; tidak ada endpoint API yang perlu dikoordinasikan.

**Definition of done:** Admin bisa suspend UMKM dan efeknya langsung terlihat di katalog publik. ✅ Diverifikasi: setelah suspend, UMKM approved hilang dari homepage (curl match 0), setelah reaktivasi muncul kembali (curl match 2).

---

## Cross-cutting — berlaku di semua tahap (NFR §7 + DoD §11)

- [ ] Validasi input mencegah data kosong/invalid di semua field wajib (form request classes, bukan validasi inline berulang).
- [ ] Authorization dites eksplisit per fitur: UMKM A tidak bisa akses/edit data UMKM B.
- [ ] Rate limit di form registrasi dan login (Laravel throttle middleware).
- [ ] `created_at`/`updated_at` otomatis ada (default Eloquent) di semua tabel baru.
- [ ] Validasi upload file: tipe (jpg/png) dan ukuran (maks 2MB) untuk logo dan gambar produk.

---

## Tahap 7 (Tambahan di luar PRD) — Multi-Foto Produk + Slider ✅

Diminta langsung oleh user setelah UI Enhancement selesai — bukan bagian PRD asli, tapi memperluas F5/F12.

- [x] Migration `product_images` (`product_id` FK cascade, `image_path`, `sort_order`) — tabel baru, tidak mengubah `products.image_path` lama (dipertahankan sebagai fallback data lama).
- [x] Model `ProductImage` + relasi `Product::images()` (`hasMany`, ordered by `sort_order`) + accessor `Product::cover_image_path` (foto pertama galeri, fallback ke `image_path` legacy kalau galeri kosong — dipakai di semua thumbnail listing).
- [x] `ProductController@store/@update`: input `images[]` (multi-file, maks 6, validasi jpg/png 2MB per foto) — foto baru di-append ke galeri (bukan replace), `sort_order` lanjut dari nilai max existing.
- [x] `ProductController@destroyImage` (route baru `DELETE /produk/{produk}/foto/{image}`) — hapus 1 foto dari galeri (bukan hapus produk), dilindungi `ProductPolicy@update` yang sama + validasi foto benar milik produk tsb.
- [x] `destroy()` produk sekarang hapus semua file di galeri dari storage juga (sebelumnya cuma hapus `image_path` tunggal) — cegah orphan file.
- [x] Form `produk/create` & `produk/edit`: input file diganti `images[]` multi-select dengan preview multi-gambar (Alpine.js). Form edit menampilkan galeri foto existing dengan tombol hapus per foto (konfirmasi dulu, submit form tersembunyi).
- [x] Slider publik di `produk-detail.blade.php`: Alpine.js murni (tanpa library eksternal, sesuai batasan Tech Stack PRD §8) — transisi fade antar foto, tombol prev/next, dot indicator; otomatis sembunyi kalau cuma 1 foto (fallback ke tampilan gambar tunggal seperti sebelumnya).
- [x] Thumbnail di `produk-publik.blade.php` dan `detail-umkm.blade.php` diganti pakai `cover_image_path` (bukan `image_path` langsung) — konsisten menampilkan foto pertama galeri.

**Definition of done:** ✅ UMKM bisa upload beberapa foto sekaligus/bertahap per produk, hapus foto individual, dan pengunjung publik melihatnya sebagai slider yang bisa di-swipe/klik. Diverifikasi: migration jalan, `php -l` lolos semua file baru/diubah, `npm run build` sukses, render test via tinker (seed 3 foto dummy → `cover_image_path` ambil foto pertama, array JSON slider di HTML berisi 3 URL benar; cleanup → fallback ke 1 foto legacy tetap jalan).

**Bug ditemukan user setelah rilis awal, sudah diperbaiki:**
1. **Form hapus foto bikin submit macet/nyangkut ("loading terus")** — root cause: `<form>` hapus foto sempat ditaruh *bersarang* di dalam `<form>` edit produk utama. HTML tidak mengizinkan form di dalam form; browser menutup form terluar lebih awal saat parsing HTML tidak valid ini, membuat submit form utama tidak terhubung dengan benar. **Fix**: form-form hapus foto dipindah jadi sibling (di luar `<form>` utama, tapi tetap dalam scope `x-data` yang sama supaya tombol × masih bisa memicu submit-nya lewat Alpine `$refs`).
2. **Pilih foto kedua kali malah replace, bukan menambah** — root cause: ini perilaku native `<input type="file" multiple>` di semua browser — setiap kali dialog file dibuka lagi, `FileList` bawaan browser **mengganti total**, bukan menambah ke pilihan sebelumnya. **Fix**: state file dikelola manual di Alpine (`files: []` array persisten), setiap kali user pilih file baru → di-*append* ke array itu → lalu direkonstruksi ulang jadi `FileList` yang sebenarnya lewat `DataTransfer` API dan ditempel balik ke `<input>` (`$refs.imagesInput.files = dt.files`) sebelum submit. Preview & tombol hapus per-foto-pending juga dipindah baca dari array `files`, bukan langsung dari `input.files`.

Diverifikasi ulang setelah fix: render test kedua form (`produk.create`, `produk.edit`) sukses tanpa error; struktur HTML form hapus foto dikonfirmasi tidak lagi bersarang (form utama ditutup dulu baru form hapus foto muncul).

**Enhancement lanjutan diminta user:**
- **Lightbox klik-untuk-perbesar** di `produk-detail.blade.php`: foto di slider sekarang bisa diklik (atau tombol kaca pembesar) untuk membuka overlay fullscreen (`x-teleport="body"`, Alpine core — tidak perlu plugin tambahan) menampilkan foto dengan `object-contain` (tidak di-crop seperti slider utama yang `object-cover`) + navigasi prev/next + tutup via tombol/klik backdrop/tombol Escape, dan panah kiri-kanan keyboard saat lightbox terbuka.
- **Soal foto terlihat blur**: sudah diperbaiki bagian yang bisa diperbaiki dari sisi tampilan (lightbox `object-contain` menghindari crop/distorsi tambahan dibanding slider kecil yang `object-cover`). **Catatan jujur**: kalau blur-nya berasal dari resolusi asli foto yang memang rendah (foto di-upscale browser untuk mengisi ruang tampil yang lebih besar dari resolusi aslinya), itu bukan sesuatu yang bisa "di-enhance" lewat kode — perbaikannya adalah UMKM re-upload foto dengan resolusi lebih tinggi. Tidak ada image super-resolution/AI upscaling di scope aplikasi ini.

---

## Keputusan Terbuka yang Perlu Dikonfirmasi (PRD §9)

Tidak menghalangi mulai coding, tapi perlu ditandai selama development karena bisa mengubah validasi/field di tahap terkait:

1. Approval registrasi UMKM wajib sebelum tampil publik — diasumsikan **ya** (Tahap 2).
2. NIB opsional — diasumsikan **ya**, nullable (Tahap 2).
3. Indikator dashboard tambahan di luar 3 metrik dasar — menyusul (Tahap 5).
4. Mekanisme integrasi website utama — diasumsikan **link-out sederhana** (Tahap 6).
5. Multi-kategori per produk — diasumsikan **tidak perlu**, 1 produk = 1 kategori (Tahap 3).

---

## Tahap 8 (Tambahan di luar PRD) — Redesain Hero & Halaman Utama Publik ✅

Diminta user karena halaman utama (yang pertama dilihat pengunjung) dinilai masih terlalu sederhana.

- [x] Hero section baru di `welcome.blade.php`: background image `public/bg.jpeg` (ilustrasi Pasar Beringharjo, 1376×768) dengan gradient overlay gelap (`from-black/85` ke transparan + tint `brand-darker`) supaya teks putih tetap terbaca di atas gambar yang ramai.
- [x] 2 CTA di hero: "Jelajahi Katalog UMKM" (scroll anchor ke `#katalog`) dan "Daftar UMKM Sekarang" (`route('register')`, gaya outline/glass biar tidak bersaing visual dengan CTA utama).
- [x] 3 kartu statistik live di hero (total UMKM approved, total produk aktif, jumlah wilayah cakupan) — diambil dari query count di `UmkmController@halamanUtama`, bukan angka hardcode.
- [x] Search/filter card dibuat "mengambang" di atas hero (negative margin + `relative z-10`) untuk kesan modern/layered, bukan lagi kotak polos terpisah dari hero.
- [x] Section baru "Jelajahi per Kategori" — chip/pill kategori dengan jumlah produk aktif per kategori (`Category::withCount('products')`), link ke katalog produk publik terfilter. Kategori tanpa produk otomatis disembunyikan (tidak ada pill kosong yang membingungkan).

**Definition of done:** Halaman utama publik terasa lebih hidup dan profesional dengan hero image + CTA jelas, bukan sekadar search bar polos di atas grid. Diverifikasi: `php -l` lolos, `npm run build` sukses, HTTP test 200 OK dengan konfirmasi hero image ter-serve (200, ukuran file cocok), kedua CTA, ketiga stat badge, dan chip kategori semua muncul di response HTML.

---

## Tahap 9 (Tambahan di luar PRD) — Perbaikan Navigasi Balik

Ditemukan user: halaman `login`/`register` tidak punya jalan balik ke beranda selain klik logo kecil (tidak terlihat seperti navigasi), dan area authed (admin/UMKM) tidak punya jalan ke situs publik sama sekali.

- [x] `layouts/guest.blade.php` (dipakai `login`, `register`, `forgot-password`, dll): ditambah top bar hijau eksplisit dengan link "← Kembali ke Beranda" ke `route('katalog.publik')` — bukan cuma mengandalkan klik logo yang kurang terlihat sebagai navigasi. Logo juga diarahkan ke katalog publik (bukan `/` generik).
- [x] `layouts/navigation.blade.php` (authed area, dipakai admin & UMKM): ditambah link "🌐 Lihat Situs Publik" → `route('katalog.publik')` di dropdown akun (desktop) dan menu responsive (mobile), diposisikan di atas "Profile" — mudah ditemukan tapi tidak mengganggu link navigasi kerja utama (Dashboard/Persetujuan/dst).

**Definition of done:** Dari halaman login/register maupun dari halaman manapun di area admin/UMKM, ada jalan eksplisit dan terlihat untuk kembali ke katalog publik. Diverifikasi: HTTP test — `/login` dan `/register` menampilkan teks "Kembali ke Beranda"; `/dashboard` (session admin) menampilkan "Lihat Situs Publik".

- [x] **Navbar publik jadi hamburger di mobile** (`layouts/public.blade.php`) — sebelumnya semua link (Daftar UMKM, Katalog Produk, Login, Daftar UMKM) `flex-wrap` berjejer penuh di layar sempit, sekarang disembunyikan ke menu hamburger (pola Alpine `x-data="{ open: false }"` sama seperti navbar authed area) supaya navbar tetap satu baris rapi di mobile.

**DoD tambahan:** ✅ Diverifikasi via HTTP: tombol hamburger ("Buka menu") muncul di response HTML halaman `/` dan `/produk-publik/{id}` — semua halaman yang pakai `<x-public-layout>` otomatis ikut konsisten karena navbar didefinisikan di satu tempat (lihat T1).

---

## Tahap 10 (Tambahan di luar PRD) — Search & Filter Produk di Halaman Detail UMKM

Ditemukan user: halaman detail UMKM (`/katalog/{id}`) cuma menampilkan grid produk polos, tidak ada cara cari/filter produk spesifik UMKM tsb — beda dengan katalog produk publik (`/produk-publik`) yang sudah punya search+filter (F9/F10). UMKM dengan banyak produk jadi sulit dijelajahi pengunjung.

- [x] `UmkmController@detail`: sekarang terima `Request` + query `search` (nama produk) dan `kategori` (category_id) — filter di-scope ke produk milik UMKM tsb saja (`$umkm->products()->where(...)`), bukan lintas semua UMKM seperti di `produkPublik()`.
- [x] Dropdown kategori di halaman ini **cuma menampilkan kategori yang benar-benar dipakai produk UMKM tsb** (`Category::whereHas('products', fn($q) => $q->where('umkm_id', $umkm->id)...)`), bukan semua kategori sistem — supaya tidak ada opsi filter yang pasti kosong. Form filter otomatis disembunyikan total kalau UMKM belum punya kategori produk apapun.
- [x] Empty state produk dibedakan: "Belum ada produk yang ditampilkan" (memang belum ada produk) vs "Tidak ada produk yang cocok dengan pencarian/filter" (ada produk tapi tidak match filter) — supaya pengunjung tidak salah paham UMKM-nya kosong padahal cuma hasil filter yang kosong.

**Definition of done:** Pengunjung bisa cari & filter produk spesifik di dalam halaman satu UMKM, bukan cuma di katalog produk global. Diverifikasi via curl: search "Mendoan" match, search "TidakAda" menampilkan empty state filter yang benar, filter kategori valid menampilkan produk, kategori tidak ada (id 999) menampilkan empty state yang sama.
