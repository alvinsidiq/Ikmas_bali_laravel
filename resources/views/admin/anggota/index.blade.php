<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Manajemen Anggota</h2>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-end gap-3 mb-4">
            <form method="GET" class="flex gap-2">
                <x-text-input name="q" placeholder="Cari nama/email/NIK/HP" :value="$q" />
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                </select>
                <x-primary-button>Cari</x-primary-button>
            </form>
            <a href="{{ route('admin.anggota.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Tambah</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">HP</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $u)
                    <tr>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.anggota.show', $u) }}" class="font-medium hover:underline">{{ $u->name }}</a>
                            <div class="text-sm text-gray-500">{{ $u->profile->nama_lengkap ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $u->email }}</td>
                        <td class="px-4 py-2">{{ $u->profile->phone ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs {{ ($u->profile->is_active ?? false) ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                {{ ($u->profile->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex gap-2 justify-end">
                                <a class="px-3 py-1 bg-gray-200 rounded" href="{{ route('admin.anggota.edit', $u) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.anggota.toggle-active', $u) }}">
                                    @csrf
                                    <button class="px-3 py-1 rounded {{ ($u->profile->is_active ?? false) ? 'bg-yellow-200' : 'bg-green-200' }}">
                                        {{ ($u->profile->is_active ?? false) ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.anggota.reset-password', $u) }}" onsubmit="return confirm('Reset password?')">
                                    @csrf
                                    <input type="hidden" name="password" value="password123" />
                                    <button class="px-3 py-1 bg-orange-200 rounded">Reset PW</button>
                                </form>
                                <form method="POST" action="{{ route('admin.anggota.destroy', $u) }}" onsubmit="return confirm('Nonaktifkan anggota ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-200 rounded">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
