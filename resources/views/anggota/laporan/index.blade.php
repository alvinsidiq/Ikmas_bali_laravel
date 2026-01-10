<x-anggota-layout title="Laporan" subtitle="Lihat arsip laporan anggota seperti pengaduan, saran, dan tindak lanjutnya">
  @php($categoryFilters = ['pengaduan'=>'Pengaduan','saran'=>'Saran','fasilitas'=>'Fasilitas','keuangan'=>'Keuangan','kegiatan'=>'Kegiatan','lainnya'=>'Lainnya'])
  <div class="space-y-6">
    @foreach(['success'=>'green','error'=>'red','info'=>'amber'] as $k=>$c)
      @if(session($k))<div class="mb-4 p-3 bg-{{ $c }}-100 border border-{{ $c }}-300 rounded">{{ session($k) }}</div>@endif
    @endforeach

    <div class="bg-white shadow rounded-xl p-4">
      <div class="space-y-3">
        <form method="GET" class="grid gap-3 md:grid-cols-2 xl:grid-cols-4 items-end">
          <div class="md:col-span-2">
            <label class="text-xs text-gray-600">Pencarian</label>
            <div class="relative">
              <span class="absolute left-2 top-2.5 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg></span>
              <input name="q" value="{{ $q }}" placeholder="Judul / kode / teks" class="pl-8 w-full border-gray-300 rounded-md" />
            </div>
          </div>
          <div>
            <label class="text-xs text-gray-600">Jenis Laporan</label>
            <select name="jenis" class="w-full border-gray-300 rounded-md">
              <option value="">Semua Jenis</option>
              @foreach($categoryFilters as $key=>$label)
                <option value="{{ $key }}" @selected($jenis===$key)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="text-xs text-gray-600">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-md">
              <option value="">Semua Status</option>
              @foreach(['open'=>'Open','in_progress'=>'In Progress','resolved'=>'Resolved','rejected'=>'Rejected'] as $sv=>$sl)
                <option value="{{ $sv }}" @selected($st===$sv)>{{ $sl }}</option>
              @endforeach
            </select>
          </div>
          <div class="md:col-span-2 xl:col-span-1">
            <x-primary-button class="w-full">Filter</x-primary-button>
          </div>
        </form>
        <div class="flex flex-wrap gap-2">
          @php($query = request()->query())
          <a href="{{ route('anggota.laporan.index', array_merge($query, ['jenis'=>null])) }}"
             class="px-3 py-2 rounded border text-sm {{ $jenis ? 'border-gray-200 text-gray-600 hover:bg-gray-50' : 'border-indigo-200 text-indigo-700 bg-indigo-50' }}">
            Semua
          </a>
          @foreach($categoryFilters as $key=>$label)
            <a href="{{ route('anggota.laporan.index', array_merge($query, ['jenis'=>$key])) }}"
               class="px-3 py-2 rounded border text-sm {{ $jenis===$key ? 'border-indigo-200 text-indigo-700 bg-indigo-50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
              {{ $label }}
            </a>
          @endforeach
        </div>
      </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($items as $r)
        @php($cls = ['open'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'][$r->status] ?? 'bg-gray-200')
        <div class="bg-white shadow rounded-xl p-4 border border-gray-100">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-xs text-gray-500 font-mono">{{ $r->kode }}</div>
              <a class="font-semibold hover:underline" href="{{ route('anggota.laporan.show',$r) }}">{{ $r->judul }}</a>
              <div class="mt-1 text-xs text-gray-600">Jenis: {{ $r->kategori ?? '-' }}</div>
            </div>
            <span class="px-2 py-1 text-xs rounded {{ $cls }}">{{ ucfirst(str_replace('_',' ',$r->status)) }}</span>
          </div>
          <div class="mt-3 text-xs text-gray-600">Lampiran: {{ $r->attachments_count }} • Komentar: {{ $r->comments_count }} • Update: {{ $r->updated_at->format('d M Y H:i') }}</div>
          <div class="mt-4 flex gap-2">
            <a class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded" href="{{ route('anggota.laporan.show',$r) }}">Detail</a>
          </div>
        </div>
      @empty
        <div class="col-span-full">
          <div class="bg-white border border-dashed rounded-xl p-10 text-center text-gray-500">Belum ada laporan.</div>
        </div>
      @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-anggota-layout>
