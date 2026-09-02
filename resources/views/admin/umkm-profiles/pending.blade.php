<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Persetujuan Profil UMKM
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

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
                            <form action="{{ route('admin.umkm-profiles.reject', $profile) }}" method="POST" onsubmit="return confirm('Yakin tolak profil ini?')" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded text-xs">
                                    Tolak
                                </button>
                            </form>
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
                                    <form action="{{ route('admin.umkm-profiles.reject', $profile) }}" method="POST" onsubmit="return confirm('Yakin tolak profil ini?')">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Tolak
                                        </button>
                                    </form>
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

        </div>
    </div>
</x-app-layout>
