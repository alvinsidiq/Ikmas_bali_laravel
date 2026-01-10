<x-anggota-layout title="Kegiatan Saya" subtitle="Daftar kegiatan yang Anda ikuti">
  <div class="space-y-4">
    @if(session('success'))<div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>@endif
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      @forelse($items as $k)
      @php($posterUrl = \App\Support\MediaPath::url($k->poster_path))
      <div class="bg-white shadow rounded overflow-hidden">
        <x-media-img :src="$k->poster_path" class="w-full h-40 object-cover {{ $posterUrl ? 'cursor-zoom-in' : '' }}" alt="Poster {{ $k->judul }}"
          @if($posterUrl) @click="$dispatch('open-image', {src: @js($posterUrl), alt: @js('Poster '.$k->judul)})" @endif />
        <div class="p-4">
          <h3 class="font-semibold text-lg"><a class="hover:underline" href="{{ route('anggota.kegiatan.show',$k->slug) }}">{{ $k->judul }}</a></h3>
          <div class="text-sm text-gray-600">{{ $k->lokasi ?? '-' }}</div>
          <div class="mt-1 text-xs text-gray-600">{{ optional($k->waktu_mulai)->format('d M Y H:i') }}</div>
          <div class="mt-3 flex flex-wrap gap-2">
            <a href="{{ route('anggota.kegiatan.ics',$k) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Add to Calendar</a>
            <form method="POST" action="{{ route('anggota.kegiatan.unregister',$k) }}" onsubmit="return confirm('Batalkan pendaftaran?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-200 rounded">Batal</button></form>
          </div>
        </div>
      </div>
      @empty
        <div class="col-span-full text-center text-gray-500">Belum ada pendaftaran.</div>
      @endforelse
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-anggota-layout>
