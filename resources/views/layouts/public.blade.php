<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#047857">
    <meta name="description" content="Direktori resmi UMKM binaan LP UMKM Muhammadiyah Daerah Istimewa Yogyakarta — cari dan hubungi UMKM/produk langsung via WhatsApp.">
    <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name') }}</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23047857'%3E%3Cpath d='M3 3a1 1 0 0 0-.98.8L1.02 8.6A2.5 2.5 0 0 0 3.5 11.5c.78 0 1.48-.33 1.98-.85.46.52 1.14.85 1.9.85.78 0 1.48-.33 1.98-.85.46.52 1.14.85 1.9.85.78 0 1.48-.33 1.98-.85.46.52 1.14.85 1.9.85.78 0 1.48-.33 1.98-.85a2.65 2.65 0 0 0 1.98.85 2.5 2.5 0 0 0 2.48-2.9L21.98 3.8A1 1 0 0 0 21 3H3z'/%3E%3Cpath d='M4 12.9V19a2 2 0 0 0 2 2h3v-5h6v5h3a2 2 0 0 0 2-2v-6.1a4.48 4.48 0 0 1-1.98.35 4.5 4.5 0 0 1-1.9-.42 4.5 4.5 0 0 1-3.88 0 4.5 4.5 0 0 1-3.88 0 4.5 4.5 0 0 1-1.9.42 4.48 4.48 0 0 1-1.98-.35z'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <!-- NAVBAR / HEADER -->
    <nav x-data="{ open: false }" class="bg-brand text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('katalog.publik') }}" class="font-bold text-sm sm:text-lg tracking-wider">
                    PORTAL UMKM MUHAMMADIYAH DIY
                </a>

                <!-- Desktop links -->
                <div class="hidden sm:flex sm:items-center sm:space-x-4">
                    <a href="{{ route('katalog.publik') }}" class="hover:bg-brand-dark px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('katalog.publik') ? 'bg-brand-dark' : '' }}">
                        Daftar UMKM
                    </a>
                    <a href="{{ route('katalog.produk') }}" class="hover:bg-brand-dark px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('katalog.produk*') ? 'bg-brand-dark' : '' }}">
                        Katalog Produk
                    </a>
                    <a href="{{ route('login') }}" class="hover:bg-brand-dark px-3 py-2 rounded-md text-sm font-medium transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-brand-light hover:brightness-110 px-3 py-2 rounded-md text-sm font-medium transition">
                        Daftar UMKM
                    </a>
                </div>

                <!-- Hamburger (mobile) -->
                <button
                    @click="open = !open"
                    class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-brand-dark focus:outline-none transition"
                    aria-label="Buka menu"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="open" x-transition class="sm:hidden border-t border-brand-dark" style="display: none;">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('katalog.publik') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('katalog.publik') ? 'bg-brand-dark' : 'hover:bg-brand-dark' }}">
                    Daftar UMKM
                </a>
                <a href="{{ route('katalog.produk') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('katalog.produk*') ? 'bg-brand-dark' : 'hover:bg-brand-dark' }}">
                    Katalog Produk
                </a>
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-brand-dark transition">
                    Login
                </a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-sm font-medium bg-brand-light hover:brightness-110 transition">
                    Daftar UMKM
                </a>
            </div>
        </div>
    </nav>

    {{ $slot }}

    <!-- FOOTER -->
    <footer class="bg-brand-darker text-white border-t border-brand-900 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-md font-bold tracking-wider uppercase mb-3 text-brand-300">Portal UMKM</h3>
                    <p class="text-sm text-brand-100 leading-relaxed">Sistem Informasi Direktori Digital Usaha Mikro Kecil dan Menengah di bawah naungan Jaringan Pemberdayaan Ekonomi Pimpinan Wilayah Muhammadiyah Daerah Istimewa Yogyakarta.</p>
                </div>

                <div>
                    <h3 class="text-md font-bold tracking-wider uppercase mb-3 text-brand-300">Kontak Kantor</h3>
                    <p class="text-sm text-brand-100 leading-relaxed">
                        📍 Gedung Pimpinan Wilayah Muhammadiyah DIY<br>
                        Jl. Gedongkuning No. 130, Rejowinangun, Kotagede, Yogyakarta<br>
                        📞 Telp: (0274) 377xxx<br>
                        ✉️ Email: info@muhammadiyahdiy.or.id
                    </p>
                </div>

                <div>
                    <h3 class="text-md font-bold tracking-wider uppercase mb-3 text-brand-300">Tautan Pintas</h3>
                    <ul class="text-sm text-brand-100 space-y-2">
                        <li><a href="{{ route('katalog.produk') }}" class="hover:text-white transition">🛍️ Katalog Produk</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">🔐 Login Admin / Pelaku Usaha</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">📝 Pendaftaran Mitra Baru</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-brand-700 mt-8 pt-6 text-center text-xs text-brand-200">
                <p>&copy; {{ date('Y') }} Portal UMKM Muhammadiyah DIY. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
