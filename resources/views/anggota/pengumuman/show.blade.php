<x-anggota-layout :title="$pengumuman->judul">
  <div class="space-y-6">
    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white">
      <div class="relative h-56 md:h-72 bg-slate-100">
        <x-media-img :src="$pengumuman->cover_path" class="w-full h-full object-cover" alt="Cover {{ $pengumuman->judul }}" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
        <div class="absolute bottom-4 left-4 right-4 text-white">
          <div class="flex flex-wrap items-center gap-2 text-xs">
            @if($pengumuman->is_pinned)
              <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100/90 text-amber-800 font-semibold">Pinned</span>
            @endif
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/90 text-gray-800 font-semibold">
              {{ $pengumuman->kategori ?? 'Umum' }}
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/80 text-gray-800">
              {{ optional($pengumuman->published_at)->format('d M Y H:i') }}
            </span>
          </div>
          <h1 class="mt-3 text-2xl md:text-3xl font-bold leading-tight">{{ $pengumuman->judul }}</h1>
        </div>
      </div>

      <div class="p-6 md:p-8 space-y-6">
        <div class="flex flex-wrap gap-3 text-sm text-gray-600">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-semibold">
              {{ Str::substr($pengumuman->kategori ?? 'U', 0, 1) }}
            </span>
            <div>
              <p class="text-xs uppercase text-gray-500">Kategori</p>
              <p class="font-semibold text-gray-900">{{ $pengumuman->kategori ?? 'Umum' }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <div>
              <p class="text-xs uppercase text-gray-500">Dipublikasikan</p>
              <p class="font-semibold text-gray-900">{{ optional($pengumuman->published_at)->format('d M Y H:i') }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <div>
              <p class="text-xs uppercase text-gray-500">Slug</p>
              <p class="font-mono text-sm text-gray-800">{{ $pengumuman->slug }}</p>
            </div>
          </div>
        </div>

        <div class="prose max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-a:text-indigo-600">
          {!! nl2br(e($pengumuman->isi)) !!}
        </div>

        <div class="flex flex-wrap gap-2">
          <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-lg border border-slate-200 text-gray-800 hover:bg-slate-50">Kembali</a>
          <a href="{{ route('anggota.pengumuman.index') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">Lihat semua pengumuman</a>
        </div>
      </div>
    </div>
  </div>
</x-anggota-layout>
