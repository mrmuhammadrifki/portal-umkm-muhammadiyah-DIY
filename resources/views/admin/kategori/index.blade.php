<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Kategori</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 text-red-700 rounded-md border border-red-200">{{ session('error') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('admin.kategori.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                    + Tambah Kategori
                </a>
            </div>

            <!-- MOBILE: card list -->
            <div class="sm:hidden space-y-3">
                @forelse($categories as $category)
                    <div class="bg-white shadow rounded-lg p-4 border border-gray-100 flex items-center justify-between gap-2">
                        <div>
                            <p class="font-bold text-gray-900">{{ $category->name }}</p>
                            <p class="text-sm text-gray-500">{{ $category->slug }}</p>
                        </div>
                        <div class="flex space-x-2 flex-shrink-0">
                            <a href="{{ route('admin.kategori.edit', $category) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                Edit
                            </a>
                            <form action="{{ route('admin.kategori.destroy', $category) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-lg border border-gray-200">
                        <p class="text-gray-500">Belum ada kategori.</p>
                    </div>
                @endforelse
            </div>

            <!-- DESKTOP: table -->
            <div class="hidden sm:block p-6 bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $category->slug }}</td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <a href="{{ route('admin.kategori.edit', $category) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.kategori.destroy', $category) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
