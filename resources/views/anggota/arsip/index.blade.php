<x-anggota-layout title="Arsip" subtitle="Dokumen publik & arsip organisasi">
  <div class="space-y-6">
    <div class="bg-white shadow rounded-xl p-4">
      <form method="GET" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
        <div class="lg:col-span-2">
          <label class="text-xs text-gray-600">Pencarian</label>
          <div class="relative">
            <span class="absolute left-2 top-2.5 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg></span>
            <input name="q" value="{{ $q }}" placeholder="Judul / nomor / ringkasan" class="pl-8 w-full border-gray-300 rounded-md" />
          </div>
        </div>
        <div>
          <label class="text-xs text-gray-600">Kategori</label>
          <input name="kat" value="{{ $kat }}" placeholder="Kategori" class="w-full border-gray-300 rounded-md" />
        </div>
        <div>
          <label class="text-xs text-gray-600">Tahun</label>
          <input name="tahun" value="{{ $th }}" placeholder="Tahun" class="w-full border-gray-300 rounded-md" />
        </div>
        <div>
          <label class="text-xs text-gray-600">Tag</label>
          <input name="tag" value="{{ $tag }}" placeholder="Tag" class="w-full border-gray-300 rounded-md" />
        </div>
        <div>
          <x-primary-button class="w-full sm:w-auto">Filter</x-primary-button>
        </div>
      </form>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($items as $a)
        <div class="bg-white shadow rounded-xl p-4 border border-gray-100">
          <div class="flex items-start justify-between gap-3">
            <div class="space-y-1">
              <div class="flex flex-wrap gap-2 text-xs text-gray-600">
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-800 font-semibold">{{ $a->kategori ?? 'Arsip' }}</span>
                <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">Tahun {{ $a->tahun ?? '-' }}</span>
                @if($a->file_size)
                  <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">{{ number_format($a->file_size/1024,1) }} KB</span>
                @endif
              </div>
              <a href="{{ route('anggota.arsip.show',$a->slug) }}" class="font-semibold hover:underline text-gray-900 block">{{ $a->judul }}</a>
              <div class="text-xs text-gray-600">Nomor: {{ $a->nomor_dokumen ?? '-' }}</div>
              @if($a->ringkasan)
                <div class="text-xs text-gray-600 line-clamp-2">{{ $a->ringkasan }}</div>
              @endif
              @if($a->tags)
                <div class="flex flex-wrap gap-1 pt-1">
                  @foreach(explode(',', (string)$a->tags) as $tg)
                    @if(trim($tg) !== '')
                      <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-700">{{ trim($tg) }}</span>
                    @endif
                  @endforeach
                </div>
              @endif
            </div>
            <div class="text-right text-xs text-gray-500">
              {{ optional($a->published_at)->format('d M Y') }}
            </div>
          </div>
          <div class="mt-4 flex gap-2">
            <a class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded" href="{{ route('anggota.arsip.show',$a->slug) }}">Detail</a>
            @if($a->file_path)
              <a class="px-3 py-1.5 bg-blue-600 text-white rounded" href="{{ route('anggota.arsip.download',$a) }}">Download</a>
            @endif
          </div>
        </div>
      @empty
        <div class="col-span-full">
          <div class="bg-white border border-dashed rounded-xl p-10 text-center text-gray-500">Belum ada arsip.</div>
        </div>
      @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-anggota-layout>
