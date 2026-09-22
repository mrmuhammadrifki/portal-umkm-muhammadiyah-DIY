<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kelola Agenda & Pelatihan (Admin)
            </h2>
            <a
                href="{{ route('admin.event.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white font-bold text-sm rounded-lg hover:bg-brand-dark shadow-sm transition"
            >
                <span>+</span> Tambah Agenda Baru
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <!-- FILTER & SEARCH -->
            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200">
                <form action="{{ route('admin.event.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cari Agenda</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Ketik judul kegiatan..."
                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2 border"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tipe</label>
                        <select name="type" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2 border">
                            <option value="">Semua Tipe</option>
                            <option value="pelatihan" {{ request('type') === 'pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                            <option value="pendampingan" {{ request('type') === 'pendampingan' ? 'selected' : '' }}>Pendampingan</option>
                            <option value="workshop" {{ request('type') === 'workshop' ? 'selected' : '' }}>Workshop</option>
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-2 px-4 rounded-md text-sm shadow-sm transition h-[40px]">
                            Cari
                        </button>
                        @if(request('search') || request('type'))
                            <a href="{{ route('admin.event.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-md text-sm shadow-sm transition flex items-center justify-center h-[40px]">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>            <!-- CARD VIEW FOR MOBILE (sm:hidden) -->
            <div class="sm:hidden space-y-4">
                @forelse($events as $event)
                    <div class="bg-white rounded-xl shadow border border-gray-200 p-4 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block px-2 py-0.5 text-xs font-bold rounded-full border {{ $event->type_badge_classes }}">
                                    {{ $event->type_label }}
                                </span>
                                <h3 class="font-bold text-gray-900 text-base mt-1">
                                    {{ $event->title }}
                                </h3>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $event->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $event->is_published ? 'Publik' : 'Draft' }}
                            </span>
                        </div>

                        <div class="text-xs text-gray-600 space-y-1">
                            <p>📅 {{ $event->date_start->translatedFormat('d M Y, H:i') }} WIB</p>
                            <p>📍 {{ $event->location }}</p>
                            @if($event->contact_person || $event->whatsapp_contact)
                                <p>📞 CP: {{ $event->display_contact_person }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                            <a href="{{ route('event.show', $event) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline">
                                Pratinjau
                            </a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('admin.event.edit', $event) }}" class="text-xs font-bold text-brand hover:underline">
                                Edit
                            </a>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('admin.event.destroy', $event) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-xl border border-gray-200 text-gray-500 text-sm">
                        Belum ada data agenda.
                    </div>
                @endforelse
            </div>

            <!-- TABLE VIEW FOR DESKTOP (hidden sm:block) -->
            <div class="hidden sm:block bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Nama Kegiatan</th>
                            <th class="px-6 py-3 text-left">Tipe</th>
                            <th class="px-6 py-3 text-left">Waktu & Tempat</th>
                            <th class="px-6 py-3 text-left">Narahubung / CP</th>
                            <th class="px-6 py-3 text-left">Publish</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($events as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900 leading-snug">{{ $event->title }}</p>
                                    @if($event->summary)
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1 max-w-sm">{{ $event->summary }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full border {{ $event->type_badge_classes }}">
                                        {{ $event->type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    <p class="font-medium text-gray-800">📅 {{ $event->date_start->translatedFormat('d M Y, H:i') }} WIB</p>
                                    <p class="truncate max-w-xs text-gray-500 mt-0.5">📍 {{ $event->location }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                    @if($event->contact_person || $event->whatsapp_contact)
                                        <span class="inline-flex items-center gap-1 font-medium bg-gray-100 text-gray-800 px-2 py-1 rounded">
                                            📞 {{ $event->display_contact_person }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $event->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $event->is_published ? 'Publik' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    <a href="{{ route('event.show', $event) }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                        Lihat
                                    </a>
                                    <a href="{{ route('admin.event.edit', $event) }}" class="text-brand hover:text-brand-dark font-medium text-xs">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.event.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada agenda kegiatan yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>        </div>

            <div class="mt-6">
                {{ $events->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
