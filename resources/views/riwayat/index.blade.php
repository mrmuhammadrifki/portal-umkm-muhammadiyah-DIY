<x-public-layout title="Riwayat Pesanan">

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Riwayat Pesanan</h1>
        <p class="text-sm text-gray-500 mt-1 mb-8">
            Daftar produk dan UMKM yang pernah Anda hubungi dengan menekan tombol "Pesan via WhatsApp".
            Transaksi dilakukan langsung dengan UMKM lewat WhatsApp, bukan di portal ini.
        </p>

        <div class="space-y-3">
            @forelse($orders as $order)
                @php
                    $product = $order->product;
                    $umkm = $order->umkmProfile;
                    // Hanya tautkan ke target yang masih tayang di katalog publik
                    $productLive = $product && $product->status === 'active'
                        && $product->umkmProfile?->status === 'approved'
                        && $product->umkmProfile?->user?->is_active;
                    $umkmLive = $umkm && $umkm->status === 'approved' && $umkm->user?->is_active;
                    $reorderUrl = $productLive
                        ? route('katalog.produk-hubungi', $product)
                        : ($umkmLive ? route('katalog.hubungi', $umkm->id) : null);
                @endphp

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500">
                            {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                        </p>

                        <h3 class="font-bold text-gray-900 mt-1 truncate">
                            @if($order->product_name)
                                @if($productLive)
                                    <a href="{{ route('katalog.produk-detail', $product) }}" class="hover:text-brand hover:underline">{{ $order->product_name }}</a>
                                @else
                                    {{ $order->product_name }}
                                @endif
                            @else
                                Pemesanan ke usaha
                            @endif
                        </h3>

                        <p class="text-sm text-gray-600 truncate">
                            @if($umkmLive)
                                <a href="{{ route('katalog.detail', $umkm->id) }}" class="hover:text-brand hover:underline">{{ $order->business_name }}</a>
                            @else
                                {{ $order->business_name }}
                            @endif
                        </p>
                    </div>

                    <div class="shrink-0">
                        @if($reorderUrl)
                            <a href="{{ $reorderUrl }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-bold text-white bg-green-600 hover:bg-green-700 shadow-sm transition">
                                Pesan lagi
                            </a>
                        @else
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">Tidak tersedia lagi</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 text-center rounded-lg border border-gray-200">
                    <p class="text-5xl mb-3">🧾</p>
                    <p class="text-gray-600 text-lg">Belum ada riwayat pesanan.</p>
                    <a href="{{ route('katalog.produk') }}" class="inline-block mt-4 px-5 py-2 rounded-lg text-sm font-bold text-white bg-brand hover:bg-brand-dark transition">
                        Jelajahi Katalog Produk
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    </main>

</x-public-layout>
