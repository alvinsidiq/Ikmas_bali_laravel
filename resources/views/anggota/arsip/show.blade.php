<x-anggota-layout :title="$arsip->judul" subtitle="Detail arsip">
  <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white p-6 md:p-8 space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
      <div>
        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
          <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-semibold">{{ $arsip->kategori ?? 'Arsip' }}</span>
          <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700">Tahun {{ $arsip->tahun ?? '-' }}</span>
          <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700">{{ optional($arsip->published_at)->format('d M Y') }}</span>
        </div>
        <h1 class="mt-3 text-2xl md:text-3xl font-bold leading-tight text-gray-900">{{ $arsip->judul }}</h1>
      </div>
      @if($arsip->file_path)
        <a href="{{ route('anggota.arsip.download',$arsip) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
          Download
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
        </a>
      @endif
    </div>

    <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-700">
      <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
        <p class="text-xs uppercase text-gray-500">Nomor Dokumen</p>
        <p class="font-semibold text-gray-900">{{ $arsip->nomor_dokumen ?? '-' }}</p>
      </div>
      <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
        <p class="text-xs uppercase text-gray-500">Slug</p>
        <p class="font-mono text-sm text-gray-900">{{ $arsip->slug }}</p>
      </div>
      <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
        <p class="text-xs uppercase text-gray-500">File</p>
        <p class="font-semibold text-gray-900">
          @if($arsip->file_path)
            {{ $arsip->file_name }} ({{ number_format($arsip->file_size/1024,1) }} KB)
          @else
            -
          @endif
        </p>
      </div>
    </div>

    @if($arsip->tags)
      <div class="flex flex-wrap gap-2">
        @foreach(explode(',', (string)$arsip->tags) as $tag)
          @if(trim($tag) !== '')
            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">{{ trim($tag) }}</span>
          @endif
        @endforeach
      </div>
    @endif

    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
      <p class="text-xs uppercase text-gray-500 mb-2">Ringkasan</p>
      <div class="prose max-w-none prose-p:text-gray-700">
        {!! nl2br(e($arsip->ringkasan)) !!}
      </div>
    </div>

    <div class="flex flex-wrap gap-2">
      <a href="{{ route('anggota.arsip.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-gray-800 hover:bg-slate-50">Kembali</a>
    </div>
  </div>
</x-anggota-layout>
