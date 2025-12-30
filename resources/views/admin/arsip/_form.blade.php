@php($editing = isset($arsip))
@php($requireFile = !$editing || empty($arsip->file_path))
<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.arsip.update', $arsip) : route('admin.arsip.store') }}">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="judul" value="Judul" />
            <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul', $arsip->judul ?? '')" required />
            <x-input-error :messages="$errors->get('judul')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="kategori" value="Kategori" />
            <x-text-input id="kategori" name="kategori" type="text" class="mt-1 block w-full" :value="old('kategori', $arsip->kategori ?? '')" />
            <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="tahun" value="Tahun" />
            <x-text-input id="tahun" name="tahun" type="number" min="1900" max="2100" class="mt-1 block w-full" :value="old('tahun', $arsip->tahun ?? '')" />
            <x-input-error :messages="$errors->get('tahun')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="nomor_dokumen" value="Nomor Dokumen" />
            <x-text-input id="nomor_dokumen" name="nomor_dokumen" type="text" class="mt-1 block w-full" :value="old('nomor_dokumen', $arsip->nomor_dokumen ?? '')" />
            <x-input-error :messages="$errors->get('nomor_dokumen')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <x-input-label for="tags" value="Tags (pisahkan dengan koma)" />
            <x-text-input id="tags" name="tags" type="text" class="mt-1 block w-full" :value="old('tags', $arsip->tags ?? '')" />
            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <x-input-label for="ringkasan" value="Ringkasan" />
            <textarea id="ringkasan" name="ringkasan" rows="5" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('ringkasan', $arsip->ringkasan ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('ringkasan')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <x-input-label for="file" value="File" />
            <input
                id="file"
                name="file"
                type="file"
                class="mt-1 block w-full"
                accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.zip"
                @if($requireFile) required @endif
            />
            <p class="text-xs text-gray-500 mt-1">Unggah berkas dokumen (PDF/Office/ZIP), wajib diisi untuk simpan arsip.</p>
            <x-input-error :messages="$errors->get('file')" class="mt-2" />
            @if($editing && $arsip->file_path)
                <div class="mt-2 text-sm text-gray-700">File saat ini: <strong>{{ $arsip->file_name }}</strong> ({{ number_format($arsip->file_size/1024,1) }} KB)</div>
                <div class="mt-2 flex gap-2">
                    <a href="{{ route('admin.arsip.download', $arsip) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Download</a>
                    <button
                        type="submit"
                        form="remove-arsip-file-form-{{ $arsip->id }}"
                        class="px-3 py-1 bg-red-200 rounded"
                        onclick="return confirm('Hapus file?')"
                    >
                        Hapus File
                    </button>
                </div>
            @endif
        </div>
        <div>
            <x-input-label value="Publish?" />
            <label class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $arsip->is_published ?? false))>
                <span>Published</span>
            </label>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('admin.arsip.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded">Batal</a>
    </div>
</form>

@if($editing && $arsip->file_path)
    <form id="remove-arsip-file-form-{{ $arsip->id }}" method="POST" action="{{ route('admin.arsip.remove-file', $arsip) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif
