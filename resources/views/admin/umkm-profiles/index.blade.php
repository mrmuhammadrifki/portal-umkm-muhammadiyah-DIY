<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Semua UMKM (Moderasi)</h2>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.umkm.import.template') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-emerald-600 text-emerald-700 hover:bg-emerald-50 font-bold text-xs rounded-lg shadow-sm transition"
                    title="Unduh format template Excel untuk data UMKM"
                >
                    <span>📥</span> Unduh Template Excel
                </a>
                <button
                    type="button"
                    x-data
                    @click="$dispatch('open-import-modal')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg shadow-sm transition cursor-pointer"
                >
                    <span>📤</span> Import Excel UMKM
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showImportModal: false }" @open-import-modal.window="showImportModal = true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md border border-green-200 text-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-50 text-red-700 rounded-md border border-red-200 text-sm flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="p-4 bg-amber-50 text-amber-800 rounded-md border border-amber-200 text-xs space-y-1">
                    <p class="font-bold flex items-center gap-1">
                        <span>ℹ️</span> Catatan baris data yang dilewati saat proses import:
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 max-h-40 overflow-y-auto">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg">
                <form action="{{ route('admin.umkm-profiles.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cari Nama Usaha</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama UMKM..." class="w-full text-sm rounded-md border-gray-300 shadow-sm p-2 border">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status Moderasi</label>
                        <select name="status" class="w-full text-sm rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pelatihan & Pendampingan</label>
                        <select name="has_attended_training" class="w-full text-sm rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">-- Semua --</option>
                            <option value="ya" {{ request('has_attended_training') == 'ya' ? 'selected' : '' }}>🎓 Pernah Ikut (Ya)</option>
                            <option value="tidak" {{ request('has_attended_training') == 'tidak' ? 'selected' : '' }}>Belum Pernah (Tidak)</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm flex-1 h-[38px]">
                            Cari & Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'has_attended_training']))
                            <a href="{{ route('admin.umkm-profiles.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-3 rounded text-sm h-[38px] flex items-center justify-center border border-gray-300" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- MOBILE: card list -->
            <div class="sm:hidden space-y-3 px-4">
                @forelse($profiles as $profile)
                    <div class="bg-white shadow rounded-lg p-4 border border-gray-100">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="font-bold text-gray-900">{{ $profile->business_name }}</p>
                                <p class="text-sm text-gray-500">{{ $profile->owner_name }} · {{ $profile->whatsapp }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <x-status-badge :status="$profile->status" :label="ucfirst($profile->status)" />
                            <x-status-badge :status="$profile->user->is_active ? 'account_active' : 'account_suspended'" />
                            @if($profile->has_attended_training === 'ya')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    🎓 Pernah Pelatihan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                    Belum Pelatihan
                                </span>
                            @endif
                        </div>
                        @if($profile->user->is_active)
                            <form action="{{ route('admin.umkm-profiles.suspend', $profile) }}" method="POST" onsubmit="return confirm('Yakin suspend akun UMKM ini? Login akan diblokir dan hilang dari katalog publik.')">
                                @csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded text-xs">
                                    Suspend
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.umkm-profiles.reactivate', $profile) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded text-xs">
                                    Aktifkan
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-lg border border-gray-200">
                        <p class="text-gray-500">Tidak ada data.</p>
                    </div>
                @endforelse
                <div class="mt-4">{{ $profiles->links() }}</div>
            </div>

            <!-- DESKTOP: table -->
            <div class="hidden sm:block p-6 bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Usaha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemilik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WhatsApp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelatihan Kewirausahaan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($profiles as $profile)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $profile->business_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->owner_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->whatsapp }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($profile->has_attended_training === 'ya')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                            🎓 Pernah
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$profile->status" :label="ucfirst($profile->status)" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$profile->user->is_active ? 'account_active' : 'account_suspended'" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($profile->user->is_active)
                                        <form action="{{ route('admin.umkm-profiles.suspend', $profile) }}" method="POST" onsubmit="return confirm('Yakin suspend akun UMKM ini? Login akan diblokir dan hilang dari katalog publik.')">
                                            @csrf
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Suspend
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.umkm-profiles.reactivate', $profile) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $profiles->links() }}</div>
            </div>

        </div>

        <!-- MODAL IMPORT EXCEL DATA UMKM -->
        <div
            x-show="showImportModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <!-- BACKDROP OVERLAY -->
            <div
                x-show="showImportModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                @click="showImportModal = false"
            ></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="showImportModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100"
                    x-data="{ isUploading: false, fileName: '' }"
                >
                    <!-- MODAL HEADER -->
                    <div class="bg-gradient-to-r from-emerald-800 to-emerald-700 px-6 py-4 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📊</span>
                            <h3 class="text-base font-bold" id="modal-title">
                                Import Data UMKM Massal (Excel)
                            </h3>
                        </div>
                        <button
                            type="button"
                            @click="showImportModal = false"
                            class="text-white/80 hover:text-white text-xl font-bold leading-none p-1"
                        >
                            &times;
                        </button>
                    </div>

                    <!-- MODAL BODY -->
                    <form
                        action="{{ route('admin.umkm.import') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        @submit="isUploading = true"
                        class="p-6 space-y-5"
                    >
                        @csrf

                        <!-- PETUNJUK & TEMPLATE LINK -->
                        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-200 text-xs text-emerald-950 space-y-2">
                            <p class="font-bold flex items-center gap-1.5 text-emerald-900">
                                <span>💡</span> Panduan Import Data:
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-emerald-800">
                                <li>Pastikan file Anda berformat <strong>.xlsx</strong> atau <strong>.xls</strong>.</li>
                                <li>Kolom wajib diisi: <strong>Nama Usaha, Nama Pemilik, Email Akun, dan WhatsApp</strong>.</li>
                                <li>Email akan digunakan sebagai akun login pemilik UMKM.</li>
                            </ul>
                            <div class="pt-2.5 border-t border-emerald-200/60 flex flex-wrap items-center justify-between gap-2">
                                <span class="text-[11px] text-emerald-800 font-semibold">File Bantuan & Uji Coba:</span>
                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('admin.umkm.import.sample') }}"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-900 bg-emerald-300/80 hover:bg-emerald-300 px-2.5 py-1 rounded transition shadow-xs"
                                        title="Unduh file Excel berisi 10 data UMKM nyata DIY siap uji coba langsung"
                                    >
                                        <span>📊</span> Unduh Data Uji Coba (.xlsx)
                                    </a>
                                    <a
                                        href="{{ route('admin.umkm.import.template') }}"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-900 bg-emerald-200/70 hover:bg-emerald-200 px-2.5 py-1 rounded transition"
                                        title="Unduh format template Excel"
                                    >
                                        <span>📥</span> Template (.xlsx)
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- INPUT FILE -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">
                                Pilih File Excel (.xlsx / .xls) <span class="text-red-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-emerald-500 transition-colors bg-gray-50/50">
                                <span class="text-3xl block mb-2">📁</span>
                                <input
                                    type="file"
                                    name="file"
                                    required
                                    accept=".xlsx, .xls"
                                    @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer"
                                >
                                <p x-show="fileName" class="text-xs font-bold text-emerald-700 mt-2 truncate" x-text="'File terpilih: ' + fileName"></p>
                                <p x-show="!fileName" class="text-[11px] text-gray-400 mt-1">Maksimal ukuran file: 10 MB</p>
                            </div>
                        </div>

                        <!-- MODAL FOOTER BUTTONS -->
                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                            <button
                                type="button"
                                @click="showImportModal = false"
                                :disabled="isUploading"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="isUploading"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition disabled:opacity-50 cursor-pointer"
                            >
                                <span x-show="!isUploading">🚀 Mulai Import Data</span>
                                <span x-show="isUploading" class="inline-flex items-center gap-1.5">
                                    <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Mengimpor Data...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
