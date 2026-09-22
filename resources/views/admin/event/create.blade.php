<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Agenda Baru
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div
                x-data="{
                    submitting: false,
                    previewUrl: null,
                    handleFileSelect(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.previewUrl = URL.createObjectURL(file);
                        }
                    }
                }"
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8"
            >
                <form
                    action="{{ route('admin.event.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    @submit="submitting = true"
                    class="space-y-6"
                >
                    @csrf

                    <!-- FORM CONTENT -->
                    <div class="space-y-6">

                        <!-- a. Nama Event & Tipe Kegiatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <x-input-label for="title" value="Nama Event *" />
                                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus placeholder="Contoh: Klinik Halal UMKM PWM DIY 2025" />
                                <x-input-error class="mt-2" :messages="$errors->get('title')" />
                            </div>

                            <div>
                                <x-input-label for="type" value="Tipe Kegiatan *" />
                                <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand text-sm h-[42px]">
                                    <option value="pelatihan" {{ old('type') === 'pelatihan' ? 'selected' : '' }}>🎓 Pelatihan</option>
                                    <option value="pendampingan" {{ old('type') === 'pendampingan' ? 'selected' : '' }}>🤝 Pendampingan</option>
                                    <option value="workshop" {{ old('type') === 'workshop' ? 'selected' : '' }}>💡 Workshop</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('type')" />
                            </div>
                        </div>

                        <!-- b. Tanggal Pelatihan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="date_start" value="Tanggal Pelatihan (Mulai) *" />
                                <input id="date_start" name="date_start" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand text-sm" value="{{ old('date_start') }}" required />
                                <p class="text-[11px] text-gray-500 mt-1">Pilih tanggal dan jam dimulainya pelatihan/kegiatan.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('date_start')" />
                            </div>

                            <div>
                                <x-input-label for="date_end" value="Tanggal Pelatihan (Selesai - Opsional)" />
                                <input id="date_end" name="date_end" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand text-sm" value="{{ old('date_end') }}" />
                                <p class="text-[11px] text-gray-500 mt-1">Kosongkan jika acara hanya berlangsung satu sesi / satu hari.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('date_end')" />
                            </div>
                        </div>

                        <!-- c. Tempat Pelatihan & e. Contact Person -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="location" value="Tempat Pelatihan *" />
                                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location')" required placeholder="Contoh: Aula Gedung PWM D.I. Yogyakarta, Jl. Gedongkuning No. 130 B" />
                                <x-input-error class="mt-2" :messages="$errors->get('location')" />
                            </div>

                            <div>
                                <x-input-label for="contact_person" value="Contact Person (Narahubung)" />
                                <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person')" placeholder="Contoh: Ibu Amalya | 082280126691" />
                                <p class="text-[11px] text-gray-500 mt-1">Format bebas: Nama & Nomor HP/WhatsApp.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                            </div>
                        </div>

                        <!-- d. Deskripsi Event -->
                        <div>
                            <x-input-label for="description" value="Deskripsi Event *" />
                            <p class="text-xs text-gray-500 mb-1.5">Tuliskan penjelasan kegiatan, jadwal/rundown penting, syarat peserta, dan keuntungan yang diperoleh.</p>
                            <textarea id="description" name="description" rows="10" required placeholder="Tulis deskripsi lengkap kegiatan..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand text-sm font-sans leading-relaxed">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Poster / Banner & Publikasi -->
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                            <div>
                                <x-input-label for="image" value="Banner / Poster Kegiatan (Opsional, Maks. 2MB)" />
                                <input
                                    id="image"
                                    name="image"
                                    type="file"
                                    accept="image/jpeg,image/png,image/jpg"
                                    @change="handleFileSelect"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand hover:file:bg-brand-100 cursor-pointer"
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('image')" />

                                <div x-show="previewUrl" class="mt-3" style="display: none;">
                                    <p class="text-xs font-semibold text-gray-600 mb-1">Pratinjau Banner:</p>
                                    <img :src="previewUrl" alt="Pratinjau Banner" class="w-full max-w-md h-auto rounded-lg shadow border border-gray-200">
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <input
                                    id="is_published"
                                    name="is_published"
                                    type="checkbox"
                                    value="1"
                                    {{ old('is_published', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand"
                                />
                                <label for="is_published" class="text-sm font-medium text-gray-700 cursor-pointer">
                                    Langsung publikasikan ke katalog agenda publik
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- BUTTONS -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.event.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="inline-flex items-center gap-2 px-6 py-2 bg-brand text-white font-bold text-sm rounded-lg hover:bg-brand-dark shadow transition disabled:opacity-50"
                        >
                            <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="submitting ? 'Menyimpan...' : 'Simpan Agenda'">Simpan Agenda</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
