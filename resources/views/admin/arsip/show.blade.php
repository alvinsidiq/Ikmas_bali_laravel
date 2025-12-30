<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detail Arsip</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <h3 class="text-2xl font-semibold text-gray-900">{{ $arsip->judul }}</h3>
            @if($arsip->kategori)
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">{{ $arsip->kategori }}</span>
            @endif
            @if($arsip->tahun)
                <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">Tahun {{ $arsip->tahun }}</span>
            @endif
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $arsip->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                {{ $arsip->is_published ? 'Published' : 'Draft' }}
            </span>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div><strong>Nomor Dokumen:</strong> {{ $arsip->nomor_dokumen ?? '-' }}</div>
            <div><strong>Slug:</strong> {{ $arsip->slug }}</div>
            <div><strong>Published At:</strong> {{ optional($arsip->published_at)->format('d M Y H:i') }}</div>
            <div><strong>Tags:</strong> {{ $arsip->tags ?? '-' }}</div>
            <div class="md:col-span-2"><strong>Ringkasan:</strong><div class="prose max-w-none">{!! nl2br(e($arsip->ringkasan)) !!}</div></div>
            <div class="md:col-span-2 flex flex-wrap items-center gap-3">
                <div><strong>File:</strong> @if($arsip->file_path) {{ $arsip->file_name }} ({{ number_format($arsip->file_size/1024,1) }} KB) @else - @endif</div>
                @if($arsip->file_path)
                    <a href="{{ route('admin.arsip.download', $arsip) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Download</a>
                @endif
            </div>
        </div>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.arsip.edit', $arsip) }}" class="px-4 py-2 bg-gray-200 rounded">Edit</a>
            <a href="{{ route('admin.arsip.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
        </div>
    </div>
</x-app-layout>
