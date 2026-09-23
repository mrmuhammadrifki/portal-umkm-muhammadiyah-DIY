<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Parameter Kategori Skala Usaha
            </h2>
            <a href="{{ route('admin.kategori.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-800 flex items-center gap-1">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="p-8 bg-white shadow-lg sm:rounded-2xl border border-gray-100">

                <div class="mb-6 pb-4 border-b border-gray-100">
                    <span class="inline-block px-3 py-1 bg-brand-50 text-brand-700 text-xs font-bold rounded-full uppercase tracking-wider mb-2">
                        Klasifikasi Skala Usaha (PP 7/2021)
                    </span>
                    <h3 class="text-xl font-bold text-gray-900">Kategori: {{ $category->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Ubah rentang pendapatan bulanan atau deskripsi acuan untuk kategori ini.</p>
                </div>

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-200">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.kategori.update', $category) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kategori Skala Usaha</label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Batas Minimal Pendapatan (Rp/Bulan)</label>
                            <input type="number" name="min_revenue" value="{{ old('min_revenue', $category->min_revenue) }}" min="0" placeholder="0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                            <p class="text-xs text-gray-400 mt-1">Isi 0 jika tidak ada batas bawah.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Batas Maksimal Pendapatan (Rp/Bulan)</label>
                            <input type="number" name="max_revenue" value="{{ old('max_revenue', $category->max_revenue) }}" min="0" placeholder="Kosongkan jika tak terbatas" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ada batas atas.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi / Catatan Acuan Regulasi</label>
                        <textarea name="description" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand" placeholder="Keterangan singkat acuan penggolongan...">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.kategori.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white text-sm font-bold shadow-md transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
