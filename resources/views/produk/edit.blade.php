<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Produk: {{ $product->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg" x-data="{
                submitting: false,
                files: [],
                addFiles(fileList) {
                    Array.from(fileList).forEach(f => this.files.push(f));
                    this.syncInput();
                },
                removeFile(index) {
                    this.files.splice(index, 1);
                    this.syncInput();
                },
                syncInput() {
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f));
                    this.$refs.imagesInput.files = dt.files;
                },
            }">

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-md border border-red-200">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    @submit="submitting = true"
                    action="{{ route('produk.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4"
                >
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    @if($product->images->count())
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto Saat Ini</label>
                            <p class="text-xs text-gray-500 mt-1 mb-2">Foto pertama = sampul. Klik &times; untuk hapus (tombol hapus di bawah form ini).</p>
                            <div class="flex flex-wrap gap-3">
                                @foreach($product->images as $image)
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Foto produk" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                        <button
                                            type="button"
                                            @click="if (confirm('Hapus foto ini?')) $refs.deleteForm{{ $image->id }}.submit()"
                                            class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow"
                                        >&times;</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tambah Foto Baru (bisa pilih beberapa kali, maks 6 total — jpg/png, maks 2MB per foto)</label>
                        <input
                            type="file" x-ref="imagesInput" name="images[]" accept="image/jpeg,image/png" multiple
                            @change="addFiles($event.target.files)"
                            class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-dark hover:file:bg-brand-100"
                        >
                        <p class="text-xs text-gray-500 mt-1">Bisa klik "Choose Files" berkali-kali — foto sebelumnya tidak akan hilang.</p>

                        <div x-show="files.length" class="flex flex-wrap gap-2 mt-3">
                            <template x-for="(file, i) in files" :key="i">
                                <div class="relative">
                                    <img :src="URL.createObjectURL(file)" alt="Preview" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                    <button
                                        type="button" @click="removeFile(i)"
                                        class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold shadow"
                                    >&times;</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex space-x-2 pt-2">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="bg-brand-600 hover:bg-brand-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold py-2 px-4 rounded shadow-sm inline-flex items-center gap-2"
                        >
                            <svg x-show="submitting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="submitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                        <a href="{{ route('produk.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                            Batal
                        </a>
                    </div>
                </form>

                {{-- Form hapus foto — sengaja DI LUAR <form> edit produk di atas, karena
                     HTML tidak boleh punya <form> bersarang di dalam <form> lain (browser
                     akan menutup form terluar lebih awal saat parsing kalau dipaksa nested,
                     menyebabkan submit form utama gagal/nyangkut). Tetap di dalam x-data
                     yang sama supaya $refs.deleteForm{id} masih bisa diakses tombol × di atas. --}}
                @foreach($product->images as $image)
                    <form x-ref="deleteForm{{ $image->id }}" action="{{ route('produk.image.destroy', [$product, $image]) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
