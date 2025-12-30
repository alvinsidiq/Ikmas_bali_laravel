@php($editing = isset($kegiatan))
<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.kegiatan.update', $kegiatan) : route('admin.kegiatan.store') }}">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="judul" value="Judul" />
            <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul', $kegiatan->judul ?? '')" required />
            <x-input-error :messages="$errors->get('judul')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="lokasi" value="Lokasi" />
            <x-text-input id="lokasi" name="lokasi" type="text" class="mt-1 block w-full" :value="old('lokasi', $kegiatan->lokasi ?? '')" />
            <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="waktu_mulai" value="Waktu Mulai" />
            <input id="waktu_mulai" name="waktu_mulai" type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('waktu_mulai', optional($kegiatan->waktu_mulai ?? null)?->format('Y-m-d\TH:i')) }}" required />
            <x-input-error :messages="$errors->get('waktu_mulai')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="waktu_selesai" value="Waktu Selesai" />
            <input id="waktu_selesai" name="waktu_selesai" type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('waktu_selesai', optional($kegiatan->waktu_selesai ?? null)?->format('Y-m-d\TH:i')) }}" />
            <x-input-error :messages="$errors->get('waktu_selesai')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <x-input-label for="deskripsi" value="Deskripsi" />
            <textarea id="deskripsi" name="deskripsi" rows="6" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('deskripsi', $kegiatan->deskripsi ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="poster" value="Poster" />
            <input id="poster" name="poster" type="file" class="mt-1 block w-full" accept="image/*" />
            <x-input-error :messages="$errors->get('poster')" class="mt-2" />
            @if($editing && $kegiatan->poster_path)
                <x-media-img :src="$kegiatan->poster_path" class="h-24 mt-2 rounded object-cover" alt="Poster {{ $kegiatan->judul }}" />
                <button
                    type="submit"
                    form="remove-poster-form-{{ $kegiatan->id }}"
                    class="mt-2 px-3 py-1 bg-red-200 rounded"
                    onclick="return confirm('Hapus poster?')"
                >
                    Hapus Poster
                </button>
            @endif
        </div>
        <div>
            <x-input-label value="Publish sekarang?" />
            <label class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $kegiatan->is_published ?? false))>
                <span>Published</span>
            </label>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('admin.kegiatan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded">Batal</a>
    </div>
</form>

@if($editing && $kegiatan->poster_path)
    <form id="remove-poster-form-{{ $kegiatan->id }}" method="POST" action="{{ route('admin.kegiatan.remove-poster', $kegiatan) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif
