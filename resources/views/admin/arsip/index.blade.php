<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Arsip Dokumen</h2>
    </x-slot>
    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-end gap-3 mb-4">
            <form method="GET" class="flex flex-wrap items-end gap-2">
                <x-text-input name="q" placeholder="Cari judul/nomor/ringkasan" :value="$q" />
                <x-text-input name="kat" placeholder="Kategori" :value="$kat" />
                <x-text-input name="tahun" placeholder="Tahun" :value="$th" />
                <x-text-input name="tag" placeholder="Tag" :value="$tag" />
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">Semua</option>
                    <option value="published" @selected($status === 'published')>Published</option>
                    <option value="draft" @selected($status === 'draft')>Draft</option>
                </select>
                <x-primary-button>Filter</x-primary-button>
            </form>
            <a href="{{ route('admin.arsip.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Tambah</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Judul</th>
                        <th class="px-4 py-2 text-left">Kategori</th>
                        <th class="px-4 py-2 text-left">Tahun</th>
                        <th class="px-4 py-2 text-left">Nomor</th>
                        <th class="px-4 py-2 text-left">Ukuran</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($items as $a)
                    <tr>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.arsip.show', $a) }}" class="font-medium hover:underline">{{ $a->judul }}</a>
                            <div class="text-xs text-gray-500">{{ $a->tags }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $a->kategori ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $a->tahun ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $a->nomor_dokumen ?? '-' }}</td>
                        <td class="px-4 py-2"> @if($a->file_size) {{ number_format($a->file_size/1024,1) }} KB @else - @endif</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs {{ $a->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">{{ $a->is_published ? 'Published' : 'Draft' }}</span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex flex-wrap gap-2 justify-end">
                                <a class="px-3 py-1 bg-gray-200 rounded" href="{{ route('admin.arsip.edit', $a) }}">Edit</a>
                                @if($a->file_path)
                                    <a class="px-3 py-1 bg-blue-600 text-white rounded" href="{{ route('admin.arsip.download', $a) }}">Download</a>
                                @endif
                                <form method="POST" action="{{ route('admin.arsip.toggle-publish', $a) }}"> @csrf<button class="px-3 py-1 rounded {{ $a->is_published ? 'bg-yellow-200' : 'bg-green-200' }}">{{ $a->is_published ? 'Unpublish' : 'Publish' }}</button></form>
                                <form method="POST" action="{{ route('admin.arsip.destroy', $a) }}" onsubmit="return confirm('Hapus arsip ini?')"> @csrf @method('DELETE')<button class="px-3 py-1 bg-red-200 rounded">Hapus</button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada arsip.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</x-app-layout>
