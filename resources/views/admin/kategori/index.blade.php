<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Kelola Kategori & Subsektor
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Klasifikasi skala usaha (PP No. 7/2021) dan Subsektor Resmi Kementerian Ekonomi Kreatif (EKRAF) RI.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.subsektor.create') }}" class="inline-flex items-center gap-1.5 bg-brand hover:bg-brand-dark text-white font-bold py-2.5 px-4 rounded-xl text-sm shadow-md transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    + Tambah Subsektor
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ activeTab: 'semua' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ALERT NOTIFIKASI --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 flex items-center gap-3 shadow-sm">
                    <span class="text-xl">✅</span>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 text-red-800 rounded-xl border border-red-200 flex items-center gap-3 shadow-sm">
                    <span class="text-xl">⚠️</span>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- ============================================================
                 BAGIAN 1: KATEGORI SKALA USAHA (MIKRO, KECIL, MENENGAH)
                 ============================================================ --}}
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100">
                    <div>
                        <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                            <span>🏛️ Acuan Regulasi: PP No. 7 Tahun 2021</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-900">
                            1. Kategori Skala Usaha (Berdasarkan Pendapatan Bulanan)
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Penggolongan otomatis profil mitra UMKM binaan berdasarkan rata-rata omzet/pendapatan dalam sebulan.
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Total UMKM Binaan Aktif:</span>
                        <p class="text-2xl font-black text-gray-800">{{ $scaleStats['total'] }} <span class="text-sm font-semibold text-gray-500">Mitra</span></p>
                    </div>
                </div>

                {{-- 3 KARTU KATEGORI --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- KARTU 1: MIKRO --}}
                    @php($mikroCat = $categories->firstWhere('slug', 'mikro'))
                    <div class="relative overflow-hidden rounded-2xl border-2 border-blue-200 bg-gradient-to-b from-blue-50/60 via-white to-white p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-100/50 rounded-full -mr-8 -mt-8 pointer-events-none"></div>
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-blue-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm">
                                    Usaha Mikro
                                </span>
                                <span class="text-2xl">🌱</span>
                            </div>
                            <p class="text-xs text-blue-800 font-semibold uppercase tracking-wide">Rata-Rata Pendapatan</p>
                            <p class="text-2xl font-black text-blue-950 mt-1">
                                &lt; Rp 25 Juta <span class="text-xs font-normal text-gray-500">/ bulan</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Setara omzet tahunan &lt; Rp 300 Juta
                            </p>

                            <div class="mt-5 pt-4 border-t border-blue-100 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 font-medium">UMKM Binaan:</span>
                                    <span class="font-extrabold text-blue-900 text-base">{{ $scaleStats['mikro'] }} UMKM</span>
                                </div>
                                <div class="w-full bg-blue-100 rounded-full h-2 overflow-hidden">
                                    @php($pctMikro = $scaleStats['total'] > 0 ? round(($scaleStats['mikro'] / $scaleStats['total']) * 100) : 0)
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $pctMikro }}%"></div>
                                </div>
                                <p class="text-right text-[11px] text-gray-400 font-medium">{{ $pctMikro }}% dari total mitra</p>
                            </div>

                            <p class="text-xs text-gray-600 mt-3 leading-relaxed bg-white/80 p-2.5 rounded-lg border border-blue-100">
                                {{ $mikroCat?->description ?? 'Rata-rata pendapatan di bawah Rp 25 juta per bulan (< Rp 300 juta/tahun).' }}
                            </p>
                        </div>

                        @if($mikroCat)
                            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                <a href="{{ route('admin.kategori.edit', $mikroCat) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-700 hover:text-blue-900 hover:underline">
                                    ⚙️ Edit Parameter
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- KARTU 2: KECIL --}}
                    @php($kecilCat = $categories->firstWhere('slug', 'kecil'))
                    <div class="relative overflow-hidden rounded-2xl border-2 border-emerald-200 bg-gradient-to-b from-emerald-50/60 via-white to-white p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-100/50 rounded-full -mr-8 -mt-8 pointer-events-none"></div>
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm">
                                    Usaha Kecil
                                </span>
                                <span class="text-2xl">🌿</span>
                            </div>
                            <p class="text-xs text-emerald-800 font-semibold uppercase tracking-wide">Rata-Rata Pendapatan</p>
                            <p class="text-2xl font-black text-emerald-950 mt-1">
                                Rp 25 Juta – 208 Juta <span class="text-xs font-normal text-gray-500">/ bln</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Setara omzet tahunan Rp 300 Juta – 2,5 Miliar
                            </p>

                            <div class="mt-5 pt-4 border-t border-emerald-100 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 font-medium">UMKM Binaan:</span>
                                    <span class="font-extrabold text-emerald-900 text-base">{{ $scaleStats['kecil'] }} UMKM</span>
                                </div>
                                <div class="w-full bg-emerald-100 rounded-full h-2 overflow-hidden">
                                    @php($pctKecil = $scaleStats['total'] > 0 ? round(($scaleStats['kecil'] / $scaleStats['total']) * 100) : 0)
                                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $pctKecil }}%"></div>
                                </div>
                                <p class="text-right text-[11px] text-gray-400 font-medium">{{ $pctKecil }}% dari total mitra</p>
                            </div>

                            <p class="text-xs text-gray-600 mt-3 leading-relaxed bg-white/80 p-2.5 rounded-lg border border-emerald-100">
                                {{ $kecilCat?->description ?? 'Rata-rata pendapatan Rp 25 juta s/d Rp 208 juta per bulan (Rp 300 juta – Rp 2,5 miliar/tahun).' }}
                            </p>
                        </div>

                        @if($kecilCat)
                            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                <a href="{{ route('admin.kategori.edit', $kecilCat) }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                                    ⚙️ Edit Parameter
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- KARTU 3: MENENGAH --}}
                    @php($menengahCat = $categories->firstWhere('slug', 'menengah'))
                    <div class="relative overflow-hidden rounded-2xl border-2 border-purple-200 bg-gradient-to-b from-purple-50/60 via-white to-white p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-purple-100/50 rounded-full -mr-8 -mt-8 pointer-events-none"></div>
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-purple-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm">
                                    Usaha Menengah
                                </span>
                                <span class="text-2xl">🌳</span>
                            </div>
                            <p class="text-xs text-purple-800 font-semibold uppercase tracking-wide">Rata-Rata Pendapatan</p>
                            <p class="text-2xl font-black text-purple-950 mt-1">
                                &gt; Rp 208 Juta <span class="text-xs font-normal text-gray-500">/ bulan</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Setara omzet tahunan &gt; Rp 2,5 Miliar
                            </p>

                            <div class="mt-5 pt-4 border-t border-purple-100 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 font-medium">UMKM Binaan:</span>
                                    <span class="font-extrabold text-purple-900 text-base">{{ $scaleStats['menengah'] }} UMKM</span>
                                </div>
                                <div class="w-full bg-purple-100 rounded-full h-2 overflow-hidden">
                                    @php($pctMenengah = $scaleStats['total'] > 0 ? round(($scaleStats['menengah'] / $scaleStats['total']) * 100) : 0)
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $pctMenengah }}%"></div>
                                </div>
                                <p class="text-right text-[11px] text-gray-400 font-medium">{{ $pctMenengah }}% dari total mitra</p>
                            </div>

                            <p class="text-xs text-gray-600 mt-3 leading-relaxed bg-white/80 p-2.5 rounded-lg border border-purple-100">
                                {{ $menengahCat?->description ?? 'Rata-rata pendapatan di atas Rp 208 juta per bulan (> Rp 2,5 miliar/tahun).' }}
                            </p>
                        </div>

                        @if($menengahCat)
                            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                <a href="{{ route('admin.kategori.edit', $menengahCat) }}" class="inline-flex items-center gap-1 text-xs font-bold text-purple-700 hover:text-purple-900 hover:underline">
                                    ⚙️ Edit Parameter
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            </section>


            {{-- ============================================================
                 BAGIAN 2: 21 SUBSEKTOR EKONOMI KREATIF (EKRAF)
                 ============================================================ --}}
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100">
                    <div>
                        <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                            <span>🎨 Standar Resmi Kemenparekraf / EKRAF</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-900">
                            2. Subsektor Ekonomi Kreatif (21 Jenis EKRAF)
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Klasifikasi jenis bidang usaha kreatif binaan LP UMKM berdasarkan penetapan perluasan subsektor Kementerian Ekonomi Kreatif RI.
                        </p>
                    </div>

                    {{-- STAT BADGES EKRAF --}}
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg font-semibold border border-gray-200">
                            📊 Total: <strong>{{ $statsEkraf['total_subsektor'] }}</strong> Subsektor
                        </span>
                        <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg font-semibold border border-emerald-200">
                            ✨ Aktif Terisi: <strong>{{ $statsEkraf['subsektor_aktif'] }}</strong>
                        </span>
                        <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-semibold border border-blue-200">
                            🛍️ Total Produk: <strong>{{ $statsEkraf['total_produk'] }}</strong>
                        </span>
                    </div>
                </div>

                {{-- OFFICIAL CITATION CALLOUT --}}
                <div class="mb-6 p-4 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 rounded-xl border border-amber-200 text-xs sm:text-sm text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl flex-shrink-0">🌐</span>
                        <div>
                            <p class="font-bold">Referensi Resmi Kementerian Ekonomi Kreatif (EKRAF):</p>
                            <p class="text-amber-800 text-xs mt-0.5">
                                Ruang lingkup ekonomi kreatif diperluas menjadi 21 subsektor guna memayungi perkembangan industri kreatif dan digital nusantara.
                            </p>
                        </div>
                    </div>
                    <a href="https://ekraf.go.id/news/rindekraf-perluas-cakupan-ekraf-menjadi" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex-shrink-0 self-start sm:self-center">
                        Buka ekraf.go.id
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                </div>

                {{-- SEARCH & FILTER BAR --}}
                <div class="mb-6">
                    <form action="{{ route('admin.kategori.index') }}" method="GET" class="flex items-center gap-2 max-w-md">
                        <div class="relative flex-1">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama subsektor..."
                                class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring-brand pl-10 py-2.5"
                            >
                            <span class="absolute left-3.5 top-3 text-gray-400">🔍</span>
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-gray-800 hover:bg-black text-white text-sm font-semibold rounded-xl transition">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.kategori.index') }}" class="px-3 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-xl transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                {{-- GRID 21 SUBSEKTOR --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($subsectors as $sub)
                        <div class="rounded-xl border border-gray-200 p-5 bg-white hover:border-brand-500 hover:shadow-md transition flex flex-col justify-between group">
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-2xl flex-shrink-0 group-hover:bg-brand-50 group-hover:border-brand-200 transition">
                                            {{ $sub->icon ?: '🏷️' }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm group-hover:text-brand-700 transition">
                                                {{ $loop->iteration }}. {{ $sub->name }}
                                            </h4>
                                            <p class="text-[11px] text-gray-400 font-mono mt-0.5">
                                                {{ $sub->slug }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-600 mt-3 line-clamp-2 leading-relaxed">
                                    {{ $sub->description ?: 'Bidang ekonomi kreatif resmi binaan LP UMKM.' }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700" title="Jumlah produk di subsektor ini">
                                        🛍️ {{ $sub->products_count }} produk
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700" title="Jumlah UMKM terdaftar">
                                        🏬 {{ $sub->umkm_profiles_count }} UMKM
                                    </span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.subsektor.edit', $sub) }}" class="p-1.5 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg transition" title="Edit Subsektor">
                                        ✏️
                                    </a>
                                    <form action="{{ route('admin.subsektor.destroy', $sub) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus subsektor \'{{ $sub->name }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus Subsektor">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full p-12 text-center bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-4xl">🔍</span>
                            <p class="text-gray-600 font-bold mt-2">Tidak ada subsektor yang cocok dengan pencarian.</p>
                            <a href="{{ route('admin.kategori.index') }}" class="text-brand-600 text-xs font-semibold hover:underline mt-1 inline-block">
                                Tampilkan Semua 21 Subsektor
                            </a>
                        </div>
                    @endforelse
                </div>

            </section>

        </div>
    </div>
</x-app-layout>
