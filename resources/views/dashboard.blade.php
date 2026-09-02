<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            @if(auth()->user()->role === 'admin')
                {{-- Panel Admin --}}

                {{-- F13: Ringkasan metrik --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-6 bg-white shadow sm:rounded-lg text-center">
                        <p class="text-3xl font-extrabold text-brand-700">{{ $approvedCount }}</p>
                        <p class="text-sm text-gray-500 mt-1">Total UMKM Disetujui</p>
                    </div>
                    <div class="p-6 bg-white shadow sm:rounded-lg text-center">
                        <p class="text-3xl font-extrabold text-blue-700">{{ $totalProducts }}</p>
                        <p class="text-sm text-gray-500 mt-1">Total Produk Aktif</p>
                    </div>
                    <div class="p-6 bg-white shadow sm:rounded-lg text-center">
                        <p class="text-3xl font-extrabold text-yellow-600">{{ $pendingCount }}</p>
                        <p class="text-sm text-gray-500 mt-1">Menunggu Persetujuan</p>
                    </div>
                </div>

                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Panel Admin LP UMKM</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.umkm-profiles.pending') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                            Persetujuan UMKM
                        </a>
                        <a href="{{ route('admin.umkm-profiles.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                            Semua UMKM
                        </a>
                        <a href="{{ route('admin.produk.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                            Semua Produk
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                            Kelola Kategori
                        </a>
                    </div>
                </div>
            @else
                {{-- Panel UMKM --}}
                @php($profile = $profile ?? null)
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Profil Usaha Anda</h3>

                    @if(!$profile)
                        <p class="text-sm text-red-600">Profil usaha tidak ditemukan. Silakan hubungi admin.</p>
                    @else
                        @php($statusLabel = [
                            'pending' => 'Menunggu Persetujuan Admin',
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
