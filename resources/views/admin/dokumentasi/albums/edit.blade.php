<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Album: {{ $album->judul }}</h2>
    </x-slot>
    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-3">Informasi Album</h3>
            @include('admin.dokumentasi.albums._form', ['album' => $album])
        </div>

        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-3">Unggah Media</h3>
      <form method="POST" enctype="multipart/form-data" action="/admin/dokumentasi/albums/{{ $album->id }}/media">
                @csrf
                <input type="file" name="media[]" accept="image/*" multiple class="block w-full border-gray-300 rounded" required />
                <p class="text-xs text-gray-500 mt-2">Dukungan: JPG, JPEG, PNG, WEBP (max 8MB/berkas). Anda bisa memilih banyak file.</p>
                <div class="mt-2"><x-primary-button>Upload</x-primary-button></div>
            </form>
        </div>

        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-3">Media ({{ $album->medias->count() }})</h3>
            @if($album->medias->isEmpty())
                <div class="text-gray-500">Belum ada media.</div>
            @else
                <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($album->medias as $m)
                        @php($mediaUrl = \App\Support\MediaPath::url($m->media_path))
                        <div class="border rounded overflow-hidden">
                            @if($mediaUrl)
                                <x-media-img :src="$m->media_path" class="w-full h-40 object-cover" :alt="$m->caption" />
                            @else
                                <x-media-img :src="$m->media_path" class="w-full h-40 object-cover" :alt="$m->caption" />
                            @endif
                            <div class="p-3 space-y-2">
                                @if($m->is_cover)
                                    <div class="text-xs px-2 py-1 rounded bg-amber-100 text-amber-700 inline-block">Cover</div>
                                @endif
                                <form method="POST" action="{{ route('admin.dokumentasi.albums.media.update', [$album, $m]) }}">
                                    @csrf
                                    @method('PUT')
                                    <x-input-label value="Caption" />
                                    <x-text-input name="caption" type="text" class="mt-1 block w-full" :value="$m->caption" />
                                    <x-input-label class="mt-2" value="Urutan" />
                                    <x-text-input name="sort_order" type="number" min="0" class="mt-1 block w-full" :value="$m->sort_order" />
                                    <div class="mt-2"><x-primary-button>Simpan</x-primary-button></div>
                                </form>
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.dokumentasi.albums.media.set-cover', [$album, $m]) }}"> @csrf<button class="px-3 py-1 rounded {{ $m->is_cover ? 'bg-slate-200' : 'bg-amber-200' }}">{{ $m->is_cover ? 'Sudah Cover' : 'Jadikan Cover' }}</button></form>
                                    @if(!\App\Support\MediaPath::isRemote($m->media_path))
                                        <a href="{{ route('admin.dokumentasi.albums.media.download', [$album, $m]) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Download</a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.dokumentasi.albums.media.destroy', [$album, $m]) }}" onsubmit="return confirm('Hapus media ini?')"> @csrf @method('DELETE')<button class="px-3 py-1 bg-red-200 rounded">Hapus</button></form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
