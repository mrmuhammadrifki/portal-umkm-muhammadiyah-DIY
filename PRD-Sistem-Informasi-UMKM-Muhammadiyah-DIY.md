# PRD — Sistem Informasi UMKM Muhammadiyah DIY

**Versi:** 1.0
**Sumber:** Proposal skripsi BAB I–III (REVISI)
**Status:** Draft untuk mulai development

---

## 1. Ringkasan Produk

Platform berbasis web untuk memusatkan data UMKM binaan LP UMKM Muhammadiyah DIY, mengelola data usaha dan produk secara terstruktur, serta menyajikannya sebagai katalog digital publik. Sistem bukan marketplace — tidak ada transaksi jual-beli di dalam sistem, kontak lanjutan (pembelian, negosiasi) diarahkan keluar melalui WhatsApp atau kontak lain milik UMKM.

Tiga peran pengguna: **Admin** (LP UMKM), **UMKM** (pemilik usaha), **Pengunjung** (publik, tanpa akun).

## 2. Masalah dan Tujuan

Data ~267 UMKM (sumber Lazismu DIY, 2020–2023) tersebar dan dikelola manual, sehingga sulit diperbarui, dicari, dan dimanfaatkan. Tujuan produk:

- Satu sumber data UMKM dan produk yang bisa dikelola mandiri oleh UMKM dan dimoderasi admin.
- Data tersebut bisa dicari dan disaring dengan cepat oleh admin maupun publik.
- Data tersaji sebagai katalog digital yang mendukung promosi UMKM.

## 3. Ruang Lingkup

**In scope**
- Registrasi dan manajemen profil UMKM secara mandiri.
- Manajemen produk per UMKM (CRUD, kategori).
- Katalog publik UMKM dan produk, dengan pencarian dan filter kategori.
- Dashboard admin untuk monitoring dan pengelolaan data.
- Role-based access: admin, UMKM, publik.

**Out of scope**
- Transaksi jual-beli, keranjang, pembayaran, checkout.
- Chat internal antara pengunjung dan UMKM (diarahkan ke WhatsApp eksternal).
- Multi-tenant di luar Muhammadiyah DIY (skala 1 wilayah saja).
- Native mobile app.

## 4. Aktor dan Kewenangan

| Aktor | Akses |
|---|---|
| **Admin** | Login via akun yang dibuat manual/seed (bukan self-register). Kelola dan verifikasi seluruh data UMKM dan produk, kelola kategori, lihat dashboard. |
| **UMKM** | Registrasi mandiri. Kelola profil usaha sendiri dan produk milik sendiri. Tidak bisa mengubah data UMKM lain. |
| **Pengunjung** | Tanpa akun. Browse dan cari katalog UMKM/produk publik. |

Admin pusat (di luar LP UMKM DIY) tidak termasuk dalam scope sistem ini.

## 5. Data Model

Skema minimal untuk mulai coding. Nama tabel/kolom bisa disesuaikan konvensi Laravel (snake_case, migration).

**users**
`id, name, email, password, role (enum: admin, umkm), phone, is_active, created_at, updated_at`
Pengunjung tidak punya baris di tabel ini — akses publik tanpa auth.

**umkm_profiles**
`id, user_id (FK users), business_name, owner_name, description, address, kecamatan, kabupaten_kota, whatsapp, instagram (nullable), nib (nullable), affiliation_status (enum: afiliasi, non_afiliasi), logo_path (nullable), status (enum: pending, approved, rejected), created_at, updated_at`

**categories**
`id, name, slug`

**products**
`id, umkm_id (FK umkm_profiles), category_id (FK categories), name, description, image_path, status (enum: active, inactive), created_at, updated_at`

Catatan desain:
- `nib` dibuat nullable — thesis menyebut status wajib/opsionalnya belum dikonfirmasi (lihat §9). Nullable adalah pilihan aman, tinggal tambah validasi `required` kalau nanti dipastikan wajib.
- `affiliation_status` disimpan sebagai atribut, tidak memengaruhi hak akses — sesuai konsep afiliasi inklusif di proposal.
- Satu produk = satu kategori, untuk MVP. Kalau nanti butuh multi-kategori per produk, tinggal ganti relasi jadi pivot table.
- Field harga sengaja tidak dimasukkan di produk karena sistem eksplisit bukan marketplace. Kalau nanti dibutuhkan untuk informasi (bukan transaksi), tambah kolom `price_info` bertipe string bebas, bukan decimal — supaya bisa isi "mulai Rp15.000" tanpa harus valid sebagai harga transaksi.

ERD detail (relasi + kardinalitas visual) dan class diagram bisa saya buatkan menyusul kalau sudah siap masuk tahap desain teknis.

## 6. Functional Requirements

Prioritas: P0 = wajib untuk MVP bisa dipakai, P1 = penting tapi bisa menyusul, P2 = nice to have.

