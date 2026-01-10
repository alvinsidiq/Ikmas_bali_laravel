<x-anggota-layout title="Pengumuman" subtitle="Informasi resmi & terbaru">
  @php($categoryOptions = $categories ?? \App\Models\Pengumuman::CATEGORY_OPTIONS)
  <div class="space-y-6">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow rounded-xl p-4">
      <div class="flex flex-wrap items-end gap-3">
        <form method="GET" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end flex-1">
          <div class="lg:col-span-2">
            <label class="text-xs text-gray-600">Pencarian</label>
            <div class="relative">
              <span class="absolute left-2 top-2.5 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg></span>
              <input name="q" value="{{ $q }}" placeholder="Judul / kategori / isi" class="pl-8 w-full border-gray-300 rounded-md" />
            </div>
          </div>
          <div>
            <label class="text-xs text-gray-600">Kategori</label>
            <select name="kat" class="w-full border-gray-300 rounded-md">
              <option value="">Semua Kategori</option>
              @foreach($categoryOptions as $value => $label)
                <option value="{{ $value }}" @selected($kat === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <label class="inline-flex items-center gap-2"><input type="checkbox" name="pinned" value="1" @checked($pinnedOnly)> <span class="text-sm">Pinned</span></label>
          <label class="inline-flex items-center gap-2"><input type="checkbox" name="unread" value="1" @checked($unreadOnly)> <span class="text-sm">Belum dibaca</span></label>
          <div><x-primary-button class="w-full sm:w-auto">Filter</x-primary-button></div>
        </form>
        <form method="POST" action="{{ route('anggota.pengumuman.mark-all-read') }}" onsubmit="return confirm('Tandai semua terbaca?')">
          @csrf
          <button class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded">Tandai semua terbaca</button>
        </form>
      </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($items as $p)
      @php($read = in_array($p->id, $readIds))
      <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100 {{ $read ? '' : 'ring-2 ring-amber-300' }}">
        <div class="relative h-40 bg-gray-100">
          <x-media-img :src="$p->cover_path" class="w-full h-full object-cover" alt="Cover {{ $p->judul }}" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
          <div class="absolute top-2 left-2 flex gap-2">
            @if($p->is_pinned)
              <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700">Pinned</span>
            @endif
            @if(!$read)
              <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700">Baru</span>
            @endif
          </div>
        </div>
        <div class="p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <a class="font-semibold text-lg hover:underline" href="{{ route('anggota.pengumuman.show',$p->slug) }}">{{ $p->judul }}</a>
              <div class="text-sm text-gray-600">{{ $p->kategori ?? '-' }}</div>
            </div>
            
          </div>
          <div class="mt-2 text-xs text-gray-600">{{ optional($p->published_at)->format('d M Y H:i') }}</div>
          <div class="mt-3 line-clamp-2 text-sm">{{ \Illuminate\Support\Str::limit(strip_tags($p->isi), 140) }}</div>
          <div class="mt-4 flex gap-2">
            <a href="{{ route('anggota.pengumuman.show',$p->slug) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded">Baca</a>
          </div>
        </div>
      </div>
      @empty
        <div class="col-span-full text-center text-gray-500">Belum ada pengumuman.</div>
      @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-anggota-layout>
