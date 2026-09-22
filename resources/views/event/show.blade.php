<x-public-layout :title="$event->title">

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- BREADCRUMB -->
        <nav class="flex items-center text-xs text-gray-500 mb-6 gap-2">
            <a href="{{ route('katalog.publik') }}" class="hover:text-brand transition">Beranda</a>
            <span>&rsaquo;</span>
            <a href="{{ route('event.index') }}" class="hover:text-brand transition">Agenda & Pelatihan</a>
            <span>&rsaquo;</span>
            <span class="text-gray-800 font-semibold truncate max-w-xs">{{ $event->title }}</span>
        </nav>

        <!-- MAIN EVENT CARD -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">

            <!-- BANNER IMAGE -->
            @if($event->image_path)
                <div class="relative w-full aspect-[21/9] sm:aspect-[24/10] bg-gray-900 overflow-hidden">
                    <img
                        src="{{ asset('storage/' . $event->image_path) }}"
                        alt="{{ $event->title }}"
                        class="w-full h-full object-cover"
                    >
                </div>
            @endif

            <div class="p-6 sm:p-10">

                <!-- BADGES & TITLE -->
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full border {{ $event->type_badge_classes }}">
                        {{ $event->type_label }}
                    </span>
                    <span class="inline-block bg-brand-50 text-brand-800 border border-brand-200 px-3 py-1 text-xs font-bold rounded-full">
                        {{ $event->cost }}
                    </span>
                    <span class="text-xs text-gray-500 font-medium">
                        Penyelenggara: <strong>{{ $event->organizer }}</strong>
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 leading-tight mb-4">
                    {{ $event->title }}
                </h1>

                @if($event->summary)
                    <p class="text-base text-gray-600 leading-relaxed pb-6 border-b border-gray-100">
                        {{ $event->summary }}
                    </p>
                @endif

                <!-- GRID: INFO RINGKAS & REGISTRATION BOX -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">

                    <!-- DESKRIPSI LENGKAP (Kiri 2 Kolom) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <span>📋</span> Deskripsi & Informasi Kegiatan
                            </h2>
                            <div class="text-gray-700 leading-relaxed whitespace-pre-line text-sm sm:text-base space-y-4 font-sans bg-gray-50/50 p-5 rounded-xl border border-gray-100">
                                {{ $event->description }}
                            </div>
                        </div>

                        @if($event->contact_person || $event->whatsapp_contact)
                            <!-- KOTAK NARAHUBUNG -->
                            <div class="bg-blue-50/60 border border-blue-200 rounded-xl p-5 flex items-start gap-4">
                                <span class="text-2xl">📞</span>
                                <div>
                                    <h3 class="text-sm font-bold text-blue-900 mb-1">Contact Person (Narahubung Panitia)</h3>
                                    <p class="text-sm font-semibold text-gray-800">{{ $event->display_contact_person }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Silakan hubungi narahubung di atas jika ada pertanyaan seputar kegiatan dan persyaratan.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- KOTAK AKSI / JADWAL DETAIL (Kanan 1 Kolom) -->
                    <div class="lg:col-span-1">
                        <div class="bg-brand-50/50 border border-brand-200 rounded-2xl p-6 sticky top-6 shadow-sm space-y-5">
                            <h2 class="text-base font-bold text-brand-900 border-b border-brand-200 pb-3">
                                Rincian Jadwal & Lokasi
                            </h2>

                            <div class="space-y-3.5 text-sm">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Tanggal Pelatihan</p>
                                    <p class="font-bold text-gray-800 mt-0.5">
                                        📅 {{ $event->date_start->translatedFormat('l, d F Y') }}
                                        @if($event->date_end && $event->date_end->format('Y-m-d') !== $event->date_start->format('Y-m-d'))
                                            <br><span class="text-xs font-normal text-gray-600">s/d {{ $event->date_end->translatedFormat('l, d F Y') }}</span>
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Waktu Pelaksanaan</p>
                                    <p class="font-semibold text-gray-800 mt-0.5">
                                        ⏰ {{ $event->date_start->format('H:i') }} WIB
                                        @if($event->date_end)
                                            - {{ $event->date_end->format('H:i') }} WIB
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Tempat Pelatihan</p>
                                    <p class="font-semibold text-gray-800 mt-0.5 leading-snug">
                                        📍 {{ $event->location }}
                                    </p>
                                </div>

                                @if($event->contact_person || $event->whatsapp_contact)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Contact Person</p>
                                        <p class="font-semibold text-gray-800 mt-0.5">
                                            👤 {{ $event->display_contact_person }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- TOMBOL PENDAFTARAN & KONTAK -->
                            <div class="pt-4 border-t border-brand-200 space-y-3">
                                @if($event->registration_url)
                                    <a
                                        href="{{ $event->registration_url }}"
                                        target="_blank"
                                        class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold py-3 px-4 rounded-xl text-sm shadow-md hover:shadow-lg transition text-center"
                                    >
                                        <span>🔗</span>
                                        <span>Daftar Sekarang</span>
                                    </a>
                                @else
                                    <button
                                        type="button"
                                        class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold py-3 px-4 rounded-xl text-sm shadow-md hover:shadow-lg transition cursor-pointer"
                                        onclick="alert('Pendaftaran dibuka sesuai tanggal penjaringan pada deskripsi kegiatan.')"
                                    >
                                        <span>🔗</span>
                                        <span>Daftar Kegiatan</span>
                                    </button>
                                @endif

                                @if($event->clean_whatsapp)
                                    <a
                                        href="https://wa.me/{{ $event->clean_whatsapp }}?text={{ urlencode('Halo panitia LP UMKM PWM DIY, saya ingin bertanya tentang agenda: ' . $event->title) }}"
                                        target="_blank"
                                        class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs shadow transition text-center"
                                    >
                                        <span>💬</span>
                                        <span>Hubungi Narahubung via WA</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- BACK TO AGENDA -->
            <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a
                    href="{{ route('event.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-bold text-brand hover:text-brand-dark transition"
                >
                    &larr; Kembali ke Semua Agenda
                </a>

                <span class="text-xs text-gray-400">
                    Dipublikasikan oleh LP UMKM Muhammadiyah DIY
                </span>
            </div>
        </div>

        <!-- AGENDA TERKAIT LAINNYA -->
        @if($relatedEvents->isNotEmpty())
            <div class="mt-14">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Agenda Lainnya</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach($relatedEvents as $rel)
                        <a
                            href="{{ route('event.show', $rel) }}"
                            class="bg-white rounded-xl shadow border border-gray-200 p-4 hover:shadow-md transition flex flex-col justify-between"
                        >
                            <div>
                                <span class="inline-block px-2 py-0.5 text-[11px] font-bold rounded-full border mb-2 {{ $rel->type_badge_classes }}">
                                    {{ $rel->type_label }}
                                </span>
                                <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 hover:text-brand">
                                    {{ $rel->title }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-2">
                                    📅 {{ $rel->date_start->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <p class="text-xs text-brand font-semibold mt-3">
                                Lihat Detail &rarr;
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </main>

</x-public-layout>
