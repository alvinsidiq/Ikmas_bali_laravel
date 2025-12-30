<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Album: {{ $album->judul }}</h2>
    </x-slot>
    <div class="p-6">
        <div class="bg-white shadow rounded p-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <x-media-img :src="$album->cover_path" class="w-full max-h-96 object-cover rounded" alt="Cover {{ $album->judul }}" />
                </div>
                <div class="space-y-2">
                    <div><strong>Tanggal:</strong> {{ optional($album->tanggal_kegiatan)->format('d M Y') }}</div>
                    <div><strong>Lokasi:</strong> {{ $album->lokasi ?? '-' }}</div>
                    <div><strong>Status:</strong> {{ $album->is_published ? 'Published' : 'Draft' }}</div>
                    <div><strong>Tags:</strong> {{ $album->tags ?? '-' }}</div>
                    <div class="prose max-w-none"><strong>Deskripsi:</strong><div>{!! nl2br(e($album->deskripsi)) !!}</div></div>
                </div>
            </div>

            <hr class="my-6" />

            <h3 class="font-semibold mb-3">Media</h3>
            <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($album->medias as $m)
                @php($mediaUrl = \App\Support\MediaPath::url($m->media_path))
                @if($mediaUrl)
                    <a href="{{ $mediaUrl }}" target="_blank" class="block">
                        <x-media-img :src="$m->media_path" class="w-full h-40 object-cover rounded" :alt="$m->caption" />
                        <div class="mt-1 text-sm">{{ $m->caption }}</div>
                    </a>
                @else
                    <div class="block">
                        <x-media-img :src="$m->media_path" class="w-full h-40 object-cover rounded" :alt="$m->caption" />
                        <div class="mt-1 text-sm">{{ $m->caption }}</div>
                    </div>
                @endif
                @endforeach
            </div>

            <div class="mt-6"><a href="{{ route('admin.dokumentasi.albums.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a></div>
        </div>
    </div>
</x-app-layout>
