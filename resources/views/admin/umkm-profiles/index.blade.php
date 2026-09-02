<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Semua UMKM (Moderasi)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg">
                <form action="{{ route('admin.umkm-profiles.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cari Nama Usaha</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm p-2 border">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                        <select name="status" class="w-full text-sm rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm w-full h-[38px]">
                            Cari & Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- MOBILE: card list -->
            <div class="sm:hidden space-y-3 px-4">
                @forelse($profiles as $profile)
                    <div class="bg-white shadow rounded-lg p-4 border border-gray-100">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="font-bold text-gray-900">{{ $profile->business_name }}</p>
                                <p class="text-sm text-gray-500">{{ $profile->owner_name }} · {{ $profile->whatsapp }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <x-status-badge :status="$profile->status" :label="ucfirst($profile->status)" />
                            <x-status-badge :status="$profile->user->is_active ? 'account_active' : 'account_suspended'" />
                        </div>
                        @if($profile->user->is_active)
                            <form action="{{ route('admin.umkm-profiles.suspend', $profile) }}" method="POST" onsubmit="return confirm('Yakin suspend akun UMKM ini? Login akan diblokir dan hilang dari katalog publik.')">
                                @csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded text-xs">
                                    Suspend
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.umkm-profiles.reactivate', $profile) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded text-xs">
                                    Aktifkan
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-lg border border-gray-200">
                        <p class="text-gray-500">Tidak ada data.</p>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($profiles as $profile)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->business_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->owner_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $profile->whatsapp }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$profile->status" :label="ucfirst($profile->status)" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$profile->user->is_active ? 'account_active' : 'account_suspended'" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($profile->user->is_active)
                                        <form action="{{ route('admin.umkm-profiles.suspend', $profile) }}" method="POST" onsubmit="return confirm('Yakin suspend akun UMKM ini? Login akan diblokir dan hilang dari katalog publik.')">
                                            @csrf
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Suspend
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.umkm-profiles.reactivate', $profile) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $profiles->links() }}</div>
            </div>

        </div>
    </div>
</x-app-layout>
