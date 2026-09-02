<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produk Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="flex justify-end px-4 sm:px-0">
                <a href="{{ route('produk.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                    + Tambah Produk
                </a>
            </div>

            <!-- MOBILE: card list -->
            <div class="sm:hidden space-y-3 px-4">
                @forelse($products as $product)
                    <div class="bg-white shadow rounded-lg p-4 border border-gray-100">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
                            </div>
                            <x-status-badge :status="$product->status" />
                        </div>
                        <div class="flex space-x-2 mt-3">
                            <a href="{{ route('produk.edit', $product) }}" class="flex-1 text-center bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 rounded text-xs">
                                Edit
                            </a>
                            <form action="{{ route('produk.destroy', $product) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 rounded text-xs">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-lg border border-gray-200">
                        <p class="text-4xl mb-2">📦</p>
                        <p class="text-gray-500 mb-4">Belum ada produk. Tambah produk pertama Anda!</p>
                        <a href="{{ route('produk.create') }}" class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded text-sm">
                            + Tambah Produk
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- DESKTOP: table -->
            <div class="hidden sm:block p-6 bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$product->status" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <a href="{{ route('produk.edit', $product) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Edit
                                    </a>
                                    <form action="{{ route('produk.destroy', $product) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-4xl mb-2">📦</p>
                                    <p class="mb-4">Belum ada produk.</p>
                                    <a href="{{ route('produk.create') }}" class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        + Tambah Produk Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
