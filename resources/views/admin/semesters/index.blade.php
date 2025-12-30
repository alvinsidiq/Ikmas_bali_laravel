<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Periode Pengajaran (Semester)</h2>
    </x-slot>
    <div class="p-6 space-y-4">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase text-gray-500">Kelola Periode Pengajaran</p>
                <p class="text-sm text-gray-700">Tambah, ubah, atau hapus data semester di sini.</p>
            </div>
            <a href="{{ route('admin.semesters.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-500">+ Tambah Semester</a>
        </div>

        <div class="flex flex-wrap items-end gap-3">
            <form method="GET" class="flex gap-2">
                <x-text-input name="q" placeholder="Cari nama/tahun ajaran" :value="$q" />
                <x-primary-button>Filter</x-primary-button>
            </form>
            <a href="{{ route('admin.semesters.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Tambah Semester</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-4 py-3">Nama Semester</th>
                        <th class="px-4 py-3">Tahun Ajaran</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $s)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $s->nama }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $s->tahun_ajaran ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ optional($s->mulai)->format('d M Y') ?? '-' }} — {{ optional($s->selesai)->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $s->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $s->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.semesters.edit', $s) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.semesters.destroy', $s) }}" onsubmit="return confirm('Hapus semester ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada data semester.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</x-app-layout>
