<x-anggota-layout title="Dokumentasi" subtitle="Album foto & media kegiatan">
  <div class="space-y-6">
    <div class="bg-white shadow rounded-xl p-4">
      <form method="GET" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
        <x-text-input name="q" placeholder="Cari judul/lokasi/tags/desc" :value="$q" />
        <x-text-input name="lokasi" placeholder="Lokasi" :value="$lokasi" />
        <x-text-input name="tahun" placeholder="Tahun" :value="$tahun" />
        <x-text-input name="tag" placeholder="Tag" :value="$tag" />
        <div><x-primary-button class="w-full sm:w-auto">Filter</x-primary-button></div>
      </form>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($albums as $a)
      @php($coverUrl = \App\Support\MediaPath::url($a->cover_path))
      <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
        <div class="relative h-44 bg-gray-100 {{ $coverUrl ? 'cursor-zoom-in' : '' }}"
             @if($coverUrl) @click="$dispatch('open-image', {src: @js($coverUrl), alt: @js('Cover '.$a->judul)})" @endif>
          <x-media-img :src="$a->cover_path" class="w-full h-full object-cover" alt="cover {{ $a->judul }}" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
          <div class="absolute top-2 left-2 flex gap-2">
            <span class="px-2 py-0.5 text-xs rounded-full bg-white/90 text-gray-700">{{ $a->media_count }} media</span>
          </div>
        </div>
        <div class="p-4">
          <a class="font-semibold text-lg hover:underline" href="{{ route('anggota.dokumentasi.show',$a) }}">{{ $a->judul }}</a>
          <div class="text-sm text-gray-600">{{ $a->lokasi ?? '-' }} • {{ optional($a->tanggal_kegiatan)->format('d M Y') }}</div>
          <div class="mt-1 text-xs text-gray-600">Views: {{ $a->view_count }}</div>
          <div class="mt-3 line-clamp-2 text-sm">{{ \Illuminate\Support\Str::limit(strip_tags($a->deskripsi), 120) }}</div>
          <div class="mt-4">
            <a href="{{ route('anggota.dokumentasi.show',$a) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded">Lihat Album</a>
          </div>
        </div>
      </div>
      @empty
        <div class="col-span-full text-center text-gray-500">Belum ada album.</div>
      @endforelse
    </div>

    <div class="mt-4">{{ $albums->links() }}</div>
  </div>
</x-anggota-layout>
