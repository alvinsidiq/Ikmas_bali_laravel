<x-anggota-layout title="Beranda Anggota" subtitle="Akses cepat fitur komunitas">
  <div class="space-y-6">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 flex items-center justify-between gap-3">
      <div>
        <p class="text-xs uppercase text-gray-500 tracking-wide">Selamat datang</p>
        <h1 class="text-xl font-semibold text-gray-900">Halo, {{ auth()->user()->name }}!</h1>
        <p class="text-sm text-gray-600 mt-1">Gunakan pintasan di bawah untuk membuka fitur utama.</p>
      </div>
      <div class="hidden sm:block">
        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 001-1V10"></path></svg>
        </span>
      </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <a href="{{ route('anggota.kegiatan.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Kegiatan</p>
        <h3 class="mt-2 text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Agenda & Pendaftaran</h3>
        <p class="text-sm text-gray-600 mt-1">Lihat jadwal dan daftar kegiatan.</p>
      </a>
      <a href="{{ route('anggota.pengumuman.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Pengumuman</p>
        <h3 class="mt-2 text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Info Terbaru</h3>
        <p class="text-sm text-gray-600 mt-1">Baca pengumuman dan pin terbaru.</p>
      </a>
      <a href="{{ route('anggota.forum.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Forum</p>
        <h3 class="mt-2 text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Diskusi Komunitas</h3>
        <p class="text-sm text-gray-600 mt-1">Buat atau ikuti topik forum.</p>
      </a>
      <a href="{{ route('anggota.iuran.dashboard') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Keuangan</p>
        <h3 class="mt-2 text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Ringkasan & Tagihan</h3>
        <p class="text-sm text-gray-600 mt-1">Pantau iuran, tagihan, dan pembayaran.</p>
      </a>
      <a href="{{ route('anggota.laporan.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Laporan</p>
        <h3 class="mt-2 text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Pengaduan & Saran</h3>
        <p class="text-sm text-gray-600 mt-1">Catat laporan anggota dan pantau tindak lanjutnya.</p>
      </a>
      <a href="{{ route('anggota.arsip.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Arsip</p>
        <h3 class="mt-2 text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Dokumen & Referensi</h3>
        <p class="text-sm text-gray-600 mt-1">Akses arsip penting komunitas.</p>
      </a>
    </div>
  </div>
</x-anggota-layout>
