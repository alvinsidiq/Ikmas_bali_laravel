@php($editing = isset($pengumuman))
@php($categoryOptions = $categories ?? \App\Models\Pengumuman::CATEGORY_OPTIONS)
<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.pengumuman.update', $pengumuman) : route('admin.pengumuman.store') }}">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="judul" value="Judul" />
            <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul', $pengumuman->judul ?? '')" required />
            <x-input-error :messages="$errors->get('judul')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="kategori" value="Kategori" />
            <select id="kategori" name="kategori" class="mt-1 block w-full border-gray-300 rounded">
                @foreach($categoryOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('kategori', $pengumuman->kategori ?? array_key_first($categoryOptions)) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <x-input-label for="isi" value="Isi" />
            <textarea id="isi" name="isi" rows="8" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('isi', $pengumuman->isi ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('isi')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="cover" value="Cover" />
            <input id="cover" name="cover" type="file" class="mt-1 block w-full" accept="image/*" />
            <x-input-error :messages="$errors->get('cover')" class="mt-2" />
            @if($editing && $pengumuman->cover_path)
                <x-media-img :src="$pengumuman->cover_path" class="h-24 mt-2 rounded object-cover" alt="Cover {{ $pengumuman->judul }}" />
                <button
                    type="submit"
                    form="remove-cover-form-{{ $pengumuman->id }}"
                    class="mt-2 px-3 py-1 bg-red-200 rounded"
                    onclick="return confirm('Hapus cover?')"
                >
                    Hapus Cover
                </button>
            @endif
        </div>
        <div>
            <x-input-label value="Publish?" />
            <label class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $pengumuman->is_published ?? false))>
                <span>Published</span>
            </label>
        </div>
        <div>
            <x-input-label value="Pin di atas?" />
            <label class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned', $pengumuman->is_pinned ?? false))>
                <span>Pinned</span>
            </label>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('admin.pengumuman.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded">Batal</a>
    </div>
</form>

@if($editing && $pengumuman->cover_path)
    <form id="remove-cover-form-{{ $pengumuman->id }}" method="POST" action="{{ route('admin.pengumuman.remove-cover', $pengumuman) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif
