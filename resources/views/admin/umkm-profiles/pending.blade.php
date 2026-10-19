<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Persetujuan Profil UMKM
        </h2>
    </x-slot>

    @php
        // Setelah validasi alasan gagal, buka kembali modal untuk profil yang sama
        $reopenId = old('reject_id');
        $reopen   = $reopenId ? $profiles->firstWhere('id', (int) $reopenId) : null;
    @endphp

    <div class="py-12"
         x-data="{
            open: @js((bool) $reopen),
            action: @js($reopen ? route('admin.umkm-profiles.reject', $reopen) : ''),
            name: @js($reopen?->business_name ?? ''),
            id: @js($reopen?->id ?? ''),
            show(action, name, id) { this.action = action; this.name = name; this.id = id; this.open = true; }
         }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div x-data="{ visible: true }" x-init="setTimeout(() => visible = false, 5000)"
                     x-show="visible" x-transition.opacity.duration.300ms
                     class="p-4 bg-green-50 text-green-700 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            {{-- ===================== MENUNGGU PERSETUJUAN ===================== --}}
            <!-- MOBILE: card list -->
            <div class="sm:hidden space-y-3 px-4">
                @forelse($profiles as $profile)
                    <div class="bg-white shadow rounded-lg p-4 border border-gray-100">
                        <p class="font-bold text-gray-900">{{ $profile->business_name }}</p>
                        <p class="text-sm text-gray-500 mb-3">{{ $profile->owner_name }} · {{ $profile->whatsapp }}</p>
                        <div class="flex space-x-2">
                            <form action="{{ route('admin.umkm-profiles.approve', $profile) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded text-xs">
                                    Setujui
                                </button>
                            </form>
                            <button type="button"
                                    @click="show(@js(route('admin.umkm-profiles.reject', $profile)), @js($profile->business_name), @js($profile->id))"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded text-xs">
                                Tolak
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-lg border border-gray-200">
                        <p class="text-4xl mb-2">🎉</p>
                        <p class="text-gray-500">Semua profil sudah dimoderasi — tidak ada yang menunggu persetujuan.</p>
                    </div>
                @endforelse
                <div class="mt-4">{{ $profiles->links() }}</div>
            </div>

            <!-- DESKTOP: table -->
            <div class="hidden sm:block p-6 bg-white shadow sm:rounded-lg overflow-x-auto">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Menunggu Persetujuan</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Usaha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemilik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WhatsApp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($profiles as $profile)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->business_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->owner_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->whatsapp }}</td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <form action="{{ route('admin.umkm-profiles.approve', $profile) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Setujui
                                        </button>
                                    </form>
                                    <button type="button"
                                            @click="show(@js(route('admin.umkm-profiles.reject', $profile)), @js($profile->business_name), @js($profile->id))"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Tolak
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-4xl mb-2">🎉</p>
                                    <p>Semua profil sudah dimoderasi — tidak ada yang menunggu persetujuan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $profiles->links() }}
                </div>
            </div>

            {{-- ===================== HISTORI KEPUTUSAN ===================== --}}
            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Histori Persetujuan &amp; Penolakan</h3>
                    <form method="GET" action="{{ route('admin.umkm-profiles.pending') }}">
                        <select name="history_status" onchange="this.form.submit()"
                                class="border-gray-300 rounded-md shadow-sm text-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">Semua keputusan</option>
                            <option value="approved" @selected(request('history_status') === 'approved')>Disetujui</option>
                            <option value="rejected" @selected(request('history_status') === 'rejected')>Ditolak</option>
                        </select>
                    </form>
                </div>

                <!-- MOBILE -->
                <div class="sm:hidden space-y-3">
                    @forelse($history as $item)
                        <div class="border border-gray-100 rounded-lg p-4">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-bold text-gray-900">{{ $item->business_name }}</p>
                                <x-status-badge :status="$item->status" :label="$item->status === 'approved' ? 'Disetujui' : 'Ditolak'" />
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $item->reviewed_at->translatedFormat('d M Y H:i') }} · oleh {{ $item->reviewer?->name ?? '-' }}
                            </p>
                            @if($item->status === 'rejected' && $item->rejection_reason)
                                <p class="mt-2 text-sm text-red-700 bg-red-50 border border-red-100 rounded p-2">
                                    <span class="font-semibold">Alasan:</span> {{ $item->rejection_reason }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6">Belum ada histori keputusan.</p>
                    @endforelse
                </div>

                <!-- DESKTOP -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Usaha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keputusan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan Penolakan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ditinjau Oleh</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($history as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $item->business_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->owner_name }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$item->status" :label="$item->status === 'approved' ? 'Disetujui' : 'Ditolak'" />
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 max-w-md whitespace-pre-line">{{ $item->status === 'rejected' ? ($item->rejection_reason ?: '-') : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $item->reviewer?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $item->reviewed_at->translatedFormat('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada histori keputusan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $history->links() }}</div>
            </div>

        </div>

        {{-- ===================== MODAL ALASAN PENOLAKAN ===================== --}}
        <div x-show="open" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4"
             @keydown.escape.window="open = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6" @click.outside="open = false">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Pengajuan</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Anda menolak <span class="font-semibold" x-text="name"></span>. Alasan ini akan tersimpan di histori dan ditampilkan kepada pemilik usaha.
                </p>

                <form :action="action" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="reject_id" :value="id">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Alasan penolakan <span class="text-red-600">*</span></label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" required minlength="10" maxlength="1000"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-brand-500 focus:ring-brand-500"
                              placeholder="Contoh: Data alamat dan nomor WhatsApp tidak dapat diverifikasi.">{{ old('rejection_reason') }}</textarea>
                    <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />

                    <div class="mt-5 flex justify-end space-x-2">
                        <button type="button" @click="open = false"
                                class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded">
                            Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
