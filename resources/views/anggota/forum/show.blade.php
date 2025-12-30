<x-anggota-layout :title="$topic->judul">
  <div class="space-y-6">
    @foreach(['success'=>'green','error'=>'red','info'=>'amber'] as $k=>$c)
      @if(session($k))<div class="mb-4 p-3 bg-{{ $c }}-100 border border-{{ $c }}-300 rounded">{{ session($k) }}</div>@endif
    @endforeach

    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white">
      @if($topic->banner_url)
        <div class="relative h-56 md:h-64">
          <x-media-img :src="$topic->banner_url" class="w-full h-full object-cover" alt="banner {{ $topic->judul }}" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/25 to-transparent"></div>
          <div class="absolute bottom-4 left-4 right-4 text-white">
            <div class="flex flex-wrap gap-2 text-xs">
              @if($topic->is_pinned)<span class="px-3 py-1 rounded-full bg-amber-100/90 text-amber-800 font-semibold">Pinned</span>@endif
              <span class="px-3 py-1 rounded-full bg-white/80 text-gray-800">{{ $topic->kategori ?? 'Umum' }}</span>
              <span class="px-3 py-1 rounded-full bg-white/80 text-gray-800">{{ $topic->is_open ? 'Open' : 'Closed' }}</span>
            </div>
            <h1 class="mt-2 text-2xl md:text-3xl font-bold leading-tight">{{ $topic->judul }}</h1>
          </div>
        </div>
      @endif

      <div class="p-6 md:p-8 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div class="text-sm text-gray-600">
            Kategori: <span class="font-semibold text-gray-900">{{ $topic->kategori ?? '-' }}</span><br>
            Posts: {{ $topic->posts_count }} • Last: {{ optional($topic->last_post_at)->format('d M Y H:i') }}
          </div>
          <div class="flex flex-wrap gap-2">
            @if($topic->author_id === auth()->id())
              <form method="POST" action="{{ route('anggota.forum.toggle-open-self',$topic) }}">@csrf<button class="px-3 py-1 rounded {{ $topic->is_open ? 'bg-yellow-100 text-yellow-800' : 'bg-emerald-100 text-emerald-700' }}">{{ $topic->is_open ? 'Close' : 'Open' }}</button></form>
              <a class="px-3 py-1 border border-slate-200 rounded" href="{{ route('anggota.forum.edit',$topic) }}">Edit Topik</a>
            @endif
          </div>
        </div>
        <div class="prose max-w-none prose-p:text-gray-700 prose-headings:text-gray-900">{!! nl2br(e($topic->body)) !!}</div>
      </div>
    </div>

    <div class="space-y-4">
      @forelse($topic->posts as $p)
      <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-4">
        <div class="flex justify-between items-start">
          <div>
            <div class="text-sm text-gray-600">{{ $p->user->name }} • {{ $p->created_at->format('d M Y H:i') }}</div>
            <div class="mt-2">{!! nl2br(e($p->content)) !!}</div>
            @if($p->is_solution)
              <div class="mt-2 inline-block px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Solution</div>
            @endif
          </div>
          <div class="flex flex-wrap gap-2">
            @if($topic->author_id === auth()->id())
              <form method="POST" action="{{ route('anggota.forum.posts.mark-solution',[$topic,$p]) }}">@csrf<button class="px-3 py-1 rounded {{ $p->is_solution ? 'bg-slate-200' : 'bg-emerald-200' }}">{{ $p->is_solution ? 'Solution' : 'Mark Solution' }}</button></form>
            @endif
            @if($p->user_id === auth()->id())
              <form method="POST" action="{{ route('anggota.forum.posts.update',[$topic,$p]) }}" onsubmit="return confirm('Simpan perubahan?')">
                @csrf @method('PUT')
                <input type="hidden" name="content" value="{{ $p->content }}">
                <button class="px-3 py-1 bg-gray-200 rounded">Simpan (Quick)</button>
              </form>
              <form method="POST" action="{{ route('anggota.forum.posts.destroy',[$topic,$p]) }}" onsubmit="return confirm('Hapus post ini?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-200 rounded">Hapus</button></form>
            @endif
          </div>
        </div>
      </div>
      @empty
        <div class="text-center text-gray-500">Belum ada balasan.</div>
      @endforelse
    </div>

    @if($topic->is_open)
    <div class="mt-6 bg-white shadow-sm border border-slate-200 rounded-xl p-4">
      <h3 class="font-semibold mb-2">Balas</h3>
      <form method="POST" action="{{ route('anggota.forum.posts.store',$topic) }}">
        @csrf
        <textarea name="content" rows="5" class="w-full border-gray-300 rounded" required>{{ old('content') }}</textarea>
        <div class="mt-2"><x-primary-button>Kirim</x-primary-button></div>
      </form>
    </div>
    @else
      <div class="mt-6 p-3 bg-amber-100 border border-amber-300 rounded">Topik ditutup. Tidak bisa menambah balasan.</div>
    @endif

    <div class="mt-6"><a href="{{ route('anggota.forum.index') }}" class="px-4 py-2 bg-gray-100 border border-slate-200 rounded hover:bg-white">Kembali</a></div>
  </div>
</x-anggota-layout>
