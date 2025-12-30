<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Edit Tagihan</h2></x-slot>
  <div class="p-6 bg-white rounded shadow">
    <form method="POST" action="{{ route('bendahara.tagihan.update',$tagihan) }}" class="grid md:grid-cols-2 gap-4">
      @csrf @method('PUT')
      <div>
        <x-input-label value="Anggota" />
        <select name="user_id" class="mt-1 w-full border-gray-300 rounded" disabled>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected($tagihan->user_id==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <x-input-label value="Judul" />
        <x-text-input name="judul" class="mt-1 w-full" :value="old('judul',$tagihan->judul)" required />
      </div>
      <div>
        <x-input-label value="Periode (YYYY-MM)" />
        <x-text-input name="periode" class="mt-1 w-full" :value="old('periode',$tagihan->periode)" />
      </div>
      <div>
        <x-input-label value="Jatuh Tempo" />
        <x-text-input type="date" name="jatuh_tempo" class="mt-1 w-full" :value="old('jatuh_tempo', optional($tagihan->jatuh_tempo)->format('Y-m-d'))" />
      </div>
      <div>
        <x-input-label value="Nominal (Rp)" />
        <x-text-input type="number" name="nominal" class="mt-1 w-full" :value="old('nominal',$tagihan->nominal)" required />
      </div>
      <div>
        <x-input-label value="Denda (Rp)" />
        <x-text-input type="number" name="denda" class="mt-1 w-full" :value="old('denda',$tagihan->denda)" />
      </div>
      <div>
        <x-input-label value="Diskon (Rp)" />
        <x-text-input type="number" name="diskon" class="mt-1 w-full" :value="old('diskon',$tagihan->diskon)" />
      </div>
      <div class="md:col-span-2">
        <x-input-label value="Catatan" />
        <textarea name="catatan" rows="4" class="mt-1 w-full border-gray-300 rounded">{{ old('catatan',$tagihan->catatan) }}</textarea>
      </div>
      <div class="md:col-span-2 flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('bendahara.tagihan.show',$tagihan) }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
      </div>
    </form>
  </div>
</x-app-layout>

