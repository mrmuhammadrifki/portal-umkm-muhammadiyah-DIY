<x-public-layout title="Agenda & Pelatihan UMKM">

    <!-- HEADER / HERO -->
    <header class="bg-gradient-to-r from-brand-darker via-brand to-brand-light text-white py-14 px-4 sm:px-6 lg:px-8 shadow-inner">
        <div class="max-w-7xl mx-auto text-center">
            <span class="inline-block bg-white/15 backdrop-blur-sm border border-white/25 text-brand-100 text-xs font-semibold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
                Program Pembinaan & Pemberdayaan Ekonomi
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight max-w-4xl mx-auto leading-tight">
                Agenda, Pelatihan & Pendampingan UMKM
            </h1>
            <p class="mt-4 text-base sm:text-lg text-brand-100 max-w-2xl mx-auto leading-relaxed">
                Tingkatkan daya saing dan kapasitas usaha Anda melalui pelatihan praktis, fasilitasi legalitas, dan pendampingan resmi dari LP UMKM PWM DIY.
            </p>

            <!-- STAT PILLS -->
            <div class="mt-8 flex flex-wrap justify-center gap-3 text-sm">
                <span class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20">
                    📅 <strong>{{ $stats['total'] }}</strong> Total Agenda Kegiatan
                </span>
                <span class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20">
                    🎓 <strong>{{ $stats['pelatihan'] }}</strong> Pelatihan
                </span>
                <span class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20">
                    🤝 <strong>{{ $stats['pendampingan'] }}</strong> Pendampingan
                </span>
                <span class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20">
                    💡 <strong>{{ $stats['workshop'] }}</strong> Workshop
                </span>
            </div>
        </div>
    </header>

    <!-- CONTENT WRAPPER -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- FILTER & SEARCH BAR -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-md border border-gray-200 -mt-16 relative z-10 mb-10">
            <form action="{{ route('event.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cari Kegiatan</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Ketik judul kegiatan atau tempat..."
                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-gray-50 p-2.5 border"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tipe Kegiatan</label>
                    <select
                        name="type"
                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand bg-gray-50 p-2.5 border"
                    >
                        <option value="">-- Semua Tipe --</option>
                        <option value="pelatihan" {{ request('type') === 'pelatihan' ? 'selected' : '' }}>🎓 Pelatihan</option>
                        <option value="pendampingan" {{ request('type') === 'pendampingan' ? 'selected' : '' }}>🤝 Pendampingan</option>
                        <option value="workshop" {{ request('type') === 'workshop' ? 'selected' : '' }}>💡 Workshop</option>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button
                        type="submit"
                        class="bg-brand hover:bg-brand-dark text-white font-bold py-2 px-4 rounded-md text-sm shadow-sm transition w-full h-[42px] cursor-pointer"
                    >
                        Filter Agenda
                    </button>
                    @if(request('search') || request('type'))
                        <a
                            href="{{ route('event.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-md text-sm shadow-sm transition text-center h-[42px] flex items-center justify-center"
                            title="Reset Filter"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- LIST EVENT CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden flex flex-col justify-between transition hover:shadow-lg">
                    <div>
                        <!-- BANNER IMAGE -->
                        <a href="{{ route('event.show', $event) }}" class="block relative aspect-[16/9] bg-gray-100 overflow-hidden group">
                            @if($event->image_path)
                                <img
                                    src="{{ asset('storage/' . $event->image_path) }}"
                                    alt="{{ $event->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-brand-50 to-brand-100 text-brand">
                                    <span class="text-4xl font-bold">LP UMKM</span>
                                </div>
                            @endif
                        </a>

                        <!-- CARD BODY -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $event->type_badge_classes }}">
                                    {{ $event->type_label }}
                                </span>
                            </div>

                            <h2 class="text-lg font-bold text-gray-900 leading-snug hover:text-brand transition mb-2">
                                <a href="{{ route('event.show', $event) }}">
                                    {{ $event->title }}
                                </a>
                            </h2>

                            @if($event->summary)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                                    {{ $event->summary }}
                                </p>
                            @endif

                            <!-- META INFO -->
                            <div class="space-y-1.5 text-xs text-gray-600 border-t border-gray-100 pt-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400">📅</span>
                                    <span class="font-medium text-gray-800">
                                        {{ $event->date_start->translatedFormat('l, d F Y') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400">⏰</span>
                                    <span>
                                        {{ $event->date_start->format('H:i') }} WIB
                                        @if($event->date_end)
                                            - {{ $event->date_end->format('H:i') }} WIB
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-400">📍</span>
                                    <span class="line-clamp-1">{{ $event->location }}</span>
                                </div>
                                @if($event->contact_person || $event->whatsapp_contact)
                                    <div class="flex items-start gap-2">
                                        <span class="text-gray-400">📞</span>
                                        <span class="line-clamp-1 font-medium text-gray-700">CP: {{ $event->display_contact_person }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER ACTION -->
                    <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <a
                            href="{{ route('event.show', $event) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-brand hover:bg-brand-dark rounded-md shadow-sm transition"
                        >
                            Lihat Detail &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 text-center rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-5xl mb-3">📅</p>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Agenda Ditemukan</h3>
                    <p class="text-gray-500 text-sm max-w-md mx-auto">
                        @if(request('search') || request('type'))
                            Tidak ada kegiatan yang sesuai dengan kriteria pencarian atau filter Anda. Silakan klik tombol <strong>Reset</strong> untuk menampilkan semua agenda.
                        @else
                            Saat ini belum ada pengumuman kegiatan pelatihan atau workshop baru. Silakan pantau halaman ini secara berkala.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $events->links() }}
        </div>

    </main>

</x-public-layout>
