<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="text-xs text-gray-500 mb-1 flex items-center gap-1.5">
                    <a href="{{ route('admin.event.index') }}" class="hover:underline text-brand">Kelola Agenda</a>
                    <span>&rsaquo;</span>
                    <span>Daftar Peserta</span>
                </nav>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    Peserta: {{ $event->title }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.event.registrations.export', $event) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 text-white font-bold text-xs rounded-lg hover:bg-emerald-800 shadow-sm transition"
                >
                    <span>📊</span> Unduh Data Peserta (CSV)
                </a>
                <a
                    href="{{ route('admin.event.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-200 text-gray-700 font-bold text-xs rounded-lg hover:bg-gray-300 transition"
                >
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- FLASH MESSAGE -->
            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 shadow-sm flex items-center gap-2 text-sm">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <!-- STATS CARDS -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Terdaftar</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">
                        {{ $stats['total'] }}
                        @if($event->quota)
                            <span class="text-xs font-normal text-gray-500">/ {{ $event->quota }} Kuota</span>
                        @endif
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-semibold text-green-600 uppercase">Terkonfirmasi</p>
                    <p class="text-2xl font-black text-green-700 mt-1">{{ $stats['confirmed'] }}</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-semibold text-blue-600 uppercase">Hadir</p>
                    <p class="text-2xl font-black text-blue-700 mt-1">{{ $stats['attended'] }}</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-semibold text-red-600 uppercase">Dibatalkan</p>
                    <p class="text-2xl font-black text-red-700 mt-1">{{ $stats['cancelled'] }}</p>
                </div>
            </div>

            <!-- SEARCH & FILTER -->
            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200">
                <form action="{{ route('admin.event.registrations', $event) }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cari Peserta</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, email, no WA, atau usaha..."
                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2 border"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status Kehadiran</label>
                        <select name="status" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2 border bg-white">
                            <option value="">Semua Status</option>
                            <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Terdaftar</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                            <option value="attended" {{ request('status') === 'attended' ? 'selected' : '' }}>Hadir</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-2 px-4 rounded-md text-sm shadow-sm transition h-[40px]">
                            Filter
                        </button>
                        @if(request('search') || request('status'))
                            <a href="{{ route('admin.event.registrations', $event) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-md text-sm shadow-sm transition flex items-center justify-center h-[40px]">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABLE VIEW FOR DESKTOP -->
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-5 py-3 text-center">Nama & Email</th>
                                <th class="px-5 py-3 text-center">WhatsApp</th>
                                <th class="px-5 py-3 text-center">Usaha / Instansi</th>
                                <th class="px-5 py-3 text-center">Domisili</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-center">Waktu Daftar</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @forelse($registrations as $reg)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-gray-900">{{ $reg->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $reg->email }}</p>
                                        @if($reg->notes)
                                            <p class="text-[11px] text-gray-600 bg-gray-100 p-1.5 rounded mt-1 max-w-xs italic">
                                                "{{ $reg->notes }}"
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs">
                                        @if($reg->clean_whatsapp)
                                            <a
                                                href="https://wa.me/{{ $reg->clean_whatsapp }}?text={{ urlencode('Halo ' . $reg->name . ', panitia LP UMKM PWM DIY mengonfirmasi pendaftaran Anda untuk kegiatan: ' . $event->title) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 font-semibold text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 px-2 py-1 rounded"
                                            >
                                                <span>💬</span> {{ $reg->phone }}
                                            </a>
                                        @else
                                            <span class="text-gray-700">{{ $reg->phone }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-700">
                                        <p class="font-medium text-gray-900">{{ $reg->institution_or_business ?: '-' }}</p>
                                        @if($reg->subsector_or_category)
                                            <span class="inline-block text-[11px] text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded mt-0.5">
                                                {{ $reg->subsector_or_category }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-700">
                                        {{ $reg->city }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <form action="{{ route('admin.event.registrations.update', [$event, $reg]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select
                                                name="status"
                                                onchange="this.form.submit()"
                                                class="text-xs font-semibold rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand py-1 px-2 border {{ $reg->status_badge_classes }}"
                                            >
                                                <option value="registered" {{ $reg->status === 'registered' ? 'selected' : '' }}>Terdaftar</option>
                                                <option value="confirmed" {{ $reg->status === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                                                <option value="attended" {{ $reg->status === 'attended' ? 'selected' : '' }}>Hadir</option>
                                                <option value="cancelled" {{ $reg->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $reg->created_at->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                        <form
                                            action="{{ route('admin.event.registrations.destroy', [$event, $reg]) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Hapus data pendaftaran peserta {{ $reg->name }}?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-sm">
                                        Belum ada peserta yang mendaftar pada agenda kegiatan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $registrations->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
