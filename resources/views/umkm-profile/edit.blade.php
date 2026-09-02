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
                    x-data="{ submitting: false, logoPreview: @js($profile->logo_path ? asset('storage/' . $profile->logo_path) : null) }"
                    @submit="submitting = true"
                    action="{{ route('umkm-profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8"
                >
                    @csrf
                    @method('PUT')

                    <!-- SECTION: Informasi Usaha -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Informasi Usaha</legend>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Usaha</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pemilik</label>
                            <input type="text" name="owner_name" value="{{ old('owner_name', $profile->owner_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi Usaha</label>
                            <textarea name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">{{ old('description', $profile->description) }}</textarea>
                        </div>
                    </fieldset>

                    <!-- SECTION: Lokasi -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Lokasi</legend>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">{{ old('address', $profile->address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                <input type="text" name="kecamatan" value="{{ old('kecamatan', $profile->kecamatan) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                                <input type="text" name="kabupaten_kota" value="{{ old('kabupaten_kota', $profile->kabupaten_kota) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECTION: Kontak & Media Sosial -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Kontak & Media Sosial</legend>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $profile->whatsapp) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instagram (opsional)</label>
                            <input type="text" name="instagram" value="{{ old('instagram', $profile->instagram) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                        </div>
                    </fieldset>

                    <!-- SECTION: Legalitas & Afiliasi -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 w-full">Legalitas & Afiliasi</legend>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIB (opsional)</label>
                            <input type="text" name="nib" value="{{ old('nib', $profile->nib) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Afiliasi</label>
                            <select name="affiliation_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
                                <option value="afiliasi" {{ old('affiliation_status', $profile->affiliation_status) == 'afiliasi' ? 'selected' : '' }}>Afiliasi</option>
                                <option value="non_afiliasi" {{ old('affiliation_status', $profile->affiliation_status) == 'non_afiliasi' ? 'selected' : '' }}>Non-Afiliasi</option>
                            </select>
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
