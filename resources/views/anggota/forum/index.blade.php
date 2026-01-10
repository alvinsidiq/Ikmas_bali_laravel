<x-anggota-layout title="Forum" subtitle="Diskusi & tanya jawab antar anggota">
  <div class="space-y-4">
    @foreach(['success'=>'green','error'=>'red','info'=>'amber'] as $k=>$c)
      @if(session($k))<div class="mb-4 p-3 bg-{{ $c }}-100 border border-{{ $c }}-300 rounded">{{ session($k) }}</div>@endif
    @endforeach

    <div class="flex flex-wrap items-end gap-3 mb-4">
      <form method="GET" class="flex flex-wrap gap-2">
        <x-text-input name="q" placeholder="Cari judul/konten" :value="$q" />
        <x-text-input name="kat" placeholder="Kategori" :value="$kat" />
        <select name="status" class="border-gray-300 rounded-md">
          <option value="">Semua</option>
          <option value="open" @selected($status==='open')>Open</option>
          <option value="closed" @selected($status==='closed')>Closed</option>
          <option value="pinned" @selected($status==='pinned')>Pinned</option>
          <option value="solved" @selected($status==='solved')>Solved</option>
        </select>
        <label class="inline-flex items-center gap-2"><input type="checkbox" name="mine" value="1" @checked($mine)> <span>Topik Saya</span></label>
        <x-primary-button>Filter</x-primary-button>
      </form>
      <a href="{{ route('anggota.forum.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Buat Topik</a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      @forelse($topics as $t)
      @php($bannerUrl = \App\Support\MediaPath::url($t->banner_url))
      <div class="bg-white shadow rounded p-4 flex flex-col gap-2">
        @if($t->banner_url)
          <div class="-mx-4 -mt-4 mb-2 h-32 overflow-hidden rounded-t {{ $bannerUrl ? 'cursor-zoom-in' : '' }}"
               @if($bannerUrl) @click="$dispatch('open-image', {src: @js($bannerUrl), alt: @js('Banner '.$t->judul)})" @endif>
            <x-media-img :src="$t->banner_url" class="w-full h-full object-cover" alt="banner {{ $t->judul }}" />
          </div>
        @endif
        <div class="flex items-start justify-between gap-3">
          <div>
            <a class="font-semibold text-lg hover:underline" href="{{ route('anggota.forum.show',$t) }}">{{ $t->judul }}</a>
            <div class="text-sm text-gray-600">{{ $t->kategori ?? '-' }}</div>
            <div class="text-xs text-gray-600">Oleh: {{ optional($t->author)->name ?? '—' }}</div>
          </div>
          <div class="flex flex-wrap gap-1">
            @if($t->is_pinned)<span class="px-2 py-1 text-xs rounded bg-amber-100 text-amber-700">Pinned</span>@endif
            @if($t->is_solved)<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Solved</span>@endif
            <span class="px-2 py-1 text-xs rounded {{ $t->is_open ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">{{ $t->is_open ? 'Open' : 'Closed' }}</span>
          </div>
        </div>
        <div class="text-xs text-gray-600">Last: {{ optional($t->last_post_at)->format('d M Y H:i') }} • Posts: {{ $t->posts_count }}</div>
        <div class="mt-2 line-clamp-3 text-sm">{{ \Illuminate\Support\Str::limit(strip_tags($t->body), 140) }}</div>
        @if($t->author_id === auth()->id())
        <div class="mt-3 flex flex-wrap gap-2">
          <a class="px-3 py-1 bg-gray-200 rounded" href="{{ route('anggota.forum.edit',$t) }}">Edit</a>
          <form method="POST" action="{{ route('anggota.forum.toggle-open-self',$t) }}">@csrf<button class="px-3 py-1 rounded {{ $t->is_open ? 'bg-yellow-200' : 'bg-green-200' }}">{{ $t->is_open ? 'Close' : 'Open' }}</button></form>
          <form method="POST" action="{{ route('anggota.forum.destroy',$t) }}" onsubmit="return confirm('Hapus topik?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-300 rounded">Hapus</button></form>
        </div>
        @endif
      </div>
      @empty
        <div class="col-span-full text-center text-gray-500">Belum ada topik.</div>
      @endforelse
    </div>

    <div class="mt-4">{{ $topics->links() }}</div>
  </div>
</x-anggota-layout>
