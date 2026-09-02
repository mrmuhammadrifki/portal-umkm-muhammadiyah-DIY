<x-public-layout :title="$umkm->business_name">

    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="p-6 sm:p-8">
                <!-- Header Toko -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 border-b border-gray-100 pb-6 mb-6">
                    @if($umkm->logo_path)
                        <img src="{{ asset('storage/' . $umkm->logo_path) }}" alt="Logo {{ $umkm->business_name }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    @endif
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $umkm->business_name }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Pemilik: <span class="font-semibold text-gray-700">{{ $umkm->owner_name }}</span></p>
                        @if($umkm->kabupaten_kota)
                            <p class="text-sm text-gray-500">📍 <span class="font-semibold text-gray-700">{{ $umkm->kecamatan ? $umkm->kecamatan . ', ' : '' }}{{ $umkm->kabupaten_kota }}</span></p>
                        @endif
                    </div>
                </div>

                <!-- Informasi Detail -->
                <div class="space-y-6">
                    @if($umkm->description)
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Deskripsi Usaha:</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $umkm->description }}</p>
                        </div>
                    @endif

                    @if($umkm->address)
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Alamat:</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $umkm->address }}</p>
                        </div>
                    @endif

                    @if($umkm->instagram)
                        <p class="text-sm text-gray-600">Instagram: <span class="font-medium">{{ $umkm->instagram }}</span></p>
                    @endif
                </div>

                <!-- TOMBOL HUBUNGI VIA WHATSAPP -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $umkm->whatsapp) }}?text=Halo%20{{ urlencode($umkm->business_name) }},%20saya%20tertarik%20dengan%20usaha%20Anda%20setelah%20melihat%20Portal%20UMKM%20Muhammadiyah%20DIY."
                       target="_blank"
                       class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 shadow-md transition duration-150 ease-in-out text-center font-bold">
                        Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- DAFTAR PRODUK -->
        <div class="mt-10">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Produk dari {{ $umkm->business_name }}</h2>

            @if($categories->isNotEmpty())
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
                    <form action="{{ route('katalog.detail', $umkm->id) }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cari Produk</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama produk..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-gray-50 p-2 border">
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kategori</label>
                            <select name="kategori" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-gray-50 p-2 border">
                                <option value="">-- Semua Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('kategori') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-1 flex items-end space-x-2">
                            <button type="submit" class="bg-brand hover:bg-brand-dark text-white font-bold py-2 px-4 rounded-md text-sm shadow-sm transition w-full h-[38px]">
                                Cari & Filter
                            </button>
                            @if(request('search') || request('kategori'))
                                <a href="{{ route('katalog.detail', $umkm->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-md text-sm shadow-sm transition text-center h-[38px] flex items-center justify-center">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <a href="{{ route('katalog.produk-detail', $product) }}" class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 hover:shadow-md transition">
                        @if($product->cover_image_path)
                            <img src="{{ asset('storage/' . $product->cover_image_path) }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                        @endif
                        <div class="p-4">
                            <span class="bg-brand-100 text-brand-800 text-xs font-semibold px-2 py-0.5 rounded">{{ $product->category->name }}</span>
                            <h3 class="font-bold text-gray-900 mt-2">{{ $product->name }}</h3>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white p-8 text-center rounded-lg border border-gray-200">
                        <p class="text-4xl mb-2">📦</p>
                        @if(request('search') || request('kategori'))
                            <p class="text-gray-500">Tidak ada produk yang cocok dengan pencarian/filter.</p>
                        @else
                            <p class="text-gray-500">Belum ada produk yang ditampilkan.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </main>

</x-public-layout>
