<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Dokumentasi — Album</h2>
    </x-slot>
    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-end gap-3 mb-4">
            <form method="GET" class="flex flex-wrap items-end gap-2">
                <x-text-input name="q" placeholder="Cari judul/lokasi/tags/desc" :value="$q" />
                <x-text-input name="tahun" placeholder="Tahun" :value="$tahun" />
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">Semua</option>
                    <option value="published" @selected($status === 'published')>Published</option>
                    <option value="draft" @selected($status === 'draft')>Draft</option>
                </select>
                <x-primary-button>Filter</x-primary-button>
            </form>
            <a href="{{ route('admin.dokumentasi.albums.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Buat Album</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-4 py-3">Album</th>
                        <th class="px-4 py-3">Lokasi / Tanggal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Media/Views</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $a)
                        <tr>
                            <td class="px-4 py-3 align-top">
                                <div class="flex items-start gap-3">
                                    <x-media-img :src="$a->cover_path" class="w-14 h-14 object-cover rounded" alt="Cover {{ $a->judul }}" />
                                    <div>
                                        <a class="font-semibold text-gray-900 hover:underline" href="{{ route('admin.dokumentasi.albums.edit', $a) }}">{{ $a->judul }}</a>
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($a->deskripsi), 120) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top text-gray-700">
                                <div>{{ $a->lokasi ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ optional($a->tanggal_kegiatan)->format('d M Y') ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $a->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $a->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top text-gray-700">
                                <div>Media: {{ $a->media_count }}</div>
                                <div class="text-xs text-gray-500">Views: {{ $a->view_count }}</div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.dokumentasi.albums.edit', $a) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.dokumentasi.albums.toggle-publish', $a) }}"> @csrf<button class="px-3 py-1 border rounded {{ $a->is_published ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">{{ $a->is_published ? 'Unpublish' : 'Publish' }}</button></form>
                                    <form method="POST" action="{{ route('admin.dokumentasi.albums.destroy', $a) }}" onsubmit="return confirm('Hapus album? Semua media akan ikut terhapus.')"> @csrf @method('DELETE')<button class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50">Hapus</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada album.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</x-app-layout>
