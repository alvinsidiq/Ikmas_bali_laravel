<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $topic->judul }}</h2>
    </x-slot>
    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded p-4 mb-6 space-y-3">
            @if($topic->banner_url)
                <x-media-img :src="$topic->banner_url" class="w-full max-h-72 object-cover rounded" alt="banner {{ $topic->judul }}" />
            @endif
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-sm text-gray-600">Kategori: {{ $topic->kategori ?? '-' }}</div>
                    <div class="mt-2 prose max-w-none">{!! nl2br(e($topic->body)) !!}</div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.forum.toggle-open', $topic) }}"> @csrf<button class="px-3 py-1 rounded {{ $topic->is_open ? 'bg-yellow-200' : 'bg-green-200' }}">{{ $topic->is_open ? 'Close' : 'Open' }}</button></form>
                    <form method="POST" action="{{ route('admin.forum.toggle-pin', $topic) }}"> @csrf<button class="px-3 py-1 rounded {{ $topic->is_pinned ? 'bg-slate-200' : 'bg-amber-200' }}">{{ $topic->is_pinned ? 'Unpin' : 'Pin' }}</button></form>
                    @if($topic->is_solved)
                        <form method="POST" action="{{ route('admin.forum.unmark-solved', $topic) }}"> @csrf<button class="px-3 py-1 bg-red-200 rounded">Unmark Solved</button></form>
                    @endif
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-600">Posts: {{ $topic->posts_count }} • Last: {{ optional($topic->last_post_at)->format('d M Y H:i') }}</div>
        </div>

        <div class="space-y-4">
            @forelse($topic->posts as $p)
            <div class="bg-white shadow rounded p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-sm text-gray-600">{{ $p->user->name }} • {{ $p->created_at->format('d M Y H:i') }}</div>
                        <div class="mt-2">{!! nl2br(e($p->content)) !!}</div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.forum.posts.mark-solution', [$topic, $p]) }}"> @csrf<button class="px-3 py-1 rounded {{ $p->is_solution ? 'bg-emerald-200' : 'bg-emerald-100' }}">{{ $p->is_solution ? 'Solution' : 'Mark Solution' }}</button></form>
                        <form method="POST" action="{{ route('admin.forum.posts.destroy', [$topic, $p]) }}" onsubmit="return confirm('Hapus post ini?')"> @csrf @method('DELETE')<button class="px-3 py-1 bg-red-200 rounded">Hapus</button></form>
                    </div>
                </div>
            </div>
            @empty
                <div class="text-center text-gray-500">Belum ada balasan.</div>
            @endforelse
        </div>

        <div class="mt-6 bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">Balas</h3>
            <form method="POST" action="{{ route('admin.forum.posts.store', $topic) }}">
                @csrf
                <textarea name="content" rows="5" class="w-full border-gray-300 rounded" required>{{ old('content') }}</textarea>
                <div class="mt-2"><x-primary-button>Kirim</x-primary-button></div>
            </form>
        </div>
    </div>
</x-app-layout>
