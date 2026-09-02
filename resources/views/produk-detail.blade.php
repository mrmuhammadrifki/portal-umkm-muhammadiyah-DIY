@php
    $galleryUrls = $product->images->isNotEmpty()
        ? $product->images->map(fn ($img) => asset('storage/' . $img->image_path))->values()
        : ($product->image_path ? collect([asset('storage/' . $product->image_path)]) : collect());
@endphp
<x-public-layout :title="$product->name">

    <main class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            @if($galleryUrls->isNotEmpty())
                <div
                    x-data="{ active: 0, images: @js($galleryUrls), lightbox: false }"
                    @keydown.escape.window="lightbox = false"
                    @keydown.left.window="if (lightbox) active = active === 0 ? images.length - 1 : active - 1"
                    @keydown.right.window="if (lightbox) active = active === images.length - 1 ? 0 : active + 1"
                    class="relative bg-gray-100"
                >
                    <div class="relative h-64 sm:h-96 overflow-hidden">
                        <template x-for="(src, i) in images" :key="i">
                            <img
                                :src="src"
                                x-show="active === i"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                @click="lightbox = true"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover cursor-zoom-in"
                            >
                        </template>

                        <template x-if="images.length > 1">
                            <div>
                                <button
                                    type="button" @click="active = active === 0 ? images.length - 1 : active - 1"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white rounded-full w-9 h-9 flex items-center justify-center text-lg"
                                    aria-label="Foto sebelumnya"
                                >&#8249;</button>
                                <button
                                    type="button" @click="active = active === images.length - 1 ? 0 : active + 1"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white rounded-full w-9 h-9 flex items-center justify-center text-lg"
                                    aria-label="Foto berikutnya"
                                >&#8250;</button>
                            </div>
                        </template>

                        <button
                            type="button" @click="lightbox = true"
                            class="absolute bottom-2 right-2 bg-black/40 hover:bg-black/60 text-white rounded-full w-8 h-8 flex items-center justify-center"
                            aria-label="Perbesar foto"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <template x-if="images.length > 1">
                        <div class="flex justify-center gap-2 py-3 bg-white">
                            <template x-for="(src, i) in images" :key="i">
                                <button
                                    type="button" @click="active = i"
                                    class="w-2.5 h-2.5 rounded-full transition"
                                    :class="active === i ? 'bg-brand' : 'bg-gray-300'"
                                    :aria-label="'Lihat foto ' + (i + 1)"
                                ></button>
                            </template>
                        </div>
                    </template>

                    <!-- LIGHTBOX: preview foto ukuran penuh, tanpa crop (object-contain) -->
                    <template x-teleport="body">
                        <div
                            x-show="lightbox"
                            x-transition.opacity
                            @click.self="lightbox = false"
                            class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
                            style="display: none;"
                        >
                            <button
                                type="button" @click="lightbox = false"
                                class="absolute top-4 right-4 text-white/80 hover:text-white text-3xl leading-none"
                                aria-label="Tutup"
                            >&times;</button>

                            <img :src="images[active]" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain">

                            <template x-if="images.length > 1">
                                <div>
                                    <button
                                        type="button" @click.stop="active = active === 0 ? images.length - 1 : active - 1"
                                        class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center text-xl"
                                        aria-label="Foto sebelumnya"
                                    >&#8249;</button>
                                    <button
                                        type="button" @click.stop="active = active === images.length - 1 ? 0 : active + 1"
                                        class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center text-xl"
                                        aria-label="Foto berikutnya"
                                    >&#8250;</button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            @endif
            <div class="p-6 sm:p-8">
                <span class="bg-brand-100 text-brand-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">{{ $product->category->name }}</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">{{ $product->name }}</h1>

                @if($product->description)
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line mt-4">{{ $product->description }}</p>
                @endif

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <p class="text-sm text-gray-500 mb-3">
                        Dijual oleh
                        <a href="{{ route('katalog.detail', $product->umkmProfile) }}" class="font-semibold text-brand hover:underline">
                            {{ $product->umkmProfile->business_name }}
                        </a>
                    </p>

                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->umkmProfile->whatsapp) }}?text=Halo%20{{ urlencode($product->umkmProfile->business_name) }},%20saya%20tertarik%20dengan%20produk%20{{ urlencode($product->name) }}%20yang%20saya%20lihat%20di%20Portal%20UMKM."
                       target="_blank"
                       class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 shadow-md transition text-center font-bold">
                        Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </main>

</x-public-layout>
