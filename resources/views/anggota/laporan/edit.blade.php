<x-anggota-layout title="Edit Laporan">
  <div class="bg-white shadow rounded p-6">
    <form method="POST" action="{{ route('anggota.laporan.update',$laporan) }}" class="space-y-4">
      @csrf @method('PUT')
      <div>
        <x-input-label for="judul" value="Judul" />
        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="$laporan->judul" required />
        <x-input-error :messages="$errors->get('judul')" class="mt-2" />
      </div>
      <div>
        <x-input-label for="kategori" value="Jenis Laporan" />
        <select id="kategori" name="kategori" class="mt-1 block w-full border-gray-300 rounded">
          <option value="">— Pilih —</option>
          @foreach(['Pengaduan','Saran','Fasilitas','Keuangan','Kegiatan','Lainnya'] as $label)
            <option value="{{ $label }}" @selected($laporan->kategori===$label)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <x-input-label for="deskripsi" value="Deskripsi" />
        <textarea id="deskripsi" name="deskripsi" rows="8" class="mt-1 block w-full border-gray-300 rounded">{{ $laporan->deskripsi }}</textarea>
      </div>
      <div class="flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('anggota.laporan.show',$laporan) }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
      </div>
    </form>
  </div>
</x-anggota-layout>
