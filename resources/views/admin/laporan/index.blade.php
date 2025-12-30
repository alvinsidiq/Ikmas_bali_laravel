<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Laporan Anggota</h2>
    </x-slot>

    <div class="p-6 space-y-4">
        <div class="bg-white shadow rounded-xl p-4">
            <div class="flex flex-wrap items-end gap-3">
                <form method="GET" class="grid sm:grid-cols-2 lg:grid-cols-7 gap-3 items-end flex-1">
                    <div class="lg:col-span-2">
                        <x-input-label value="Pencarian" />
                        <div class="relative">
                            <span class="absolute left-2 top-2.5 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg></span>
                            <input name="q" value="{{ $q }}" placeholder="Judul / kode / deskripsi" class="pl-8 w-full border-gray-300 rounded-md" />
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Dari Tanggal" />
                        <input type="date" name="from" value="{{ $from }}" class="w-full border-gray-300 rounded-md" />
                    </div>
                    <div>
                        <x-input-label value="Sampai Tanggal" />
                        <input type="date" name="to" value="{{ $to }}" class="w-full border-gray-300 rounded-md" />
                    </div>
                    <div>
                        <x-input-label value="Status" />
                        <select name="status" class="w-full border-gray-300 rounded-md">
                            <option value="">Semua Status</option>
                            @foreach(['open'=>'Open','in_progress'=>'In Progress','resolved'=>'Resolved','rejected'=>'Rejected'] as $sv=>$sl)
                                <option value="{{ $sv }}" @selected($st===$sv)>{{ $sl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Jenis Laporan" />
                        <select name="jenis" class="w-full border-gray-300 rounded-md">
                            <option value="">Semua Jenis</option>
                            <option value="kegiatan" @selected($jenis==='kegiatan')>Laporan Kegiatan</option>
                            <option value="pengumuman" @selected($jenis==='pengumuman')>Laporan Pengumuman</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Pelapor" />
                        <input name="reporter" value="{{ $reporter }}" placeholder="Nama / email" class="w-full border-gray-300 rounded-md" />
                    </div>
                    <div>
                        <x-primary-button class="w-full sm:w-auto">Filter</x-primary-button>
                    </div>
                </form>
                <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                    @php($query = request()->query())
                    <a href="{{ route('admin.laporan.index', array_merge($query, ['jenis'=>null])) }}"
                       class="px-3 py-2 rounded border text-sm {{ $jenis ? 'border-gray-200 text-gray-600 hover:bg-gray-50' : 'border-indigo-200 text-indigo-700 bg-indigo-50' }}">
                        Semua Laporan
                    </a>
                    <a href="{{ route('admin.laporan.index', array_merge($query, ['jenis'=>'kegiatan'])) }}"
                       class="px-3 py-2 rounded border text-sm {{ $jenis==='kegiatan' ? 'border-indigo-200 text-indigo-700 bg-indigo-50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        Laporan Kegiatan
                    </a>
                    <a href="{{ route('admin.laporan.index', array_merge($query, ['jenis'=>'pengumuman'])) }}"
                       class="px-3 py-2 rounded border text-sm {{ $jenis==='pengumuman' ? 'border-indigo-200 text-indigo-700 bg-indigo-50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        Laporan Pengumuman
                    </a>
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <a href="{{ route('admin.laporan.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-500 text-sm">+ Tambah Laporan</a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Pelapor</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Update</th>
                        <th class="px-4 py-3 text-right">Lampiran/Komentar</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $r)
                        @php($cls = ['open'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'][$r->status] ?? 'bg-gray-200 text-gray-700')
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $r->kode }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $r->judul }}</div>
                                <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($r->deskripsi), 120) }}</div>
                                <div class="text-[11px] text-gray-400 mt-1">Jenis: {{ $r->kategori ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ optional($r->reporter)->name ?? '—' }}
                                <div class="text-xs text-gray-500">{{ optional($r->reporter)->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">{{ ucfirst(str_replace('_',' ',$r->status)) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                <div>{{ $r->updated_at->format('d M Y H:i') }}</div>
                                <div class="text-xs text-gray-500">Dibuat {{ $r->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700">
                                <div>Lampiran: {{ $r->attachments_count }}</div>
                                <div>Komentar: {{ $r->comments_count }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.laporan.show',$r) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $items->links() }}</div>
    </div>
</x-app-layout>
