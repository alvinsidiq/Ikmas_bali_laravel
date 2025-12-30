@php($editing = isset($semester))
<form method="POST" action="{{ $editing ? route('admin.semesters.update', $semester) : route('admin.semesters.store') }}">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="nama" value="Nama Semester" />
            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $semester->nama ?? '')" required />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="tahun_ajaran" value="Tahun Ajaran" />
            <x-text-input id="tahun_ajaran" name="tahun_ajaran" type="text" class="mt-1 block w-full" placeholder="2024/2025" :value="old('tahun_ajaran', $semester->tahun_ajaran ?? '')" />
            <x-input-error :messages="$errors->get('tahun_ajaran')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="mulai" value="Mulai" />
            <x-text-input id="mulai" name="mulai" type="date" class="mt-1 block w-full" :value="old('mulai', optional($semester->mulai ?? null)->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('mulai')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="selesai" value="Selesai" />
            <x-text-input id="selesai" name="selesai" type="date" class="mt-1 block w-full" :value="old('selesai', optional($semester->selesai ?? null)->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('selesai')" class="mt-2" />
        </div>
        <div>
            <x-input-label value="Aktif?" />
            <label class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $semester->is_active ?? false))>
                <span>Jadikan semester aktif (semester lain otomatis nonaktif)</span>
            </label>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('admin.semesters.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded">Batal</a>
    </div>
</form>
