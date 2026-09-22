<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profil Usaha
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md border border-green-200">{{ session('success') }}</div>
                @endif

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
                    x-data="{
                        submitting: false,
                        logoPreview: @js($profile->logo_path ? asset('storage/' . $profile->logo_path) : null),
                        hasHalal: @js((string) old('has_halal_certificate', $profile->has_halal_certificate ? '1' : '0'))
                    }"
                    @submit="submitting = true"
                    action="{{ route('umkm-profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8"
                >
                    @csrf
                    @method('PUT')

                    <!-- SECTION: Informasi Usaha -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Informasi Usaha</legend>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Usaha <span class="text-red-500">*</span></label>
                            <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pemilik <span class="text-red-500">*</span></label>
                            <input type="text" name="owner_name" value="{{ old('owner_name', $profile->owner_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tahun Pendirian UMKM</label>
                                <input
                                    type="number"
                                    name="established_year"
                                    value="{{ old('established_year', $profile->established_year) }}"
                                    min="1900"
                                    max="{{ date('Y') }}"
                                    placeholder="Contoh: 2018"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                                >
                                <p class="text-xs text-gray-500 mt-1">Tahun awal usaha didirikan (antara 1900 - {{ date('Y') }}).</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah Karyawan</label>
                                <input
                                    type="number"
                                    name="employee_count"
                                    value="{{ old('employee_count', $profile->employee_count) }}"
                                    min="0"
                                    placeholder="Contoh: 5"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                                >
                                <p class="text-xs text-gray-500 mt-1">Total tenaga kerja/karyawan saat ini.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Rata-rata Pendapatan per Bulan (Rp)
                            </label>
                            <input
                                type="number"
                                name="monthly_revenue"
                                value="{{ old('monthly_revenue', $profile->monthly_revenue) }}"
                                min="0"
                                step="100000"
                                placeholder="Contoh: 15000000"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                            >
                            <p class="text-xs text-gray-500 mt-1">
                                Digunakan untuk menentukan klasifikasi usaha Anda:
                                <strong>Mikro</strong> (&lt;Rp25jt) ·
                                <strong>Kecil</strong> (Rp25jt–208jt) ·
                                <strong>Menengah</strong> (&gt;Rp208jt).
                                Sesuai PP No. 7 Tahun 2021.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi Usaha</label>
                            <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand" placeholder="Jelaskan produk, keunggulan, dan profil singkat usaha Anda">{{ old('description', $profile->description) }}</textarea>
                        </div>
                    </fieldset>

                    <!-- SECTION: Lokasi -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Lokasi Usaha</legend>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea name="address" rows="2" placeholder="Nama jalan, nomor bangunan, RT/RW..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">{{ old('address', $profile->address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kelurahan / Desa</label>
                                <input
                                    type="text"
                                    name="kelurahan"
                                    value="{{ old('kelurahan', $profile->kelurahan) }}"
                                    placeholder="Contoh: Ngupasan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                <input
                                    type="text"
                                    name="kecamatan"
                                    value="{{ old('kecamatan', $profile->kecamatan) }}"
                                    placeholder="Contoh: Gondomanan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kabupaten / Kota</label>
                                <input
                                    type="text"
                                    name="kabupaten_kota"
                                    value="{{ old('kabupaten_kota', $profile->kabupaten_kota) }}"
                                    placeholder="Contoh: Kota Yogyakarta"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                                >
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECTION: Kontak & Media Sosial -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Kontak & Media Sosial</legend>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp <span class="text-red-500">*</span></label>
                            <input
                                type="number"
                                name="whatsapp"
                                value="{{ old('whatsapp', $profile->whatsapp) }}"
                                required
                                placeholder="Contoh: 081234567890"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                            >
                            <p class="text-xs text-gray-500 mt-1">Hanya dapat diisi dengan angka (nomor telepon WhatsApp aktif).</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instagram (opsional)</label>
                            <input
                                type="text"
                                name="instagram"
                                value="{{ old('instagram', $profile->instagram) }}"
                                placeholder="@nama_akun"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                            >
                        </div>
                    </fieldset>

                    <!-- SECTION: Legalitas & Sertifikasi Halal -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Legalitas & Sertifikasi Halal</legend>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIB (Nomor Induk Berusaha - opsional)</label>
                            <input
                                type="text"
                                name="nib"
                                value="{{ old('nib', $profile->nib) }}"
                                placeholder="Contoh: 9120001234567"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                            >
                        </div>

                        <!-- Sertifikasi Halal -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-3">
                            <label class="block text-sm font-semibold text-gray-900">
                                Sertifikasi Halal <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label
                                    class="flex items-center gap-3 p-3 rounded-md border cursor-pointer transition"
                                    :class="hasHalal === '1' ? 'bg-green-50 border-green-500 text-green-900 font-semibold' : 'bg-white border-gray-300 text-gray-700'"
                                >
                                    <input
                                        type="radio"
                                        name="has_halal_certificate"
                                        value="1"
                                        x-model="hasHalal"
                                        class="text-brand focus:ring-brand"
                                    >
                                    <span>Sudah Bersertifikat Halal</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-3 rounded-md border cursor-pointer transition"
                                    :class="hasHalal === '0' ? 'bg-gray-100 border-gray-400 text-gray-900 font-semibold' : 'bg-white border-gray-300 text-gray-700'"
                                >
                                    <input
                                        type="radio"
                                        name="has_halal_certificate"
                                        value="0"
                                        x-model="hasHalal"
                                        class="text-brand focus:ring-brand"
                                    >
                                    <span>Belum Bersertifikat Halal</span>
                                </label>
                            </div>

                            <!-- Input Tahun Penerbitan Sertifikasi Halal (Kondisional saat sudah halal) -->
                            <div x-show="hasHalal === '1'" x-transition class="pt-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Tahun Penerbitan Sertifikasi Halal <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="halal_certificate_year"
                                    value="{{ old('halal_certificate_year', $profile->halal_certificate_year) }}"
                                    :required="hasHalal === '1'"
                                    min="1980"
                                    max="{{ date('Y') }}"
                                    placeholder="Contoh: 2022"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-white"
                                >
                                <p class="text-xs text-gray-500 mt-1">Wajib diisi bila sudah memiliki sertifikasi halal.</p>
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECTION: Pelatihan & Pendampingan Kewirausahaan -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Pembinaan Kewirausahaan</legend>

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Pernah Mengikuti Pelatihan dan Pendampingan Kewirausahaan UMKM? <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Pernah mengikuti pelatihan atau pendampingan yang diselenggarakan oleh LP UMKM Muhammadiyah, pemerintah, atau lembaga lainnya.</p>

                            @php
                                $attended = old('has_attended_training', $profile->has_attended_training ?? 'tidak');
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 p-3 rounded-md border bg-white border-gray-300 hover:border-brand cursor-pointer transition">
                                    <input
                                        type="radio"
                                        name="has_attended_training"
                                        value="ya"
                                        {{ $attended === 'ya' ? 'checked' : '' }}
                                        required
                                        class="text-brand focus:ring-brand"
                                    >
                                    <span class="text-sm text-gray-800 font-medium">Ya, Pernah</span>
                                </label>

                                <label class="flex items-center gap-3 p-3 rounded-md border bg-white border-gray-300 hover:border-brand cursor-pointer transition">
                                    <input
                                        type="radio"
                                        name="has_attended_training"
                                        value="tidak"
                                        {{ $attended === 'tidak' ? 'checked' : '' }}
                                        required
                                        class="text-brand focus:ring-brand"
                                    >
                                    <span class="text-sm text-gray-800 font-medium">Tidak / Belum Pernah</span>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECTION: Logo -->
                    <fieldset class="space-y-3">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Logo Usaha</legend>

                        <div class="flex items-center gap-4">
                            <img
                                x-show="logoPreview"
                                :src="logoPreview"
                                alt="Preview logo"
                                class="w-20 h-20 object-cover rounded-lg border border-gray-200 flex-shrink-0"
                            >
                            <div class="flex-1">
                                <input
                                    type="file" name="logo" accept="image/jpeg,image/png"
                                    @change="logoPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : logoPreview"
                                    class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-dark hover:file:bg-brand-100"
                                >
                                <p class="text-xs text-gray-500 mt-1">Format jpg/png, maksimal 2MB.</p>
                            </div>
                        </div>
                    </fieldset>

                    <div class="flex space-x-2 pt-2 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="bg-brand-600 hover:bg-brand-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold py-2 px-4 rounded shadow-sm inline-flex items-center gap-2 mt-4"
                        >
                            <svg x-show="submitting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="submitting ? 'Menyimpan...' : 'Simpan Profil'"></span>
                        </button>
                        <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mt-4">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
