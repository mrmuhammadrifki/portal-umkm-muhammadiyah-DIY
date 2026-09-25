<x-public-layout :title="'Pendaftaran Berhasil: ' . $event->title">

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- BREADCRUMB -->
        <nav class="flex items-center text-xs text-gray-500 mb-6 gap-2">
            <a href="{{ route('katalog.publik') }}" class="hover:text-brand transition">Beranda</a>
            <span>&rsaquo;</span>
            <a href="{{ route('event.index') }}" class="hover:text-brand transition">Agenda & Pelatihan</a>
            <span>&rsaquo;</span>
            <a href="{{ route('event.show', $event->slug) }}" class="hover:text-brand transition truncate max-w-xs">{{ $event->title }}</a>
            <span>&rsaquo;</span>
            <span class="text-gray-800 font-semibold">Bukti Pendaftaran</span>
        </nav>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden text-center">

            <!-- HERO SUCCESS HEADER -->
            <div class="bg-gradient-to-b from-green-50 to-white pt-10 pb-6 px-6 sm:px-10 border-b border-gray-100">
                <div class="w-20 h-20 mx-auto rounded-full bg-green-100 text-green-600 flex items-center justify-center text-4xl shadow-inner mb-4 animate-bounce">
                    ✓
                </div>
                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full mb-2">
                    Pendaftaran Berhasil Terkirim
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
                    Selamat, Anda Telah Terdaftar!
                </h1>
                <p class="text-gray-600 text-sm sm:text-base mt-2 max-w-lg mx-auto leading-relaxed">
                    Data pendaftaran Anda telah berhasil dicatat untuk kegiatan <strong>{{ $event->title }}</strong>.
                </p>
            </div>

            <div class="p-6 sm:p-10 space-y-8 text-left">

                <!-- RINGKASAN DATA PENDAFTARAN -->
                @if($registration)
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                📋 Detail Pendaftar
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $registration->status_badge_classes }}">
                                {{ $registration->status_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                            <div>
                                <span class="text-gray-500 block text-[11px] uppercase">Nama Lengkap</span>
                                <span class="font-bold text-gray-900">{{ $registration->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px] uppercase">Nomor WhatsApp / HP</span>
                                <span class="font-bold text-gray-900">{{ $registration->phone }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px] uppercase">Alamat Email</span>
                                <span class="font-medium text-gray-900">{{ $registration->email }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[11px] uppercase">Domisili</span>
                                <span class="font-medium text-gray-900">{{ $registration->city }}</span>
                            </div>
                            @if($registration->institution_or_business)
                                <div>
                                    <span class="text-gray-500 block text-[11px] uppercase">Usaha / Instansi</span>
                                    <span class="font-medium text-gray-900">{{ $registration->institution_or_business }}</span>
                                </div>
                            @endif
                            @if($registration->subsector_or_category)
                                <div>
                                    <span class="text-gray-500 block text-[11px] uppercase">Bidang Usaha / Minat</span>
                                    <span class="font-medium text-gray-900">{{ $registration->subsector_or_category }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- RINCIAN JADWAL KEGIATAN -->
                <div class="bg-brand-50/50 rounded-2xl p-6 border border-brand-200 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-900 block pb-2 border-b border-brand-200">
                        📍 Informasi Pelaksanaan
                    </span>
                    <div class="space-y-2 text-xs sm:text-sm text-gray-800">
                        <p><strong>📅 Tanggal:</strong> {{ $event->date_start->translatedFormat('l, d F Y') }}</p>
                        <p><strong>⏰ Waktu:</strong> {{ $event->date_start->format('H:i') }} WIB @if($event->date_end) - {{ $event->date_end->format('H:i') }} WIB @endif</p>
                        <p><strong>🏢 Tempat:</strong> {{ $event->location }}</p>
                        <p><strong>🏛️ Penyelenggara:</strong> {{ $event->organizer }}</p>
                    </div>
                </div>

                <!-- PANDUAN LANGKAH SELANJUTNYA -->
                <div class="space-y-3 text-xs sm:text-sm text-gray-600 bg-blue-50/60 p-5 rounded-2xl border border-blue-200">
                    <h3 class="font-bold text-blue-900 flex items-center gap-1.5 text-sm">
                        <span>ℹ️</span> Langkah Selanjutnya:
                    </h3>
                    <ul class="space-y-1.5 list-disc list-inside text-gray-700">
                        <li>Catat jadwal kegiatan dan pasang pengingat pada kalender Anda.</li>
                        <li>Panitia dari <strong>LP UMKM PWM DIY</strong> akan menghubungi Anda melalui WhatsApp untuk konfirmasi kehadiran atau tautan grup peserta sebelum hari pelaksanaan.</li>
                        <li>Pastikan nomor WhatsApp dan email yang Anda daftarkan dalam kondisi aktif.</li>
                    </ul>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4 border-t border-gray-100">
                    @if($event->clean_whatsapp)
                        <a
                            href="https://wa.me/{{ $event->clean_whatsapp }}?text={{ urlencode('Halo panitia LP UMKM PWM DIY, saya sudah mendaftar pada agenda: ' . $event->title . ' atas nama ' . ($registration?->name ?? '')) }}"
                            target="_blank"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow transition"
                        >
                            <span>💬</span>
                            <span>Konfirmasi ke Narahubung via WhatsApp</span>
                        </a>
                    @endif

                    <a
                        href="{{ route('event.show', $event->slug) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold py-3 px-6 rounded-xl text-sm shadow transition"
                    >
                        <span>&larr;</span>
                        <span>Kembali ke Detail Agenda</span>
                    </a>

                    <a
                        href="{{ route('event.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-5 rounded-xl text-sm transition"
                    >
                        <span>Lihat Agenda Lainnya</span>
                    </a>
                </div>

            </div>

        </div>

    </main>

</x-public-layout>
