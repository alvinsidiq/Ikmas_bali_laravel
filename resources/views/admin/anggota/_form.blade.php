@php($editing = isset($anggota))
<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.anggota.update', $anggota) : route('admin.anggota.store') }}">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="name" value="Nama (akun)" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $anggota->name ?? '')" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $anggota->email ?? '')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password" value="Password {{ $editing ? '(kosongkan jika tidak ganti)' : '' }}" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="nik" value="NIK" />
            <x-text-input id="nik" name="nik" type="text" class="mt-1 block w-full" :value="old('nik', $anggota->profile->nik ?? '')" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <x-input-label for="nama_lengkap" value="Nama Lengkap" />
            <x-text-input id="nama_lengkap" name="nama_lengkap" type="text" class="mt-1 block w-full" :value="old('nama_lengkap', $anggota->profile->nama_lengkap ?? '')" />
            <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="phone" value="No. HP" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $anggota->profile->phone ?? '')" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
            <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full" :value="old('tanggal_lahir', optional($anggota->profile->tanggal_lahir ?? null)?->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
        </div>
        <div>
            <x-input-label value="Jenis Kelamin" />
            <select name="jenis_kelamin" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">-</option>
                <option value="L" @selected(old('jenis_kelamin', $anggota->profile->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
                <option value="P" @selected(old('jenis_kelamin', $anggota->profile->jenis_kelamin ?? '') === 'P')>Perempuan</option>
            </select>
            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="pekerjaan" value="Pekerjaan" />
            <x-text-input id="pekerjaan" name="pekerjaan" type="text" class="mt-1 block w-full" :value="old('pekerjaan', $anggota->profile->pekerjaan ?? '')" />
            <x-input-error :messages="$errors->get('pekerjaan')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="organisasi" value="Organisasi" />
            <x-text-input id="organisasi" name="organisasi" type="text" class="mt-1 block w-full" :value="old('organisasi', $anggota->profile->organisasi ?? '')" />
            <x-input-error :messages="$errors->get('organisasi')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
            <x-input-label for="alamat" value="Alamat" />
            <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('alamat', $anggota->profile->alamat ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="avatar" value="Avatar" />
            <input id="avatar" name="avatar" type="file" class="mt-1 block w-full" accept="image/*" />
            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
            @if($editing && ($anggota->profile->avatar_path ?? false))
                <img src="{{ asset('storage/'.($anggota->profile->avatar_path)) }}" class="h-20 mt-2 rounded" />
            @endif
        </div>
        <div>
            <x-input-label value="Status" />
            <label class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $anggota->profile->is_active ?? true))>
                <span>Aktif</span>
            </label>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('admin.anggota.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded">Batal</a>
    </div>
</form>
