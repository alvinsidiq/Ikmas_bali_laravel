<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Generate Tagihan Massal</h2></x-slot>
  <div class="p-6 bg-white rounded shadow space-y-4">
    @if(session('success'))
      <div class="p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="p-3 bg-red-100 border border-red-300 rounded text-red-700">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-1">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @php($target = old('target','all'))
    @php($selectedIds = old('user_ids', []))

    <form method="POST" action="{{ route('bendahara.bulk.generate') }}" class="grid md:grid-cols-2 gap-4">
      @csrf
      <div>
        <x-input-label value="Periode (YYYY-MM)" />
        <div class="relative mt-1">
          <span class="absolute left-3 top-2.5 text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h6m-9 0h.01M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
          </span>
          <input
            type="month"
            name="periode"
            value="{{ old('periode') }}"
            required
            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-9"
            placeholder="2025-11"
          />
        </div>
        <x-input-error :messages="$errors->get('periode')" class="mt-2" />
      </div>
      <div>
        <x-input-label value="Judul" />
        <x-text-input name="judul" class="mt-1 w-full" required placeholder="Iuran Bulanan — Nov 2025" :value="old('judul')" />
        <x-input-error :messages="$errors->get('judul')" class="mt-2" />
      </div>
      <div>
        <x-input-label value="Nominal (Rp)" />
        <x-text-input name="nominal" type="number" class="mt-1 w-full" required :value="old('nominal')" />
        <x-input-error :messages="$errors->get('nominal')" class="mt-2" />
      </div>
      <div>
        <x-input-label value="Jatuh Tempo" />
        <div class="relative mt-1">
          <span class="absolute left-3 top-2.5 text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h6m-9 0h.01M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
          </span>
          <input
            type="date"
            name="jatuh_tempo"
            value="{{ old('jatuh_tempo') }}"
            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-9"
          />
        </div>
        <x-input-error :messages="$errors->get('jatuh_tempo')" class="mt-2" />
      </div>
      <div>
        <x-input-label value="Denda (Rp)" />
        <x-text-input name="denda" type="number" class="mt-1 w-full" :value="old('denda', 0)" />
        <x-input-error :messages="$errors->get('denda')" class="mt-2" />
      </div>
      <div>
        <x-input-label value="Diskon (Rp)" />
        <x-text-input name="diskon" type="number" class="mt-1 w-full" :value="old('diskon', 0)" />
        <x-input-error :messages="$errors->get('diskon')" class="mt-2" />
      </div>
      <div class="md:col-span-2">
        <x-input-label value="Target" />
        <div class="flex items-center gap-4 mt-1">
          <label class="inline-flex items-center gap-2"><input type="radio" name="target" value="all" @checked($target==='all')> Semua Anggota</label>
          <label class="inline-flex items-center gap-2"><input type="radio" name="target" value="selected" @checked($target==='selected')> Pilih Anggota</label>
        </div>
      </div>
      <div class="md:col-span-2">
        <x-input-label value="Pilih Anggota (opsional)" />
        <div class="grid md:grid-cols-3 gap-2 max-h-60 overflow-auto border rounded p-2">
          @foreach($anggota as $u)
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="user_ids[]" value="{{ $u->id }}" @checked(in_array($u->id, $selectedIds))> <span>{{ $u->name }}</span></label>
          @endforeach
        </div>
        <x-input-error :messages="$errors->get('user_ids')" class="mt-2" />
      </div>
      <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2"><input type="checkbox" name="skip_if_exists" value="1" @checked(old('skip_if_exists', true))> Lewati jika tagihan periode tersebut sudah ada</label>
      </div>
      <div class="md:col-span-2 flex gap-2">
        <x-primary-button>Generate</x-primary-button>
        <a href="{{ route('bendahara.tagihan.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
      </div>
    </form>
  </div>
</x-app-layout>
