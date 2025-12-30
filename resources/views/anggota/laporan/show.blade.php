<x-anggota-layout :title="'Laporan '.$laporan->kode">
  <div class="space-y-6">
    @foreach(['success'=>'green','error'=>'red','info'=>'amber'] as $k=>$c)
      @if(session($k))<div class="mb-4 p-3 bg-{{ $c }}-100 border border-{{ $c }}-300 rounded">{{ session($k) }}</div>@endif
    @endforeach

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 space-y-6">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <p class="text-xs uppercase text-gray-500">Laporan</p>
          <h1 class="text-2xl font-bold text-gray-900">{{ $laporan->judul }}</h1>
          <p class="text-sm text-gray-600">Kode: {{ $laporan->kode }} • Jenis: {{ $laporan->kategori ?? '-' }}</p>
          <div class="mt-2 text-xs text-gray-500">Dibuat {{ $laporan->created_at->format('d M Y H:i') }} • Update {{ $laporan->updated_at->format('d M Y H:i') }}</div>
        </div>
        <div class="text-right space-y-2">
          @php($cls = ['open'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'][$laporan->status] ?? 'bg-gray-200 text-gray-700')
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $cls }}">{{ ucfirst(str_replace('_',' ',$laporan->status)) }}</span>
          @if($laporan->resolved_at)
            <div class="text-xs text-gray-600">Ditutup: {{ $laporan->resolved_at->format('d M Y H:i') }}</div>
          @endif
          @if($laporan->rejected_reason)
            <div class="text-xs text-red-700">Ditolak: {{ $laporan->rejected_reason }}</div>
          @endif
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-3">
          <div class="font-semibold">Detail</div>
          <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-gray-700">
            <p class="text-xs uppercase text-gray-500">Jenis</p>
            <p class="font-semibold text-gray-900">{{ $laporan->kategori ?? '-' }}</p>
            <p class="mt-3 text-xs uppercase text-gray-500">Status</p>
            <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_',' ',$laporan->status)) }}</p>
          </div>
          @if($laporan->rejected_reason)
            <div class="rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700">
              Alasan ditolak: {{ $laporan->rejected_reason }}
            </div>
          @endif
        </div>
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <div class="font-semibold">Lampiran ({{ $laporan->attachments_count }})</div>
          </div>
          <div class="space-y-2">
            @forelse($laporan->attachments as $a)
              <div class="flex items-center justify-between gap-3 border rounded p-2">
                <div class="flex-1 min-w-0">
                  <div class="truncate text-sm">{{ $a->file_name }} <span class="text-xs text-gray-500">({{ number_format(($a->file_size ?? 0)/1024,1) }} KB)</span></div>
                  @php($url = $a->file_path ? Storage::disk('public')->url($a->file_path) : null)
                  @if($url && str_contains(strtolower($a->file_mime ?? $a->file_name ?? ''),'pdf'))
                    <div class="mt-2 border rounded overflow-hidden bg-gray-50">
                      <iframe src="{{ $url }}" class="w-full h-64" title="Preview {{ $a->file_name }}"></iframe>
                    </div>
                  @endif
                </div>
                <div class="flex gap-2 flex-shrink-0">
                  <a class="px-2 py-1 text-xs bg-blue-600 text-white rounded" href="{{ route('anggota.laporan.attachment.download',[$laporan,$a]) }}">Download</a>
                </div>
              </div>
            @empty
              <div class="text-sm text-gray-500">Belum ada lampiran.</div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="space-y-3">
        <h3 class="font-semibold">Timeline Komentar ({{ $laporan->comments_count }})</h3>
        <div class="relative">
          <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-slate-200"></div>
          <div class="space-y-4">
            @forelse($laporan->comments as $c)
              <div class="relative pl-8">
                <span class="absolute left-1 top-1.5 w-4 h-4 rounded-full bg-white border-2 border-indigo-400"></span>
                <div class="bg-slate-50 rounded p-3 border">
                  <div class="text-xs text-gray-600">{{ $c->user->name }} • {{ $c->created_at->format('d M Y H:i') }}</div>
                  <div class="mt-2">{!! nl2br(e($c->body)) !!}</div>
                </div>
              </div>
            @empty
              <div class="text-gray-500">Belum ada komentar.</div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="mt-4 flex gap-2">
        <a href="{{ route('anggota.laporan.index') }}" class="px-4 py-2 bg-gray-100 border border-slate-200 rounded hover:bg-white">Kembali</a>
      </div>
    </div>
  </div>
</x-anggota-layout>
