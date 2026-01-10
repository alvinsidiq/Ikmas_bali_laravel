<x-anggota-layout :title="$album->judul" subtitle="Album dokumentasi">
  <div class="space-y-6">
    @php($coverUrl = \App\Support\MediaPath::url($album->cover_path))
    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white">
      <div class="relative h-56 md:h-64 bg-slate-100 {{ $coverUrl ? 'cursor-zoom-in' : '' }}"
           @if($coverUrl) @click="$dispatch('open-image', {src: @js($coverUrl), alt: @js('Cover '.$album->judul)})" @endif>
        <x-media-img :src="$album->cover_path" class="w-full h-full object-cover" alt="cover {{ $album->judul }}" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/25 to-transparent"></div>
        <div class="absolute bottom-4 left-4 right-4 text-white">
          <div class="flex flex-wrap gap-2 text-xs">
            <span class="px-3 py-1 rounded-full bg-white/90 text-gray-800 font-semibold">{{ optional($album->tanggal_kegiatan)->format('d M Y') }}</span>
            <span class="px-3 py-1 rounded-full bg-white/80 text-gray-800">{{ $album->lokasi ?? '-' }}</span>
          </div>
          <h1 class="mt-2 text-2xl md:text-3xl font-bold leading-tight">{{ $album->judul }}</h1>
        </div>
      </div>

      <div class="p-6 md:p-8 space-y-6">
        <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-700">
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs uppercase text-gray-500">Tags</p>
            <p class="font-semibold text-gray-900">{{ $album->tags ?? '-' }}</p>
          </div>
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs uppercase text-gray-500">Media</p>
            <p class="font-semibold text-gray-900">{{ $album->medias->count() }} item</p>
          </div>
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs uppercase text-gray-500">Lokasi</p>
            <p class="font-semibold text-gray-900">{{ $album->lokasi ?? '-' }}</p>
          </div>
        </div>

        <div class="prose max-w-none prose-p:text-gray-700">
          {!! nl2br(e($album->deskripsi)) !!}
        </div>

        <div>
          <h3 class="font-semibold mb-3">Media ({{ $album->medias->count() }})</h3>
          <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($album->medias as $m)
            @php($mediaUrl = \App\Support\MediaPath::url($m->media_path))
            <div class="border rounded-xl overflow-hidden shadow-sm">
              @if($mediaUrl)
                <button type="button" class="block w-full text-left" @click="$dispatch('open-image', {src: @js($mediaUrl), alt: @js($m->caption ?: 'Media')})">
                  <x-media-img :src="$m->media_path" class="w-full h-44 object-cover cursor-zoom-in" alt="foto {{ $m->caption }}" />
                </button>
              @else
                <x-media-img :src="$m->media_path" class="w-full h-44 object-cover" alt="foto {{ $m->caption }}" />
              @endif
              <div class="p-2 text-sm flex items-center justify-between gap-2">
                <div class="truncate">{{ $m->caption }}</div>
                @if(!\App\Support\MediaPath::isRemote($m->media_path))
                  <a href="{{ route('anggota.dokumentasi.media.download',[$album,$m]) }}" class="text-blue-600 text-xs">Download</a>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        </div>

        <div class="flex flex-wrap gap-2">
          <a href="{{ route('anggota.dokumentasi.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-gray-800 hover:bg-slate-50">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</x-anggota-layout>
