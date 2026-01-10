<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Tambah Laporan</h2>
    </x-slot>

    <div class="p-6">
        <div class="max-w-3xl mx-auto bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <form method="POST" action="{{ route('admin.laporan.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="judul" value="Judul" />
                    <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul')" required />
                    <x-input-error :messages="$errors->get('judul')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="kategori" value="Jenis Laporan" />
                    <select id="kategori" name="kategori" class="mt-1 block w-full border-gray-300 rounded">
                        <option value="">— Pilih —</option>
                        @foreach(['Pengaduan','Saran','Fasilitas','Keuangan','Kegiatan','Lainnya'] as $label)
                            <option value="{{ $label }}" @selected(old('kategori')===$label)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="deskripsi" value="Deskripsi" />
                    <textarea id="deskripsi" name="deskripsi" rows="8" class="mt-1 block w-full border-gray-300 rounded">{{ old('deskripsi') }}</textarea>
                    <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="files" value="Lampiran (opsional)" />
                    <input id="files" type="file" name="files[]" multiple class="block w-full" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png,.webp,.zip" />
                    <p class="text-xs text-gray-500 mt-1">Maks 10 berkas, 8MB per berkas.</p>
                    <x-input-error :messages="$errors->get('files')" class="mt-2" />
                </div>
                <div class="pt-2 flex gap-3">
                    <x-primary-button>Simpan</x-primary-button>
                    <a href="{{ route('admin.laporan.index') }}" class="px-4 py-2 bg-gray-100 border border-slate-200 rounded hover:bg-white">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
