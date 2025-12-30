<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detail Kegiatan</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        <x-media-img :src="$kegiatan->poster_path" class="w-full max-h-96 object-cover rounded mb-4" alt="Poster {{ $kegiatan->judul }}" />
        <div class="grid md:grid-cols-2 gap-4">
            <div><strong>Judul:</strong> {{ $kegiatan->judul }}</div>
            <div><strong>Lokasi:</strong> {{ $kegiatan->lokasi ?? '-' }}</div>
            <div><strong>Mulai:</strong> {{ optional($kegiatan->waktu_mulai)->format('d M Y H:i') }}</div>
            <div><strong>Selesai:</strong> {{ optional($kegiatan->waktu_selesai)->format('d M Y H:i') }}</div>
            <div class="md:col-span-2"><strong>Deskripsi:</strong><div class="prose max-w-none">{!! nl2br(e($kegiatan->deskripsi)) !!}</div></div>
            <div><strong>Status:</strong> {{ $kegiatan->is_published ? 'Published' : 'Draft' }}</div>
            <div><strong>Published At:</strong> {{ optional($kegiatan->published_at)->format('d M Y H:i') }}</div>
            <div><strong>Slug:</strong> {{ $kegiatan->slug }}</div>
        </div>
        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Edit</a>
            <a href="{{ route('admin.kegiatan.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
        </div>
    </div>
</x-app-layout>
