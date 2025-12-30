<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detail Laporan</h2>
    </x-slot>

    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 space-y-5">
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

            <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-700">
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <p class="text-xs uppercase text-gray-500">Pelapor</p>
                    <p class="font-semibold text-gray-900">{{ optional($laporan->reporter)->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ optional($laporan->reporter)->email }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <p class="text-xs uppercase text-gray-500">Status</p>
                    <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_',' ',$laporan->status)) }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <p class="text-xs uppercase text-gray-500">Timeline</p>
                    <p class="font-semibold text-gray-900">Lampiran: {{ $laporan->attachments_count }} • Komentar: {{ $laporan->comments_count }}</p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs uppercase text-gray-500 mb-2">Deskripsi</p>
                <div class="prose max-w-none prose-p:text-gray-700">
                    {!! nl2br(e($laporan->deskripsi)) !!}
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Lampiran ({{ $laporan->attachments_count }})</h3>
                </div>
                <div class="space-y-2">
                    @forelse($laporan->attachments as $a)
                        @php($url = $a->file_path ? Storage::disk('public')->url($a->file_path) : null)
                        <div class="flex items-center justify-between gap-3 border rounded p-2 bg-white">
                            <div class="flex-1 min-w-0">
                                <div class="truncate text-sm font-semibold text-gray-900">{{ $a->file_name }} <span class="text-xs text-gray-500">({{ number_format(($a->file_size ?? 0)/1024,1) }} KB)</span></div>
                                @if($url && str_contains(strtolower($a->file_mime ?? $a->file_name ?? ''),'pdf'))
                                    <div class="mt-2 border rounded overflow-hidden bg-gray-50">
                                        <iframe src="{{ $url }}" class="w-full h-64" title="Preview {{ $a->file_name }}"></iframe>
                                    </div>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">Uploader: {{ optional($a->uploader)->name ?? '-' }}</div>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <a class="px-3 py-1 text-xs bg-blue-600 text-white rounded" href="{{ route('admin.laporan.attachment.download',[$laporan,$a]) }}">Download</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Tidak ada lampiran.</div>
                    @endforelse
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
                                    <div class="text-xs text-gray-600">{{ optional($c->user)->name ?? 'User' }} • {{ $c->created_at->format('d M Y H:i') }}</div>
                                    <div class="mt-2">{!! nl2br(e($c->body)) !!}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-500">Belum ada komentar.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
