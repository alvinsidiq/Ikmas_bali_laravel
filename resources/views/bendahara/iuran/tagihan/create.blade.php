<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Buat Tagihan</h2></x-slot>
  <div class="p-6 bg-white rounded shadow">
    <form method="POST" action="{{ route('bendahara.tagihan.store') }}" class="grid md:grid-cols-2 gap-4">
      @csrf
      <div>
        <x-input-label value="Anggota" />
        <select name="user_id" class="mt-1 w-full border-gray-300 rounded" required>
          @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <x-input-label value="Judul" />
        <x-text-input name="judul" class="mt-1 w-full" :value="old('judul','')" required />
      </div>
      <div>
        <x-input-label value="Periode (YYYY-MM)" />
        <x-text-input name="periode" class="mt-1 w-full" :value="old('periode','')" />
      </div>
      <div>
        <x-input-label value="Jatuh Tempo" />
        <x-text-input type="date" name="jatuh_tempo" class="mt-1 w-full" :value="old('jatuh_tempo','')" />
      </div>
      <div>
        <x-input-label value="Nominal (Rp)" />
        <x-text-input type="number" name="nominal" class="mt-1 w-full" :value="old('nominal',0)" required />
      </div>
      <div>
        <x-input-label value="Denda (Rp)" />
        <x-text-input type="number" name="denda" class="mt-1 w-full" :value="old('denda',0)" />
      </div>
      <div>
        <x-input-label value="Diskon (Rp)" />
        <x-text-input type="number" name="diskon" class="mt-1 w-full" :value="old('diskon',0)" />
      </div>
      <div class="md:col-span-2">
        <x-input-label value="Catatan" />
        <textarea name="catatan" rows="4" class="mt-1 w-full border-gray-300 rounded">{{ old('catatan','') }}</textarea>
      </div>
      <div class="md:col-span-2 flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('bendahara.tagihan.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
      </div>
    </form>
  </div>
</x-app-layout>

