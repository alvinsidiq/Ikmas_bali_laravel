<x-anggota-layout title="Edit Topik">
  <div class="bg-white shadow rounded p-6">
    <form method="POST" action="{{ route('anggota.forum.update',$topic) }}">
      @csrf @method('PUT')
      <div class="grid gap-4">
        <div>
          <x-input-label for="judul" value="Judul" />
          <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul',$topic->judul)" required />
          <x-input-error :messages="$errors->get('judul')" class="mt-2" />
        </div>
        <div>
          <x-input-label for="kategori" value="Kategori" />
          <x-text-input id="kategori" name="kategori" type="text" class="mt-1 block w-full" :value="old('kategori',$topic->kategori)" />
        </div>
        <div>
          <x-input-label for="banner_url" value="Gambar Banner (URL)" />
          <x-text-input id="banner_url" name="banner_url" type="url" class="mt-1 block w-full" placeholder="https://..." :value="old('banner_url',$topic->banner_url)" />
          <x-input-error :messages="$errors->get('banner_url')" class="mt-2" />
          @if($topic->banner_url)
            <x-media-img :src="$topic->banner_url" class="h-24 mt-2 rounded object-cover" alt="Banner {{ $topic->judul }}" />
          @endif
        </div>
        <div>
          <x-input-label for="body" value="Konten Pembuka" />
          <textarea id="body" name="body" rows="8" class="mt-1 block w-full border-gray-300 rounded">{{ old('body',$topic->body) }}</textarea>
        </div>
        <div class="flex gap-2">
          <x-primary-button>Update</x-primary-button>
          <a href="{{ route('anggota.forum.show',$topic) }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
        </div>
      </div>
    </form>
  </div>
</x-anggota-layout>
