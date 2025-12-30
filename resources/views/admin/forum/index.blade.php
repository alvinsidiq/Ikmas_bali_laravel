<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Forum — Topik</h2>
    </x-slot>
    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-end gap-2 mb-4">
            <form method="GET" class="flex flex-wrap gap-2">
                <x-text-input name="q" placeholder="Cari judul/konten" :value="$q" />
                <x-text-input name="kat" placeholder="Kategori" :value="$kat" />
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">Semua Status</option>
                    <option value="open" @selected($status === 'open')>Open</option>
                    <option value="closed" @selected($status === 'closed')>Closed</option>
                    <option value="pinned" @selected($status === 'pinned')>Pinned</option>
                    <option value="solved" @selected($status === 'solved')>Solved</option>
                </select>
                <x-primary-button>Filter</x-primary-button>
            </form>
            <a href="{{ route('admin.forum.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Buat Topik</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aktivitas</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($topics as $t)
                        <tr>
                            <td class="px-4 py-3 align-top">
                                <a class="font-semibold text-gray-900 hover:underline" href="{{ route('admin.forum.show', $t) }}">{{ $t->judul }}</a>
                                <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($t->body), 140) }}</div>
                            </td>
                            <td class="px-4 py-3 align-top text-gray-700">{{ $t->kategori ?? '-' }}</td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-col gap-1">
                                    @if($t->is_pinned)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Pinned</span>
                                    @endif
                                    @if($t->is_solved)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Solved</span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $t->is_open ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $t->is_open ? 'Open' : 'Closed' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top text-gray-700">
                                <div>Last: {{ optional($t->last_post_at)->format('d M Y H:i') ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Posts: {{ $t->posts_count }}</div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50" href="{{ route('admin.forum.edit', $t) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.forum.toggle-open', $t) }}"> @csrf<button class="px-3 py-1 border rounded {{ $t->is_open ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">{{ $t->is_open ? 'Close' : 'Open' }}</button></form>
                                    <form method="POST" action="{{ route('admin.forum.toggle-pin', $t) }}"> @csrf<button class="px-3 py-1 border rounded {{ $t->is_pinned ? 'border-slate-300 text-slate-700 hover:bg-slate-50' : 'border-amber-300 text-amber-700 hover:bg-amber-50' }}">{{ $t->is_pinned ? 'Unpin' : 'Pin' }}</button></form>
                                    @if($t->is_solved)
                                        <form method="POST" action="{{ route('admin.forum.unmark-solved', $t) }}"> @csrf<button class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50">Unmark Solved</button></form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.forum.destroy', $t) }}" onsubmit="return confirm('Hapus topik?')"> @csrf @method('DELETE')<button class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50">Hapus</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada topik.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $topics->links() }}</div>
    </div>
</x-app-layout>
