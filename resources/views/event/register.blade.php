<x-public-layout :title="'Pendaftaran: ' . $event->title">

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- BREADCRUMB -->
        <nav class="flex items-center text-xs text-gray-500 mb-6 gap-2">
            <a href="{{ route('katalog.publik') }}" class="hover:text-brand transition">Beranda</a>
            <span>&rsaquo;</span>
            <a href="{{ route('event.index') }}" class="hover:text-brand transition">Agenda & Pelatihan</a>
            <span>&rsaquo;</span>
            <a href="{{ route('event.show', $event->slug) }}" class="hover:text-brand transition truncate max-w-xs">{{ $event->title }}</a>
            <span>&rsaquo;</span>
            <span class="text-gray-800 font-semibold">Formulir Pendaftaran</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- KOLOM KIRI: FORMULIR PENDAFTARAN (7 atau 8 KOLOM) -->
            <div class="lg:col-span-7 xl:col-span-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-brand to-brand-dark px-6 sm:px-8 py-6 text-white">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-green-100 mb-1">
                        <span>📝</span> Formulir Pendaftaran Peserta
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold leading-tight">
                        Pendaftaran {{ $event->type_label }}
                    </h1>
                    <p class="text-green-50 text-xs sm:text-sm mt-1 line-clamp-1">
                        {{ $event->title }}
                    </p>
                </div>

                <div class="p-6 sm:p-8">

                    <!-- FLASH MESSAGE / ALERT ERROR -->
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl border border-red-200 text-sm flex items-start gap-3">
                            <span class="text-lg">⚠️</span>
                            <div>
                                <p class="font-bold">Pendaftaran Tidak Dapat Diproses</p>
                                <p class="text-xs sm:text-sm mt-0.5">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl border border-red-200 text-sm">
                            <p class="font-bold mb-1 flex items-center gap-1.5">
                                <span>⚠️</span> Mohon periksa kembali data yang Anda masukkan:
                            </p>
                            <ul class="list-disc list-inside text-xs space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- INFO LOGGED IN USER -->
                    @auth
                        <div class="mb-6 p-3.5 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between gap-3 text-xs text-blue-900">
                            <div class="flex items-center gap-2">
                                <span class="text-base">👤</span>
                                <div>
                                    Mendaftar sebagai <strong class="font-semibold">{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-blue-200 text-blue-800 font-semibold rounded text-[11px]">Terautentikasi</span>
                        </div>
                    @else
                        <div class="mb-6 p-3.5 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between gap-3 text-xs text-amber-900">
                            <div class="flex items-center gap-2">
                                <span class="text-base">💡</span>
                                <div>
                                    Sudah memiliki akun UMKM? <a href="{{ route('login') }}" class="font-bold underline hover:text-amber-800">Masuk sekarang</a> untuk pengisian otomatis.
                                </div>
                            </div>
                        </div>
                    @endauth

                    <form action="{{ route('event.register.store', $event->slug) }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- SECTION 1: DATA IDENTITAS DIRI -->
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                                <span class="w-6 h-6 rounded-full bg-brand-50 text-brand text-xs flex items-center justify-center font-bold">1</span>
                                Informasi Pribadi Peserta
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="name" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ $defaults['name'] }}"
                                        required
                                        placeholder="Contoh: Muhammad Ilham, S.E."
                                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2.5 border"
                                    >
                                </div>

                                <div>
                                    <label for="phone" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                        Nomor WhatsApp / HP <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="tel"
                                        id="phone"
                                        name="phone"
                                        value="{{ $defaults['phone'] }}"
                                        required
                                        placeholder="Contoh: 081234567890"
                                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2.5 border"
                                    >
                                    <p class="text-[11px] text-gray-500 mt-1">Digunakan untuk konfirmasi kehadiran & info grup.</p>
                                </div>

                                <div>
                                    <label for="email" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                        Alamat Email Aktif <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ $defaults['email'] }}"
                                        required
                                        placeholder="Contoh: nama@gmail.com"
                                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2.5 border"
                                    >
                                    <p class="text-[11px] text-gray-500 mt-1">Bukti pendaftaran & materi akan dikirim ke sini.</p>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: PROFIL USAHA / INSTANSI -->
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                                <span class="w-6 h-6 rounded-full bg-brand-50 text-brand text-xs flex items-center justify-center font-bold">2</span>
                                Profil Usaha / Instansi (Opsional)
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="institution_or_business" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                        Nama Usaha / UMKM / Instansi
                                    </label>
                                    <input
                                        type="text"
                                        id="institution_or_business"
                                        name="institution_or_business"
                                        value="{{ $defaults['institution_or_business'] }}"
                                        placeholder="Contoh: Kripik Berkah / PDM Sleman"
                                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2.5 border"
                                    >
                                    <p class="text-[11px] text-gray-500 mt-1">Kosongkan jika mendaftar atas nama pribadi.</p>
                                </div>

                                <div>
                                    <label for="subsector_or_category" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                        Bidang Usaha / Minat
                                    </label>
                                    <select
                                        id="subsector_or_category"
                                        name="subsector_or_category"
                                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2.5 border bg-white"
                                    >
                                        <option value="">-- Pilih Bidang Usaha / Minat --</option>
                                        @php
                                            $bidangOptions = [
                                                'Kuliner (Makanan & Minuman)',
                                                'Fashion & Pakaian',
                                                'Kriya & Kerajinan Tangan',
                                                'Agrobisnis & Pertanian',
                                                'Perdagangan & Retail',
                                                'Jasa & Konsultasi',
                                                'Teknologi Digital & Kreatif',
                                                'Minat Memulai Usaha (Pemula)',
                                                'Lainnya',
                                            ];
                                        @endphp
                                        @foreach($bidangOptions as $opt)
                                            <option value="{{ $opt }}" {{ $defaults['subsector_or_category'] === $opt ? 'selected' : '' }}>
                                                {{ $opt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="city" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                        Kabupaten / Kota Domisili <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="city"
                                        name="city"
                                        required
                                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2.5 border bg-white"
                                    >
                                        <option value="">-- Pilih Domisili --</option>
                                        @php
                                            $cities = [
                                                'Kota Yogyakarta',
                                                'Kabupaten Sleman',
                                                'Kabupaten Bantul',
                                                'Kabupaten Kulon Progo',
                                                'Kabupaten Gunungkidul',
                                                'Luar Daerah Istimewa Yogyakarta',
                                            ];
                                        @endphp
                                        @foreach($cities as $city)
                                            <option value="{{ $city }}" {{ $defaults['city'] === $city ? 'selected' : '' }}>
                                                {{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: CATATAN & MOTIVASI -->
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                                <span class="w-6 h-6 rounded-full bg-brand-50 text-brand text-xs flex items-center justify-center font-bold">3</span>
                                Motivasi & Catatan (Opsional)
                            </h2>

                            <div>
                                <label for="notes" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                    Harapan atau Pertanyaan untuk Narasumber
                                </label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="3"
                                    placeholder="Ceritakan apa yang ingin Anda pelajari atau pertanyaan yang ingin disampaikan kepada pemateri..."
                                    class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand p-2.5 border"
                                >{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- PERNYATAAN / KONFIRMASI -->
                        <div class="pt-2 border-t border-gray-100">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="agree"
                                    required
                                    class="mt-1 rounded text-brand focus:ring-brand h-4 w-4 border-gray-300"
                                >
                                <span class="text-xs text-gray-600 leading-relaxed">
                                    Saya menyatakan data yang saya masukkan adalah benar, berkomitmen hadir tepat waktu, dan bersedia mengikuti tata tertib kegiatan yang diselenggarakan oleh <strong>LP UMKM Muhammadiyah DIY</strong>.
                                </span>
                            </label>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100">
                            <a
                                href="{{ route('event.show', $event->slug) }}"
                                class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition"
                            >
                                &larr; Kembali ke Rincian Agenda
                            </a>

                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-8 py-3 rounded-xl text-sm shadow-md hover:shadow-lg transition cursor-pointer"
                            >
                                <span>🚀</span>
                                <span>Kirim Pendaftaran</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- KOLOM KANAN: RINGKASAN AGENDA (STICKY) (5 atau 4 KOLOM) -->
            <div class="lg:col-span-5 xl:col-span-4 sticky top-6 space-y-6">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    @if($event->image_path)
                        <div class="w-full aspect-[16/9] bg-gray-900 overflow-hidden">
                            <img
                                src="{{ asset('storage/' . $event->image_path) }}"
                                alt="{{ $event->title }}"
                                class="w-full h-full object-cover"
                            >
                        </div>
                    @endif

                    <div class="p-6 space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $event->type_badge_classes }}">
                                {{ $event->type_label }}
                            </span>
                            <span class="inline-block bg-brand-50 text-brand-800 border border-brand-200 px-2.5 py-0.5 text-xs font-bold rounded-full">
                                {{ $event->cost }}
                            </span>
                        </div>

                        <h3 class="font-bold text-gray-900 text-base leading-snug">
                            {{ $event->title }}
                        </h3>

                        <div class="space-y-3 pt-3 border-t border-gray-100 text-xs text-gray-700">
                            <div>
                                <span class="font-semibold text-gray-500 uppercase block text-[10px]">Waktu & Tanggal</span>
                                <p class="font-bold text-gray-900 mt-0.5">
                                    📅 {{ $event->date_start->translatedFormat('l, d F Y') }}
                                </p>
                                <p class="text-gray-600">
                                    ⏰ {{ $event->date_start->format('H:i') }} WIB @if($event->date_end) - {{ $event->date_end->format('H:i') }} WIB @endif
                                </p>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-500 uppercase block text-[10px]">Lokasi</span>
                                <p class="font-medium text-gray-900 mt-0.5 leading-snug">
                                    📍 {{ $event->location }}
                                </p>
                            </div>

                            @if($event->speaker)
                                <div>
                                    <span class="font-semibold text-gray-500 uppercase block text-[10px]">Narasumber</span>
                                    <p class="font-medium text-gray-900 mt-0.5">
                                        🎤 {{ $event->speaker }}
                                    </p>
                                </div>
                            @endif

                            @if($event->quota)
                                <div>
                                    <span class="font-semibold text-gray-500 uppercase block text-[10px]">Kapasitas Peserta</span>
                                    <div class="flex items-center justify-between mt-0.5 font-medium">
                                        <span>👥 Kuota: {{ $event->quota }} Orang</span>
                                        @if($event->is_full)
                                            <span class="text-red-700 font-bold bg-red-100 px-2 py-0.5 rounded text-[11px]">Penuh</span>
                                        @else
                                            <span class="text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded text-[11px]">Sisa {{ $event->remaining_quota }} Kursi</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($event->display_contact_person)
                                <div>
                                    <span class="font-semibold text-gray-500 uppercase block text-[10px]">Narahubung</span>
                                    <p class="font-medium text-gray-900 mt-0.5">
                                        👤 {{ $event->display_contact_person }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- JAMINAN PRIVASI -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 text-xs text-gray-600 flex items-start gap-3">
                    <span class="text-lg">🔒</span>
                    <p class="leading-relaxed">
                        Data pendaftaran Anda aman bersama <strong>LP UMKM Muhammadiyah DIY</strong> dan hanya digunakan untuk keperluan koordinasi kegiatan ini.
                    </p>
                </div>

            </div>

        </div>

    </main>

</x-public-layout>
