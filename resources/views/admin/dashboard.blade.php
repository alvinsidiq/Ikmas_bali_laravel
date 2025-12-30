<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Admin Dashboard</h2></x-slot>
  <div class="p-6 space-y-6">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <a href="{{ route('admin.anggota.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Anggota</p>
        <div class="mt-2 flex items-end gap-2">
          <div class="text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['anggota_total']) }}</div>
          <div class="text-sm text-emerald-700 bg-emerald-50 px-2 py-1 rounded">Aktif: {{ number_format($stats['anggota_aktif']) }}</div>
        </div>
        <div class="mt-2 text-xs text-indigo-700 underline">Kelola Anggota</div>
      </a>
      <a href="{{ route('admin.kegiatan.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Kegiatan</p>
        <div class="mt-2 flex items-end gap-2">
          <div class="text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['kegiatan_published']) }}</div>
          <div class="text-sm text-indigo-700 bg-indigo-50 px-2 py-1 rounded">Upcoming: {{ number_format($stats['kegiatan_upcoming']) }}</div>
        </div>
        <div class="mt-2 text-xs text-indigo-700 underline">Kelola Kegiatan</div>
      </a>
      <a href="{{ route('admin.pengumuman.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Pengumuman</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['pengumuman_published']) }}</div>
        <p class="text-xs text-gray-500 mt-1">Published</p>
        <div class="mt-2 text-xs text-indigo-700 underline">Kelola Pengumuman</div>
      </a>
      <a href="{{ route('admin.arsip.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Arsip</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['arsip_published']) }}</div>
        <p class="text-xs text-gray-500 mt-1">Published</p>
        <div class="mt-2 text-xs text-indigo-700 underline">Kelola Arsip</div>
      </a>
      <a href="{{ route('admin.dokumentasi.albums.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Album Dokumentasi</p>
        <div class="mt-2 flex items-end gap-2">
          <div class="text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['album_published']) }}</div>
          <div class="text-sm text-blue-700 bg-blue-50 px-2 py-1 rounded">Media: {{ number_format($stats['media_total']) }}</div>
        </div>
        <div class="mt-2 text-xs text-indigo-700 underline">Kelola Dokumentasi</div>
      </a>
    </div>

    <div class="bg-white border border-dashed border-slate-200 rounded-xl p-4 text-sm text-gray-700">
      <p class="font-semibold text-gray-900 mb-2">Akses cepat data:</p>
      <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.anggota.index') }}" class="text-indigo-700 hover:underline">Anggota</a>
        <span class="text-gray-400">•</span>
        <a href="{{ route('admin.kegiatan.index') }}" class="text-indigo-700 hover:underline">Kegiatan</a>
        <span class="text-gray-400">•</span>
        <a href="{{ route('admin.pengumuman.index') }}" class="text-indigo-700 hover:underline">Pengumuman</a>
        <span class="text-gray-400">•</span>
        <a href="{{ route('admin.arsip.index') }}" class="text-indigo-700 hover:underline">Arsip</a>
        <span class="text-gray-400">•</span>
        <a href="{{ route('admin.dokumentasi.albums.index') }}" class="text-indigo-700 hover:underline">Dokumentasi</a>
        @if(Route::has('admin.laporan.index'))
          <span class="text-gray-400">•</span>
          <a href="{{ route('admin.laporan.index') }}" class="text-indigo-700 hover:underline">Laporan</a>
        @endif
      </div>
    </div>

    <div class="rounded-xl border border-dashed border-slate-200 bg-white p-6 text-gray-600">
      Selamat datang, Admin! Gunakan menu di kiri/atas untuk mengelola data. Ringkasan di atas terupdate otomatis dari data sistem.
    </div>
  </div>
</x-app-layout>
