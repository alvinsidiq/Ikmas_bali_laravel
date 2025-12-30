<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Bendahara Dashboard</h2></x-slot>
  <div class="p-6 space-y-6">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <a href="{{ route('bendahara.iuran.dashboard') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Total Tagihan</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['tagihan_total']) }}</div>
        <p class="text-xs text-gray-500 mt-1">Outstanding: Rp {{ number_format($stats['tagihan_outstanding'],0,',','.') }}</p>
        <div class="mt-2 text-xs text-indigo-700 underline">Lihat Ringkasan Iuran</div>
      </a>
      <a href="{{ route('bendahara.tagihan.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Tagihan Belum Lunas</p>
        <div class="mt-2 flex items-end gap-2">
          <div class="text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['tagihan_unpaid']) }}</div>
          <div class="text-sm text-red-700 bg-red-50 px-2 py-1 rounded">Overdue: {{ number_format($stats['tagihan_overdue']) }}</div>
        </div>
        <div class="mt-2 text-xs text-indigo-700 underline">Kelola Tagihan</div>
      </a>
      <a href="{{ route('bendahara.pembayaran.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Pembayaran Masuk</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['pembayaran_total']) }}</div>
        <p class="text-xs text-gray-500 mt-1">Pending: {{ number_format($stats['pembayaran_pending']) }}</p>
        <div class="mt-2 text-xs text-indigo-700 underline">Verifikasi Pembayaran</div>
      </a>
      <a href="{{ route('bendahara.laporan.index') }}" class="group rounded-xl border border-slate-200 bg-white shadow-sm p-4 hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Terverifikasi</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">Rp {{ number_format($stats['pembayaran_verified_amount'],0,',','.') }}</div>
        <p class="text-xs text-gray-500 mt-1">Total nilai pembayaran sudah diverifikasi</p>
        <div class="mt-2 text-xs text-indigo-700 underline">Laporan Keuangan</div>
      </a>
    </div>

    <div class="bg-white border border-dashed border-slate-200 rounded-xl p-4 text-sm text-gray-700">
      <p class="font-semibold text-gray-900 mb-2">Akses cepat data:</p>
      <div class="flex flex-wrap gap-3">
        <a href="{{ route('bendahara.iuran.dashboard') }}" class="text-indigo-700 hover:underline">Ringkasan Iuran</a>
        <span class="text-gray-400">•</span>
        <a href="{{ route('bendahara.tagihan.index') }}" class="text-indigo-700 hover:underline">Tagihan</a>
        <span class="text-gray-400">•</span>
        <a href="{{ route('bendahara.pembayaran.index') }}" class="text-indigo-700 hover:underline">Pembayaran</a>
        <span class="text-gray-400">•</span>
        <a href="{{ route('bendahara.laporan.index') }}" class="text-indigo-700 hover:underline">Laporan Keuangan</a>
      </div>
    </div>

    <div class="rounded-xl border border-dashed border-slate-200 bg-white p-6 text-gray-600">
      Selamat datang, Bendahara! Gunakan kartu di atas untuk membuka ringkasan iuran, daftar tagihan, verifikasi pembayaran, dan laporan keuangan lebih cepat.
    </div>
  </div>
</x-app-layout>
