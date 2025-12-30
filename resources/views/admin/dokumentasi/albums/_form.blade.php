@php($editing = isset($album))
<form method="POST" action="{{ $editing ? route('admin.dokumentasi.albums.update', $album) : route('admin.dokumentasi.albums.store') }}">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="judul" value="Judul Album" />
            <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul', $album->judul ?? '')" required />
            <x-input-error :messages="$errors->get('judul')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="tanggal_kegiatan" value="Tanggal Kegiatan" />
            <x-text-input id="tanggal_kegiatan" name="tanggal_kegiatan" type="date" class="mt-1 block w-full" :value="old('tanggal_kegiatan', optional($album->tanggal_kegiatan ?? null)?->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('tanggal_kegiatan')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="lokasi" value="Lokasi" />
            <x-text-input id="lokasi" name="lokasi" type="text" class="mt-1 block w-full" :value="old('lokasi', $album->lokasi ?? '')" />
            <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="tags" value="Tags (pisahkan dengan koma)" />
            <x-text-input id="tags" name="tags" type="text" class="mt-1 block w-full" :value="old('tags', $album->tags ?? '')" />
            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <x-input-label for="deskripsi" value="Deskripsi" />
            <textarea id="deskripsi" name="deskripsi" rows="5" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('deskripsi', $album->deskripsi ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
        </div>
        <div>
            <x-input-label value="Publish?" />
            <label class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $album->is_published ?? false))>
                <span>Published</span>
            </label>
        </div>
        @if($editing && $album->cover_path)
            <div>
                <x-input-label value="Cover Saat Ini" />
                <x-media-img :src="$album->cover_path" class="h-24 mt-2 rounded object-cover" alt="Cover {{ $album->judul }}" />
                <button
                    type="submit"
                    form="remove-album-cover-form-{{ $album->id }}"
                    class="mt-2 px-3 py-1 bg-red-200 rounded"
                    onclick="return confirm('Hapus cover?')"
                >
                    Hapus Cover
                </button>
            </div>
        @endif
    </div>
    <div class="mt-6 flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('admin.dokumentasi.albums.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
    </div>
</form>

@if($editing && $album->cover_path)
    <form id="remove-album-cover-form-{{ $album->id }}" method="POST" action="{{ route('admin.dokumentasi.albums.remove-cover', $album) }}" class="hidden">
        @csrf
    </form>
@endif
