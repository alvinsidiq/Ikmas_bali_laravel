<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detail Pengumuman</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        <x-media-img :src="$pengumuman->cover_path" class="w-full max-h-96 object-cover rounded mb-4" alt="Cover {{ $pengumuman->judul }}" />
        <div class="grid md:grid-cols-2 gap-4">
            <div><strong>Judul:</strong> {{ $pengumuman->judul }}</div>
            <div><strong>Kategori:</strong> {{ $pengumuman->kategori ?? '-' }}</div>
            <div><strong>Status:</strong> {{ $pengumuman->is_published ? 'Published' : 'Draft' }}</div>
            <div><strong>Pinned:</strong> {{ $pengumuman->is_pinned ? 'Ya' : 'Tidak' }}</div>
            <div><strong>Published At:</strong> {{ optional($pengumuman->published_at)->format('d M Y H:i') }}</div>
            <div><strong>Slug:</strong> {{ $pengumuman->slug }}</div>
            <div class="md:col-span-2"><strong>Isi:</strong><div class="prose max-w-none">{!! nl2br(e($pengumuman->isi)) !!}</div></div>
        </div>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.pengumuman.edit', $pengumuman) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Edit</a>
            <a href="{{ route('admin.pengumuman.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
        </div>
    </div>
</x-app-layout>
