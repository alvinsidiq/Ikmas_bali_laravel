<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Buat Topik</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        <form method="POST" action="{{ route('admin.forum.store') }}">
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
                    <x-input-label for="banner_url" value="Gambar Banner (URL)" />
                    <x-text-input id="banner_url" name="banner_url" type="url" placeholder="https://..." class="mt-1 block w-full" :value="old('banner_url')" />
                    <x-input-error :messages="$errors->get('banner_url')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="body" value="Konten Pembuka" />
                    <textarea id="body" name="body" rows="8" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
                </div>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_pinned" value="1"> <span>Pin topik</span></label>
                <div class="flex gap-2">
                    <x-primary-button>Simpan</x-primary-button>
                    <a href="{{ route('admin.forum.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
