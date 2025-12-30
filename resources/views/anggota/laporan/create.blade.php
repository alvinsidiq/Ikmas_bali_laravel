<x-anggota-layout title="Buat Laporan" subtitle="Isi detail laporan Anda">
  <div class="bg-white shadow rounded p-6">
    <form method="POST" action="{{ route('anggota.laporan.store') }}" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <div>
        <x-input-label for="judul" value="Judul" />
        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" required />
        <x-input-error :messages="$errors->get('judul')" class="mt-2" />
      </div>
      <div>
        <x-input-label for="kategori" value="Jenis Laporan" />
        <select id="kategori" name="kategori" class="mt-1 block w-full border-gray-300 rounded">
          <option value="">— Pilih —</option>
          <option value="Laporan Kegiatan">Laporan Kegiatan</option>
          <option value="Laporan Pengumuman">Laporan Pengumuman</option>
        </select>
        <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
      </div>
      <div>
        <x-input-label for="deskripsi" value="Deskripsi" />
        <textarea id="deskripsi" name="deskripsi" rows="8" class="mt-1 block w-full border-gray-300 rounded"></textarea>
        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
      </div>
      <div>
        <x-input-label for="files" value="Lampiran (opsional)" />
        <input id="files" type="file" name="files[]" multiple class="block w-full" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png,.webp,.zip" />
        <p class="text-xs text-gray-500 mt-1">Maks 10 berkas, 8MB per berkas.</p>
        <x-input-error :messages="$errors->get('files')" class="mt-2" />
      </div>
      <div class="pt-2"><x-primary-button>Kirim Laporan</x-primary-button></div>
    </form>
  </div>
</x-anggota-layout>
