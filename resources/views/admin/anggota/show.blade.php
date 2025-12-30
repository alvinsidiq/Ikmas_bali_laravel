<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detail Anggota</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        <div class="flex gap-6">
            <div>
                @if($anggota->profile && $anggota->profile->avatar_path)
                    <img src="{{ asset('storage/'.$anggota->profile->avatar_path) }}" class="h-32 w-32 rounded object-cover" />
                @else
                    <div class="h-32 w-32 bg-gray-200 rounded"></div>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><strong>Nama:</strong> {{ $anggota->name }}</div>
                <div><strong>Email:</strong> {{ $anggota->email }}</div>
                <div><strong>Nama Lengkap:</strong> {{ $anggota->profile->nama_lengkap ?? '-' }}</div>
                <div><strong>NIK:</strong> {{ $anggota->profile->nik ?? '-' }}</div>
                <div><strong>HP:</strong> {{ $anggota->profile->phone ?? '-' }}</div>
                <div><strong>JK:</strong> {{ $anggota->profile->jenis_kelamin ?? '-' }}</div>
                <div><strong>Pekerjaan:</strong> {{ $anggota->profile->pekerjaan ?? '-' }}</div>
                <div><strong>Organisasi:</strong> {{ $anggota->profile->organisasi ?? '-' }}</div>
                <div class="md:col-span-2"><strong>Alamat:</strong> {{ $anggota->profile->alamat ?? '-' }}</div>
                <div><strong>Status:</strong> {{ ($anggota->profile->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}</div>
                <div><strong>Joined:</strong> {{ optional($anggota->profile->joined_at)->format('d M Y H:i') }}</div>
            </div>
        </div>
        <div class="mt-6">
            <a href="{{ route('admin.anggota.edit', $anggota) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Edit</a>
            <a href="{{ route('admin.anggota.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
        </div>
    </div>
</x-app-layout>
