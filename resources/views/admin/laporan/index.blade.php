<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Laporan Pengumuman & Kegiatan</h2>
    </x-slot>

    <div class="p-6 space-y-4">
        <div class="bg-white shadow rounded-xl p-4">
            <div class="space-y-3">
                <form method="GET" class="grid gap-3 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-6 items-end">
                    <div class="md:col-span-2 xl:col-span-2">
                        <x-input-label value="Pencarian" />
                        <div class="relative">
                            <span class="absolute left-2 top-2.5 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg></span>
                            <input name="q" value="{{ $q }}" placeholder="Judul / kategori / lokasi" class="pl-8 w-full border-gray-300 rounded-md" />
                        </div>
                    </div>
                    <div class="md:col-span-1">
                        <x-input-label value="Dari Tanggal" />
                        <input type="date" name="from" value="{{ $from }}" class="w-full border-gray-300 rounded-md" />
                    </div>
                    <div class="md:col-span-1">
                        <x-input-label value="Sampai Tanggal" />
                        <input type="date" name="to" value="{{ $to }}" class="w-full border-gray-300 rounded-md" />
                    </div>
                    <div class="md:col-span-1">
                        <x-input-label value="Jenis Data" />
                        <select name="jenis" class="w-full border-gray-300 rounded-md">
                            <option value="">Semua Jenis</option>
                            <option value="pengumuman" @selected($jenis==='pengumuman')>Pengumuman</option>
                            <option value="kegiatan" @selected($jenis==='kegiatan')>Kegiatan</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <x-input-label value="Status" />
                        <select name="status" class="w-full border-gray-300 rounded-md">
                            <option value="">Semua Status</option>
                            <option value="published" @selected($status==='published')>Published</option>
                            <option value="draft" @selected($status==='draft')>Draft</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 xl:col-span-1">
                        <x-primary-button class="w-full">Filter</x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php($typeCls = ['pengumuman'=>'bg-sky-100 text-sky-700','kegiatan'=>'bg-emerald-100 text-emerald-700'])
                    @forelse($items as $r)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $typeCls[$r->type] ?? 'bg-gray-200 text-gray-700' }}">{{ $r->type_label }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ $r->detail_url }}" class="font-semibold text-gray-900 hover:underline">{{ $r->title }}</a>
                                <div class="text-xs text-gray-500 mt-1">{{ $r->meta_label }}: {{ $r->meta_value }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                <div>{{ $r->date_label }}</div>
                                @if($r->date_meta)
                                    <div class="text-xs text-gray-500">{{ $r->date_meta }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $r->status_class }}">{{ $r->status_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ $r->detail_url }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada data laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $items->links() }}</div>
    </div>
</x-app-layout>
