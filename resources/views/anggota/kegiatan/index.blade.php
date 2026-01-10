<x-anggota-layout title="Kegiatan" subtitle="Ikuti kegiatan terbaru komunitas">
  <div class="space-y-6">
    @if(session('success'))<div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="p-3 bg-red-50 border border-red-200 text-red-800 rounded">{{ session('error') }}</div>@endif
    @if(session('info'))<div class="p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded">{{ session('info') }}</div>@endif

    <div class="bg-white shadow rounded-xl p-4">
      <form method="GET" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
        <div>
          <label class="text-xs text-gray-600">Pencarian</label>
          <div class="relative">
            <span class="absolute left-2 top-2.5 text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
            </span>
            <input name="q" value="{{ $q }}" placeholder="Judul / lokasi / teks" class="pl-8 w-full border-gray-300 rounded-md" />
          </div>
        </div>
        <div>
          <label class="text-xs text-gray-600">Waktu</label>
          <select name="w" class="w-full border-gray-300 rounded-md">
            <option value="">Semua</option>
            <option value="upcoming" @selected($w==='upcoming')>Akan Datang</option>
            <option value="past" @selected($w==='past')>Selesai</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-gray-600">Status</label>
          <select name="status" class="w-full border-gray-300 rounded-md">
            <option value="">Semua</option>
            <option value="published" @selected($status==='published')>Published</option>
            <option value="draft" @selected($status==='draft')>Draft</option>
          </select>
        </div>
        <div class="sm:col-span-2 lg:col-span-1 flex gap-2">
          <x-primary-button class="w-full sm:w-auto">Filter</x-primary-button>
          <a href="{{ route('anggota.kegiatan.mine') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">Kegiatan Saya</a>
        </div>
      </form>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($items as $k)
      <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
        <div class="relative h-40 bg-gray-100">
          <x-media-img :src="$k->poster_path" class="w-full h-full object-cover" alt="Poster {{ $k->judul }}" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
          <div class="absolute top-2 left-2 flex gap-2">
            <span class="px-2 py-0.5 text-xs rounded-full bg-white/90 text-gray-700">{{ optional($k->waktu_mulai)->format('d M Y') }}</span>
            @if(!$k->is_published)
              <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700">Draft</span>
            @endif
            @if(optional($k->waktu_mulai)->isFuture())
              <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700">Upcoming</span>
            @else
              <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-700">Selesai</span>
            @endif
          </div>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-lg"><a class="hover:underline" href="{{ route('anggota.kegiatan.show',$k->slug) }}">{{ $k->judul }}</a></h3>
          <div class="mt-1 flex items-center gap-2 text-xs text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657l4.243 4.243"></path></svg>
            <span>{{ $k->lokasi ?? '-' }}</span>
          </div>
          <div class="mt-1 text-xs text-gray-600">{{ optional($k->waktu_mulai)->format('d M Y H:i') }} @if($k->waktu_selesai) - {{ optional($k->waktu_selesai)->format('d M Y H:i') }} @endif</div>
          <div class="mt-2 text-xs text-gray-600">Pendaftar: {{ $k->participants_count ?? 0 }}</div>
          <div class="mt-3 line-clamp-2 text-sm">{{ \Illuminate\Support\Str::limit(strip_tags($k->deskripsi), 120) }}</div>
          <div class="mt-4 flex flex-wrap gap-2 items-center">
            <a href="{{ route('anggota.kegiatan.show',$k->slug) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded">Detail</a>
            @php($joined = in_array($k->id, $mine))
            @php($closed = optional($k->waktu_mulai)->isPast())
            @php($isPublished = (bool) $k->is_published)
            @if($joined)
              <span class="px-3 py-1.5 bg-slate-200 rounded text-sm">Sudah terdaftar</span>
              <a href="{{ route('anggota.kegiatan.mine') }}" class="px-3 py-1.5 bg-white border border-slate-200 rounded text-sm">Lihat daftar saya</a>
            @elseif(!$isPublished)
              <span class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded text-sm">Belum dipublish</span>
            @elseif($closed)
              <span class="px-3 py-1.5 bg-red-50 text-red-700 rounded text-sm">Pendaftaran ditutup</span>
            @else
              <form method="POST" action="{{ route('anggota.kegiatan.register', ['kegiatan' => $k->id]) }}" class="inline-flex">
                @csrf
                <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-500">Daftar</button>
              </form>
            @endif
          </div>
        </div>
      </div>
      @empty
        <div class="col-span-full">
          <div class="bg-white border border-dashed rounded-xl p-10 text-center text-gray-500">Belum ada kegiatan.</div>
        </div>
      @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-anggota-layout>