| ID | Modul | Deskripsi | Aktor | Prioritas |
|---|---|---|---|---|
| F1 | Auth | UMKM registrasi mandiri (nama usaha, email, password, kontak dasar) | UMKM | P0 |
| F2 | Auth | Login/logout untuk admin dan UMKM | Admin, UMKM | P0 |
| F3 | Profil UMKM | UMKM lengkapi/edit profil usaha (deskripsi, alamat, kontak, logo) | UMKM | P0 |
| F4 | Profil UMKM | Admin approve/reject profil UMKM baru sebelum tampil publik | Admin | P0 |
| F5 | Produk | UMKM tambah/edit/hapus produk milik sendiri | UMKM | P0 |
| F6 | Produk | Produk diberi kategori saat dibuat | UMKM | P0 |
| F7 | Kategori | Admin kelola daftar kategori (CRUD) | Admin | P0 |
| F8 | Katalog | Publik browse daftar UMKM | Pengunjung | P0 |
| F9 | Katalog | Publik browse produk dengan filter kategori | Pengunjung | P0 |
| F10 | Katalog | Pencarian UMKM/produk berdasarkan kata kunci | Pengunjung | P0 |
| F11 | Katalog | Halaman detail UMKM (profil + daftar produk + kontak WA) | Pengunjung | P0 |
| F12 | Katalog | Halaman detail produk | Pengunjung | P0 |
| F13 | Admin | Dashboard ringkasan (total UMKM, total produk, jumlah menunggu approval) | Admin | P1 |
| F14 | Admin | Listing dan pencarian semua UMKM/produk untuk moderasi | Admin | P0 |
| F15 | Profil UMKM | Admin bisa suspend/nonaktifkan UMKM bermasalah | Admin | P1 |
| F16 | Integrasi | Katalog bisa diakses/ditautkan dari website utama UMKM DIY | Admin/Sistem | P1 |

## 7. Non-Functional Requirements

- **Akses berbasis role**: pakai Laravel middleware/policy, jangan cek role manual di tiap controller.
- **Responsif**: pengunjung katalog kemungkinan besar akses dari HP — layout mobile-first, bukan desktop-first yang di-adapt belakangan.
- **Validasi input & upload**: validasi ukuran/tipe file untuk logo dan gambar produk (batasi jpg/png, maks ukuran wajar, misal 2MB).
- **Keamanan dasar**: gunakan Eloquent ORM (aman dari SQL injection by default), hash password dengan bcrypt bawaan Laravel, CSRF protection aktif, rate limit di form registrasi dan login.
- **Performa katalog**: pagination di semua listing publik, jangan load semua data sekaligus.
- **Auditability minimal**: `created_at`/`updated_at` di semua tabel utama, cukup untuk kebutuhan monitoring admin.

## 8. Tech Stack

Sesuai proposal — tidak diubah kecuali ada alasan kuat:

- Backend: PHP + Laravel
- Database: MySQL
- Frontend: Blade + HTML/CSS (bisa tambah Alpine.js/Tailwind kalau butuh interaktivitas ringan tanpa build step berat)
- Local dev: XAMPP
- Auth: Laravel Breeze paling cocok untuk kebutuhan sesimpel ini — daripada Sanctum/Passport yang overkill untuk sistem tanpa API mobile.

## 9. Asumsi dan Keputusan yang Perlu Dikonfirmasi

Proposal secara eksplisit menyisakan beberapa keputusan terbuka (§3.3.9). Supaya coding bisa mulai, berikut asumsi kerja yang dipakai di PRD ini — perlu dikonfirmasi ke dosen pembimbing/pihak LP UMKM, tapi tidak menghalangi mulai development karena perubahan di titik ini relatif murah (field tambahan/validasi), bukan restrukturisasi arsitektur:

1. **Approval registrasi UMKM**: diasumsikan admin harus approve dulu sebelum UMKM tampil di katalog publik (F4). Kalau ternyata auto-approve, tinggal hilangkan status `pending`.
2. **NIB**: diasumsikan opsional (nullable). Kalau wajib, tambah validasi `required` di form registrasi.
3. **Indikator dashboard**: F13 pakai 3 metrik dasar yang hampir pasti dibutuhkan (total UMKM, total produk, pending approval). Indikator tambahan menyusul setelah dikonfirmasi ke LP UMKM.
4. **Mekanisme integrasi website utama**: belum ada keputusan teknis di proposal (embed, subdomain, atau sekadar link-out). Untuk MVP, asumsikan link-out sederhana dari website utama ke domain/subdomain katalog ini — opsi paling murah dan tidak butuh koordinasi API dengan tim website utama. Bisa ditingkatkan ke integrasi lebih dalam nanti.
5. **Multi-kategori per produk**: diasumsikan tidak perlu untuk MVP (satu produk, satu kategori).

## 10. Urutan Development yang Disarankan

Bukan urutan Waterfall skripsi (itu untuk kebutuhan akademik/dokumentasi), tapi urutan praktis biar cepat dapat sistem yang bisa didemokan:

1. Setup project Laravel + migration untuk 4 tabel inti (users, umkm_profiles, categories, products).
2. Auth: registrasi UMKM + login admin/UMKM (F1, F2).
3. CRUD profil UMKM + alur approval admin (F3, F4).
4. CRUD produk + kategori (F5, F6, F7).
5. Katalog publik: listing, filter, search, detail page (F8–F12).
6. Dashboard admin (F13, F14).
7. Fitur sekunder: suspend UMKM, integrasi website utama (F15, F16).

Langkah 1–5 sudah cukup untuk demo end-to-end ke pembimbing atau LP UMKM.

## 11. Definition of Done per Fitur

Sebuah fitur dianggap selesai kalau:
- Fungsi utama jalan sesuai skenario di tabel §6 (bisa dicek manual, sejalan dengan rencana Black Box Testing di skripsi).
- Validasi input mencegah data kosong/invalid pada field wajib.
- Role yang tidak berwenang tidak bisa mengakses (misal UMKM A tidak bisa edit produk UMKM B — ini titik paling sering bocor kalau authorization tidak dites eksplisit).
- Tampilan tidak rusak di lebar layar mobile standar (~375px).
