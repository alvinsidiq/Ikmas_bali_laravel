<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Kegiatan</h2>
    </x-slot>
    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-end gap-3 mb-4">
            <form method="GET" class="flex gap-2">
                <x-text-input name="q" placeholder="Cari judul/lokasi/teks" :value="$q" />
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">Semua Status</option>
                    <option value="published" @selected($status === 'published')>Published</option>
                    <option value="unpublished" @selected($status === 'unpublished')>Unpublished</option>
                </select>
                <select name="w" class="border-gray-300 rounded-md">
                    <option value="">Semua Waktu</option>
                    <option value="upcoming" @selected($w === 'upcoming')>Akan Datang</option>
                    <option value="past" @selected($w === 'past')>Selesai</option>
                </select>
                <x-primary-button>Filter</x-primary-button>
            </form>
            <a href="{{ route('admin.kegiatan.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Tambah</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-4 py-3">Poster</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Publikasi</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $k)
                        <tr>
                            <td class="px-4 py-3 align-top">
                                @if($k->poster_path)
                                    <x-media-img :src="$k->poster_path" class="w-16 h-16 object-cover rounded" alt="Poster {{ $k->judul }}" />
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">No Poster</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <a href="{{ route('admin.kegiatan.show', $k) }}" class="font-semibold text-gray-900 hover:underline">
                                    {{ $k->judul }}
                                </a>
                                <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($k->deskripsi), 120) }}</div>
                            </td>
                            <td class="px-4 py-3 align-top text-gray-700">{{ $k->lokasi ?? '-' }}</td>
                            <td class="px-4 py-3 align-top text-gray-700">
                                {{ optional($k->waktu_mulai)->format('d M Y H:i') ?? '-' }}<br>
                                @if($k->waktu_selesai)
                                    <span class="text-xs text-gray-500">sampai {{ optional($k->waktu_selesai)->format('d M Y H:i') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $k->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $k->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.kegiatan.edit', $k) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.kegiatan.toggle-publish', $k) }}">
                                        @csrf
                                        <button class="px-3 py-1 border rounded {{ $k->is_published ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                            {{ $k->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.kegiatan.destroy', $k) }}" onsubmit="return confirm('Hapus kegiatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</x-app-layout>
