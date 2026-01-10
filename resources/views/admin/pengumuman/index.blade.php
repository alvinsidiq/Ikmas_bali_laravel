<x-app-layout>
    @php($categoryOptions = $categories ?? \App\Models\Pengumuman::CATEGORY_OPTIONS)
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Pengumuman</h2>
    </x-slot>
    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-end gap-3 mb-4">
            <form method="GET" class="flex gap-2">
                <x-text-input name="q" placeholder="Cari judul/kategori/isi" :value="$q" />
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">Semua Status</option>
                    <option value="published" @selected($status === 'published')>Published</option>
                    <option value="draft" @selected($status === 'draft')>Draft</option>
                </select>
                <select name="pinned" class="border-gray-300 rounded-md">
                    <option value="">Pin atau tidak</option>
                    <option value="1" @selected($pinned === '1')>Pinned saja</option>
                </select>
                <select name="kat" class="border-gray-300 rounded-md">
                    <option value="">Semua Kategori</option>
                    @foreach($categoryOptions as $value => $label)
                        <option value="{{ $value }}" @selected($kat === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-primary-button>Filter</x-primary-button>
            </form>
            <a href="{{ route('admin.pengumuman.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Tambah</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-4 py-3">Judul & Cover</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Terbit</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $p)
                        <tr>
                            <td class="px-4 py-3 align-top">
                                <div class="flex gap-3">
                                    <x-media-img :src="$p->cover_path" class="w-14 h-14 rounded object-cover" alt="Cover {{ $p->judul }}" />
                                    <div>
                                        <a href="{{ route('admin.pengumuman.show', $p) }}" class="font-semibold text-gray-900 hover:underline">
                                            {{ $p->judul }}
                                        </a>
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($p->isi), 140) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top text-gray-700">{{ $p->kategori ?? '-' }}</td>
                            <td class="px-4 py-3 align-top text-gray-700">{{ optional($p->published_at)->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-col gap-1">
                                    @if($p->is_pinned)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Pinned</span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $p->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $p->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.pengumuman.edit', $p) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.pengumuman.toggle-publish', $p) }}">
                                        @csrf
                                        <button class="px-3 py-1 border rounded {{ $p->is_published ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                            {{ $p->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.pengumuman.email', $p) }}" onsubmit="return confirm('Kirim email pengumuman ini ke semua anggota?')">
                                        @csrf
                                        <button class="px-3 py-1 border border-blue-300 text-blue-700 rounded hover:bg-blue-50">Kirim Email</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.pengumuman.toggle-pin', $p) }}">
                                        @csrf
                                        <button class="px-3 py-1 border rounded {{ $p->is_pinned ? 'border-slate-300 text-slate-700 hover:bg-slate-50' : 'border-amber-300 text-amber-700 hover:bg-amber-50' }}">
                                            {{ $p->is_pinned ? 'Unpin' : 'Pin' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.pengumuman.destroy', $p) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada pengumuman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</x-app-layout>
