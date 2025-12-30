<x-anggota-layout :title="$kegiatan->judul">
  <div class="space-y-6">
    @if(session('success'))<div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 p-3 bg-red-100 border border-red-300 rounded">{{ session('error') }}</div>@endif
    @if(session('info'))<div class="mb-4 p-3 bg-amber-100 border border-amber-300 rounded">{{ session('info') }}</div>@endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white">
      <div class="relative h-60 md:h-72 bg-slate-100">
        <x-media-img :src="$kegiatan->poster_path" class="w-full h-full object-cover" alt="Poster {{ $kegiatan->judul }}" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/25 to-transparent"></div>
        <div class="absolute bottom-4 left-4 right-4 text-white">
          <div class="flex flex-wrap gap-2 text-xs">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/90 text-gray-800">
              {{ optional($kegiatan->waktu_mulai)->format('d M Y H:i') }}
              @if($kegiatan->waktu_selesai) - {{ optional($kegiatan->waktu_selesai)->format('H:i') }} @endif
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100/90 text-emerald-800 font-semibold">
              {{ optional($kegiatan->waktu_mulai)->isFuture() ? 'Akan datang' : 'Selesai' }}
            </span>
          </div>
          <h1 class="mt-2 text-2xl md:text-3xl font-bold leading-tight">{{ $kegiatan->judul }}</h1>
          <p class="text-sm text-white/80 mt-1">{{ $kegiatan->lokasi ?? '-' }}</p>
        </div>
      </div>

      <div class="p-6 md:p-8 space-y-6">
        <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-700">
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs uppercase text-gray-500">Lokasi</p>
            <p class="font-semibold text-gray-900">{{ $kegiatan->lokasi ?? '-' }}</p>
          </div>
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs uppercase text-gray-500">Waktu</p>
            <p class="font-semibold text-gray-900">{{ optional($kegiatan->waktu_mulai)->format('d M Y H:i') }} @if($kegiatan->waktu_selesai) - {{ optional($kegiatan->waktu_selesai)->format('H:i') }} @endif</p>
          </div>
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs uppercase text-gray-500">Kode Pendaftaran</p>
            <p class="font-semibold text-gray-900">{{ $pivot?->kode ?? 'Belum daftar' }}</p>
          </div>
        </div>

        <div class="prose max-w-none prose-headings:text-gray-900 prose-p:text-gray-700">
          {!! nl2br(e($kegiatan->deskripsi)) !!}
        </div>

        <div class="flex flex-wrap gap-3">
          <a href="{{ route('anggota.kegiatan.ics',$kegiatan) }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500">Add to Calendar</a>
          @php($closed = optional($kegiatan->waktu_mulai)->isPast())
          @if(!$pivot && !$closed)
            <form method="POST" action="{{ route('anggota.kegiatan.register', ['kegiatan' => $kegiatan->id]) }}">@csrf<button class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-500">Daftar</button></form>
          @elseif($pivot)
            <form method="POST" action="{{ route('anggota.kegiatan.unregister',$kegiatan) }}" onsubmit="return confirm('Batalkan pendaftaran?')">@csrf @method('DELETE')<button class="px-4 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">Batal</button></form>
            <span class="px-3 py-1.5 text-xs rounded-full bg-emerald-100 text-emerald-700">Kode: {{ $pivot->kode }}</span>
          @else
            <span class="px-4 py-2 rounded-lg bg-red-50 text-red-700">Pendaftaran ditutup</span>
          @endif
          <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-lg border border-slate-200 text-gray-800 hover:bg-slate-50">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</x-anggota-layout>
