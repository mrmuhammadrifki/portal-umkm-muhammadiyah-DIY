<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if(auth()->user()->role === 'admin')
        {{-- Chart.js CDN untuk grafik dinamis admin --}}
        @push('head')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>
        @endpush
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            @if(auth()->user()->role === 'admin')
                {{-- ============================================================
                     PANEL ADMIN
                     ============================================================ --}}

                {{-- F13: Ringkasan Metrik Kartu --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-5 bg-white shadow sm:rounded-xl text-center border-t-4 border-brand-600">
                        <p class="text-3xl font-extrabold text-brand-700">{{ $approvedCount }}</p>
                        <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide font-semibold">UMKM Disetujui</p>
                    </div>
                    <div class="p-5 bg-white shadow sm:rounded-xl text-center border-t-4 border-blue-500">
                        <p class="text-3xl font-extrabold text-blue-700">{{ $totalProducts }}</p>
                        <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide font-semibold">Produk Aktif</p>
                    </div>
                    <div class="p-5 bg-white shadow sm:rounded-xl text-center border-t-4 border-amber-500">
                        <p class="text-3xl font-extrabold text-amber-600">{{ $pendingCount }}</p>
                        <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide font-semibold">Menunggu Persetujuan</p>
                    </div>
                    <div class="p-5 bg-white shadow sm:rounded-xl text-center border-t-4 border-purple-500">
                        <p class="text-3xl font-extrabold text-purple-700">{{ number_format($totalEmployees) }}</p>
                        <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide font-semibold">Total Karyawan</p>
                    </div>
                </div>

                {{-- ============================================================
                     GRAFIK BARIS 1: Sertifikasi Halal + Klasifikasi UMKM
                     ============================================================ --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- GRAFIK 1: Sertifikasi Halal (Donut) --}}
                    <div class="bg-white shadow sm:rounded-xl p-6 border border-gray-100">
                        <div class="mb-4">
                            <h3 class="text-base font-bold text-gray-900">🕌 Sertifikasi Halal UMKM</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Proporsi UMKM yang sudah dan belum bersertifikasi halal (dari yang disetujui).</p>
                        </div>

                        @if($approvedCount > 0)
                            <div class="flex items-center justify-center gap-6 flex-wrap">
                                <div class="relative w-52 h-52">
                                    <canvas id="chartHalal"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                        <p class="text-2xl font-extrabold text-gray-800">{{ $approvedCount }}</p>
                                        <p class="text-xs text-gray-500">Total UMKM</p>
                                    </div>
                                </div>
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-3 h-3 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                        <div>
                                            <span class="font-bold text-gray-800">{{ $halalCount }}</span>
                                            <span class="text-gray-500"> Sudah Bersertifikat</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-3 h-3 rounded-full bg-gray-300 flex-shrink-0"></span>
                                        <div>
                                            <span class="font-bold text-gray-800">{{ $nonHalalCount }}</span>
                                            <span class="text-gray-500"> Belum Bersertifikat</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <p class="text-xs text-gray-400">Tingkat sertifikasi halal</p>
                                        <p class="font-bold text-emerald-700 text-lg">
                                            {{ $approvedCount > 0 ? round(($halalCount / $approvedCount) * 100) : 0 }}%
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-gray-400 py-12 text-sm">Belum ada data UMKM disetujui.</div>
                        @endif
                    </div>

                    {{-- GRAFIK 2: Klasifikasi UMKM (Bar) --}}
                    <div class="bg-white shadow sm:rounded-xl p-6 border border-gray-100">
                        <div class="mb-4">
                            <h3 class="text-base font-bold text-gray-900">📊 Klasifikasi UMKM</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Berdasarkan rata-rata pendapatan per bulan (PP 7/2021):
                                <span class="font-semibold text-gray-700">Mikro</span> &lt;Rp25jt ·
                                <span class="font-semibold text-gray-700">Kecil</span> Rp25jt–208jt ·
                                <span class="font-semibold text-gray-700">Menengah</span> &gt;Rp208jt.
                            </p>
                        </div>

                        <div class="relative h-52">
                            <canvas id="chartKlasifikasi"></canvas>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="bg-blue-50 rounded-lg p-2 border border-blue-100">
                                <p class="text-xl font-extrabold text-blue-700">{{ $mikro }}</p>
                                <p class="text-gray-600 font-semibold">Mikro</p>
                            </div>
                            <div class="bg-indigo-50 rounded-lg p-2 border border-indigo-100">
                                <p class="text-xl font-extrabold text-indigo-700">{{ $kecil }}</p>
                                <p class="text-gray-600 font-semibold">Kecil</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-2 border border-purple-100">
                                <p class="text-xl font-extrabold text-purple-700">{{ $menengah }}</p>
                                <p class="text-gray-600 font-semibold">Menengah</p>
                            </div>
                        </div>

                        @if($belumIsiRevenue > 0)
                            <p class="text-xs text-amber-600 mt-3 bg-amber-50 border border-amber-200 rounded px-2 py-1">
                                ⚠️ <strong>{{ $belumIsiRevenue }}</strong> UMKM belum mengisi data pendapatan —
                                belum termasuk dalam klasifikasi di atas.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- ============================================================
                     PANEL AKSI ADMIN
                     ============================================================ --}}
                <div class="p-6 bg-white shadow sm:rounded-xl border border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 mb-4">⚙️ Panel Admin LP UMKM</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.umkm-profiles.pending') }}" class="inline-flex items-center gap-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                            @if($pendingCount > 0)
                                <span class="bg-white text-purple-700 text-xs font-black px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                            @endif
                            Persetujuan UMKM
                        </a>
                        <a href="{{ route('admin.umkm-profiles.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                            Semua UMKM
                        </a>
                        <a href="{{ route('admin.produk.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                            Semua Produk
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                            Kelola Kategori
                        </a>
                        <a href="{{ route('admin.event.index') }}" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                            Kelola Event
                        </a>
                    </div>
                </div>

                {{-- ============================================================
                     CHART.JS SCRIPTS
                     ============================================================ --}}
                <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {

                        // ── Grafik 1: Donut Sertifikasi Halal ──────────────────────────────
                        const ctxHalal = document.getElementById('chartHalal');
                        if (ctxHalal) {
                            new Chart(ctxHalal, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Sudah Bersertifikat Halal', 'Belum Bersertifikat'],
                                    datasets: [{
                                        data: [{{ $halalCount }}, {{ $nonHalalCount }}],
                                        backgroundColor: ['#10b981', '#d1d5db'],
                                        borderColor: ['#059669', '#9ca3af'],
                                        borderWidth: 2,
                                        hoverOffset: 6,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '72%',
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: ctx => ` ${ctx.label}: ${ctx.raw} UMKM`
                                            }
                                        }
                                    },
                                    animation: { animateRotate: true, duration: 800 }
                                }
                            });
                        }

                        // ── Grafik 2: Bar Klasifikasi UMKM ─────────────────────────────────
                        const ctxKlas = document.getElementById('chartKlasifikasi');
                        if (ctxKlas) {
                            new Chart(ctxKlas, {
                                type: 'bar',
                                data: {
                                    labels: ['Usaha Mikro', 'Usaha Kecil', 'Usaha Menengah'],
                                    datasets: [{
                                        label: 'Jumlah UMKM',
                                        data: [{{ $mikro }}, {{ $kecil }}, {{ $menengah }}],
                                        backgroundColor: [
                                            'rgba(59, 130, 246, 0.75)',
                                            'rgba(99, 102, 241, 0.75)',
                                            'rgba(139, 92, 246, 0.75)',
                                        ],
                                        borderColor: [
                                            'rgb(59, 130, 246)',
                                            'rgb(99, 102, 241)',
                                            'rgb(139, 92, 246)',
                                        ],
                                        borderWidth: 2,
                                        borderRadius: 8,
                                        borderSkipped: false,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: ctx => ` ${ctx.raw} UMKM`
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1,
                                                precision: 0
                                            },
                                            grid: { color: 'rgba(0,0,0,0.05)' }
                                        },
                                        x: {
                                            grid: { display: false }
                                        }
                                    },
                                    animation: { duration: 800, easing: 'easeOutQuart' }
                                }
                            });
                        }

                    });
                </script>

            @else
                {{-- ============================================================
                     PANEL UMKM (USER)
                     ============================================================ --}}
                @php($profile = $profile ?? null)
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Profil Usaha Anda</h3>

                    @if(!$profile)
                        <p class="text-sm text-red-600">Profil usaha tidak ditemukan. Silakan hubungi admin.</p>
                    @else
                        @php($statusLabel = [
                            'pending'  => 'Menunggu Persetujuan Admin',
                            'approved' => 'Disetujui — Tampil di Katalog Publik',
                            'rejected' => 'Ditolak Admin',
                        ][$profile->status])

                        <x-status-badge :status="$profile->status" :label="$statusLabel" class="mb-4" />

                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">{{ $profile->business_name }}</span></p>
                        <p class="text-sm text-gray-600 mb-4">{{ $profile->whatsapp }}</p>

                        <div class="flex space-x-2">
                            <a href="{{ route('umkm-profile.edit') }}" class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                                Lengkapi / Edit Profil Usaha
                            </a>
                            <a href="{{ route('produk.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                                Kelola Produk
                            </a>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
