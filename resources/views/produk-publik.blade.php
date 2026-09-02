<x-public-layout title="Katalog Produk">

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-8">Katalog Produk UMKM</h1>

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200 mb-8">
            <form action="{{ route('katalog.produk') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cari Nama Produk</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama produk..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-gray-50 p-2 border">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Kategori</label>
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
                    @if(request('search') || request('kategori'))
                        <a href="{{ route('katalog.produk') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-md text-sm shadow-sm transition text-center h-[40px] flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
                <a href="{{ route('katalog.produk-detail', $product) }}" class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 hover:shadow-md transition">
                    @if($product->cover_image_path)
                        <img src="{{ asset('storage/' . $product->cover_image_path) }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                    @endif
                    <div class="p-4">
                        <span class="bg-brand-100 text-brand-800 text-xs font-semibold px-2 py-0.5 rounded">{{ $product->category->name }}</span>
                        <h3 class="font-bold text-gray-900 mt-2">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $product->umkmProfile->business_name }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white p-12 text-center rounded-lg border border-gray-200">
                    <p class="text-5xl mb-3">🛍️</p>
                    <p class="text-gray-500 text-lg">Belum ada produk yang terdaftar.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </main>

</x-public-layout>
