<x-public-layout title="Katalog UMKM">

    <!-- HERO SECTION -->
    <header class="relative overflow-hidden">
        <div
            class="absolute inset-0 bg-cover bg-center scale-105"
            style="background-image: url('{{ asset('bg.jpeg') }}');"
        ></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/60 to-black/30"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-brand-darker/70 via-transparent to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-28 sm:pt-24 sm:pb-36 text-center">
            <span class="inline-block bg-white/10 backdrop-blur-sm border border-white/20 text-white text-xs font-semibold uppercase tracking-widest px-4 py-1.5 rounded-full mb-6">
                LP UMKM Pimpinan Wilayah Muhammadiyah DIY
            </span>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight max-w-3xl mx-auto drop-shadow-sm">
                Temukan & Dukung UMKM Muhammadiyah di Yogyakarta
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-base sm:text-xl text-gray-200">
                Direktori resmi produk dan jasa kreatif kader Muhammadiyah — cari, jelajahi, dan hubungi langsung lewat WhatsApp.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="#katalog" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold py-3 px-6 rounded-lg shadow-lg transition">
                    Jelajahi Katalog UMKM
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 text-white font-bold py-3 px-6 rounded-lg transition">
                    Daftar UMKM Sekarang
                </a>
            </div>

            <!-- STAT BADGES -->
            <div class="mt-12 grid grid-cols-3 max-w-lg mx-auto gap-4 sm:gap-8">
                <div>
                    <p class="text-2xl sm:text-4xl font-extrabold text-white">{{ $stats['umkm'] }}+</p>
                    <p class="text-xs sm:text-sm text-gray-300 mt-1">UMKM Terdaftar</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-4xl font-extrabold text-white">{{ $stats['produk'] }}+</p>
                    <p class="text-xs sm:text-sm text-gray-300 mt-1">Produk & Jasa</p>
                </div>
                <div>
                    <p class="text-2xl sm:text-4xl font-extrabold text-white">{{ $stats['wilayah'] }}</p>
                    <p class="text-xs sm:text-sm text-gray-300 mt-1">Wilayah Cakupan</p>
                </div>
            </div>
        </div>
    </header>

    <!-- KATALOG UMKM -->
    <main id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">

        <!-- KOTAK PENCARIAN & FILTER — mengambang di atas hero -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-xl border border-gray-100 -mt-16 sm:-mt-20 relative z-10 mb-10">
            <form action="{{ route('katalog.publik') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cari Nama UMKM</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama usaha..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-gray-50 p-2 border">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Wilayah</label>
                    <select name="wilayah" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-gray-50 p-2 border">
                        <option value="">-- Semua Wilayah --</option>
                        @foreach($wilayahList as $wilayah)
                            <option value="{{ $wilayah }}" {{ request('wilayah') == $wilayah ? 'selected' : '' }}>{{ $wilayah }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Kategori Produk</label>
                    <select name="kategori" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-gray-50 p-2 border">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('kategori') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-brand hover:bg-brand-dark text-white font-bold py-2 px-4 rounded-md text-sm shadow-sm transition w-full h-[40px] cursor-pointer">
                        Cari & Filter
                    </button>
                    @if(request('search') || request('wilayah') || request('kategori'))
                        <a href="{{ route('katalog.publik') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-md text-sm shadow-sm transition text-center h-[40px] flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- KATEGORI CEPAT -->
        @if($categories->where('products_count', '>', 0)->isNotEmpty())
            <div class="mb-10">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Jelajahi per Kategori</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories->where('products_count', '>', 0) as $category)
                        <a href="{{ route('katalog.produk', ['kategori' => $category->id]) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-brand-50 border border-gray-200 hover:border-brand text-gray-700 hover:text-brand-dark text-sm font-medium px-4 py-2 rounded-full shadow-sm transition">
                            {{ $category->name }}
                            <span class="text-xs text-gray-400">({{ $category->products_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-8">Daftar UMKM Terdaftar</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($umkms as $item)
                <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 flex flex-col justify-between transition hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-start gap-3 mb-2">
                            @if($item->logo_path)
                                <img src="{{ asset('storage/' . $item->logo_path) }}" alt="Logo {{ $item->business_name }}" class="w-10 h-10 rounded object-cover flex-shrink-0">
                            @endif
                            <a href="{{ route('katalog.detail', $item->id) }}" class="text-lg font-bold text-gray-900 hover:text-brand transition">
                                {{ $item->business_name }}
                            </a>
                        </div>
                        @if($item->kabupaten_kota)
                            <p class="text-sm text-gray-500 mb-4">📍 <span class="font-medium text-gray-700">{{ $item->kabupaten_kota }}</span></p>
                        @endif
                        <p class="text-gray-600 text-sm line-clamp-3">{{ $item->description }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 border-t border-gray-100">
                        <a href="{{ route('katalog.detail', $item->id) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand hover:bg-brand-dark shadow-sm transition">
                            Lihat Detail & Produk
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 text-center rounded-lg border border-gray-200">
                    <p class="text-5xl mb-3">🏬</p>
                    <p class="text-gray-500 text-lg">Belum ada katalog UMKM yang terdaftar saat ini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $umkms->links() }}
        </div>
    </main>

</x-public-layout>
