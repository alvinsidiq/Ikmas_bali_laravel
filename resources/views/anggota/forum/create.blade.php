<x-anggota-layout title="Buat Topik" subtitle="Tulis topik baru">
  <div class="bg-white shadow rounded p-6">
    <form method="POST" action="{{ route('anggota.forum.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="grid gap-4">
        <div>
          <x-input-label for="judul" value="Judul" />
          <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" required />
          <x-input-error :messages="$errors->get('judul')" class="mt-2" />
        </div>
        <div>
          <x-input-label for="kategori" value="Kategori" />
          <x-text-input id="kategori" name="kategori" type="text" class="mt-1 block w-full" />
        </div>
        <div>
          <x-input-label for="banner" value="Gambar Banner" />
          <input id="banner" name="banner" type="file" accept="image/*" class="mt-1 block w-full" />
          <p class="text-xs text-gray-500 mt-1">Format gambar umum, maks 3MB.</p>
          <x-input-error :messages="$errors->get('banner')" class="mt-2" />
        </div>
        <div>
          <x-input-label for="body" value="Konten Pembuka" />
          <textarea id="body" name="body" rows="8" class="mt-1 block w-full border-gray-300 rounded"></textarea>
        </div>
        <div class="flex gap-2">
          <x-primary-button>Simpan</x-primary-button>
          <a href="{{ route('anggota.forum.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
        </div>
      </div>
    </form>
  </div>
</x-anggota-layout>
