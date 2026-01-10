<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Kegiatan</h2>
    </x-slot>
    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-end gap-3 mb-4">
            <form method="GET" class="flex gap-2">
                <x-text-input name="q" placeholder="Cari judul/lokasi/teks" :value="$q" />
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">Semua Status</option>
                    <option value="published" @selected($status === 'published')>Published</option>
                    <option value="unpublished" @selected($status === 'unpublished')>Unpublished</option>
                </select>
                <select name="w" class="border-gray-300 rounded-md">
                    <option value="">Semua Waktu</option>
                    <option value="upcoming" @selected($w === 'upcoming')>Akan Datang</option>
                    <option value="past" @selected($w === 'past')>Selesai</option>
                </select>
                <x-primary-button>Filter</x-primary-button>
            </form>
            <a href="{{ route('admin.kegiatan.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">+ Tambah</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-4 py-3">Poster</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Publikasi</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $k)
                        <tr>
                            <td class="px-4 py-3 align-top">
                                @if($k->poster_path)
                                    @php($posterUrl = \App\Support\MediaPath::url($k->poster_path))
                                    <x-media-img :src="$k->poster_path" class="w-16 h-16 object-cover rounded" alt="Poster {{ $k->judul }}" />
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">No Poster</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <a href="{{ route('admin.kegiatan.show', $k) }}" class="font-semibold text-gray-900 hover:underline">
                                    {{ $k->judul }}
                                </a>
                                <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($k->deskripsi), 120) }}</div>
                            </td>
                            <td class="px-4 py-3 align-top text-gray-700">{{ $k->lokasi ?? '-' }}</td>
                            <td class="px-4 py-3 align-top text-gray-700">
                                {{ optional($k->waktu_mulai)->format('d M Y H:i') ?? '-' }}<br>
                                @if($k->waktu_selesai)
                                    <span class="text-xs text-gray-500">sampai {{ optional($k->waktu_selesai)->format('d M Y H:i') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $k->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $k->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button
                                        type="button"
                                        data-poster-trigger
                                        data-src="{{ $posterUrl ?? '' }}"
                                        data-title="{{ $k->judul }}"
                                        data-lokasi="{{ $k->lokasi ?? '-' }}"
                                        data-mulai="{{ optional($k->waktu_mulai)->format('d M Y H:i') ?? '-' }}"
                                        data-selesai="{{ optional($k->waktu_selesai)->format('d M Y H:i') ?? '-' }}"
                                        data-status="{{ $k->is_published ? 'Published' : 'Draft' }}"
                                        data-deskripsi="{{ e(strip_tags($k->deskripsi)) }}"
                                        class="px-3 py-1 border border-blue-300 text-blue-700 rounded hover:bg-blue-50 {{ $posterUrl ? '' : 'opacity-50 cursor-not-allowed' }}"
                                        @disabled(!$posterUrl)
                                    >View</button>
                                    <a href="{{ route('admin.kegiatan.edit', $k) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.kegiatan.toggle-publish', $k) }}">
                                        @csrf
                                        <button class="px-3 py-1 border rounded {{ $k->is_published ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                            {{ $k->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.kegiatan.destroy', $k) }}" onsubmit="return confirm('Hapus kegiatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>

    <div id="poster-modal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <div class="font-semibold text-gray-800" id="poster-modal-title">Poster</div>
                <button data-close class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            <div class="p-4 grid md:grid-cols-2 gap-4">
                <div>
                    <img id="poster-modal-img" src="" alt="Poster" class="max-h-[70vh] w-full object-contain rounded hidden">
                    <div id="poster-modal-empty" class="text-center text-gray-500 py-10">Poster tidak tersedia.</div>
                </div>
                <div class="space-y-2 text-sm text-gray-700">
                    <div class="space-y-1">
                        <p class="text-xs uppercase text-gray-500">Judul</p>
                        <div class="font-semibold text-gray-900" id="modal-judul">-</div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs uppercase text-gray-500">Lokasi</p>
                            <div class="font-medium text-gray-900" id="modal-lokasi">-</div>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500">Waktu Mulai</p>
                            <div class="font-medium text-gray-900" id="modal-mulai">-</div>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500">Waktu Selesai</p>
                            <div class="font-medium text-gray-900" id="modal-selesai">-</div>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500">Status Publish</p>
                            <div class="font-medium text-gray-900" id="modal-status">-</div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-500">Deskripsi</p>
                        <div class="rounded border border-slate-200 bg-slate-50 p-2 text-sm leading-relaxed" id="modal-deskripsi">-</div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 border-t text-right">
                <button data-close class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('poster-modal');
        const img = document.getElementById('poster-modal-img');
        const titleEl = document.getElementById('poster-modal-title');
        const emptyEl = document.getElementById('poster-modal-empty');
        const judulEl = document.getElementById('modal-judul');
        const lokasiEl = document.getElementById('modal-lokasi');
        const mulaiEl = document.getElementById('modal-mulai');
        const selesaiEl = document.getElementById('modal-selesai');
        const statusEl = document.getElementById('modal-status');
        const deskripsiEl = document.getElementById('modal-deskripsi');

        document.querySelectorAll('[data-poster-trigger]').forEach(btn => {
          btn.addEventListener('click', () => {
            const src = btn.dataset.src;
            const title = btn.dataset.title || 'Poster';
            const lokasi = btn.dataset.lokasi || '-';
            const mulai = btn.dataset.mulai || '-';
            const selesai = btn.dataset.selesai || '-';
            const status = btn.dataset.status || '-';
            const desc = btn.dataset.deskripsi || '-';

            titleEl.textContent = title;
            judulEl.textContent = title;
            lokasiEl.textContent = lokasi;
            mulaiEl.textContent = mulai;
            selesaiEl.textContent = selesai;
            statusEl.textContent = status;
            deskripsiEl.textContent = desc;

            if (src) {
              img.src = src;
              img.classList.remove('hidden');
              emptyEl.classList.add('hidden');
            } else {
              img.src = '';
              img.classList.add('hidden');
              emptyEl.classList.remove('hidden');
            }
            modal.classList.remove('hidden');
          });
        });

        modal.querySelectorAll('[data-close]').forEach(btn => {
          btn.addEventListener('click', () => modal.classList.add('hidden'));
        });

        modal.addEventListener('click', (e) => {
          if (e.target === modal) { modal.classList.add('hidden'); }
        });
      });
    </script>
</x-app-layout>
