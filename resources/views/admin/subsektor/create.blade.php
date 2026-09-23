<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Subsektor Ekonomi Kreatif (EKRAF)
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
                    <span class="inline-block px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full uppercase tracking-wider mb-2">
                        Standar Kemenparekraf / Baparekraf RI
                    </span>
                    <h3 class="text-xl font-bold text-gray-900">Form Tambah Subsektor Baru</h3>
                    <p class="text-sm text-gray-500 mt-1">Tambahkan klasifikasi bidang usaha ekonomi kreatif binaan LP UMKM.</p>
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

                <form action="{{ route('admin.subsektor.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Nama Subsektor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Seni Rupa, Desain Produk..." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Ikon / Emoji Representatif
                        </label>
                        <input type="text" name="icon" value="{{ old('icon', '🏷️') }}" maxlength="50" placeholder="Pilih emoji, contoh: 🎨, 🍲, 👗, 📱" class="block w-full sm:w-1/3 rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand text-xl">
                        <p class="text-xs text-gray-400 mt-1">Gunakan karakter emoji untuk visualisasi menarik di katalog dan kartu informasi.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Deskripsi Cakupan Subsektor
                        </label>
                        <textarea name="description" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand" placeholder="Jelaskan jenis produk atau cakupan industri kreatif ini...">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.kategori.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white text-sm font-bold shadow-md transition">
                            + Tambah Subsektor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
