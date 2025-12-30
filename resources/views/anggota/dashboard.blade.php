<x-anggota-layout title="Dashboard" subtitle="Ringkasan cepat fitur anggota dan keuangan">
  <div class="space-y-6">
    <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-4">
      <a href="{{ route('anggota.kegiatan.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Kegiatan</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['kegiatan']) }}</div>
        <p class="text-xs text-gray-600 mt-1">Agenda yang bisa diikuti</p>
      </a>
      <a href="{{ route('anggota.pengumuman.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Pengumuman</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['pengumuman']) }}</div>
        <p class="text-xs text-gray-600 mt-1">Info terbaru & pinned</p>
      </a>
      <a href="{{ route('anggota.forum.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Forum</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['forum']) }}</div>
        <p class="text-xs text-gray-600 mt-1">Topik diskusi aktif</p>
      </a>
      <a href="{{ route('anggota.arsip.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Arsip</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['arsip']) }}</div>
        <p class="text-xs text-gray-600 mt-1">Dokumen referensi</p>
      </a>
      <a href="{{ route('anggota.dokumentasi.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Dokumentasi</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['album']) }}</div>
        <p class="text-xs text-gray-600 mt-1">Album foto & media</p>
      </a>
      <a href="{{ route('anggota.laporan.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition">
        <p class="text-xs text-gray-500 uppercase">Laporan Kegiatan/Pengumuman</p>
        <div class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-indigo-700">{{ number_format($stats['laporan']) }}</div>
        <p class="text-xs text-gray-600 mt-1">Laporan hasil kegiatan & informasi resmi</p>
      </a>
      <a href="{{ route('anggota.iuran.dashboard') }}" class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition lg:col-span-2">
        <p class="text-xs text-gray-500 uppercase">Keuangan (Iuran)</p>
        <div class="mt-2 flex items-end gap-3">
          <div class="text-3xl font-bold text-gray-900 group-hover:text-indigo-700">Rp {{ number_format($iuran['outstanding_total'],0,',','.') }}</div>
          <div class="text-sm text-amber-700 bg-amber-50 px-2 py-1 rounded">Tagihan: {{ number_format($iuran['unpaid_count']) }}</div>
        </div>
        <p class="text-xs text-gray-600 mt-1">Klik untuk lihat rincian iuran</p>
      </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-4">
      <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase text-gray-500">Keuangan anggota</p>
            <h3 class="text-lg font-semibold text-gray-900">Ringkasan iuran</h3>
          </div>
          <a href="{{ route('anggota.iuran.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Buka detail</a>
        </div>
        <div class="grid sm:grid-cols-3 gap-3">
          <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs text-gray-500 uppercase">Tertunggak</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($iuran['outstanding_total'],0,',','.') }}</p>
          </div>
          <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs text-gray-500 uppercase">Tagihan Aktif</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($iuran['unpaid_count']) }}</p>
          </div>
          <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
            <p class="text-xs text-gray-500 uppercase">Pembayaran Pending</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($iuran['pending_payments']) }}</p>
          </div>
        </div>
        @if($iuran['last_payment'])
          <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-4">
            <p class="text-xs uppercase text-emerald-700">Pembayaran terakhir</p>
            <p class="text-sm text-gray-800 mt-1">
              Kode {{ $iuran['last_payment']->kode }} — Rp {{ number_format($iuran['last_payment']->amount,0,',','.') }} ({{ ucfirst($iuran['last_payment']->channel ?? 'manual') }})
            </p>
            <p class="text-xs text-gray-600">Tanggal: {{ optional($iuran['last_payment']->paid_at)->format('d M Y H:i') ?? '-' }}</p>
          </div>
        @endif
        <div class="flex flex-wrap gap-2">
          <a class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500" href="{{ route('anggota.iuran.tagihan.index') }}">Lihat tagihan</a>
          <a class="px-4 py-2 rounded-lg border border-slate-200 text-gray-800 hover:bg-slate-50" href="{{ route('anggota.iuran.dashboard') }}">Ringkasan iuran</a>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3">
        <h3 class="text-lg font-semibold text-gray-900">Akses cepat</h3>
        <div class="space-y-2 text-sm text-gray-700">
          <a class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50" href="{{ route('anggota.kegiatan.index') }}">
            <span>Kegiatan & agenda</span>
            <span class="text-xs text-gray-500">{{ number_format($stats['kegiatan']) }}</span>
          </a>
          <a class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50" href="{{ route('anggota.pengumuman.index') }}">
            <span>Pengumuman terbaru</span>
            <span class="text-xs text-gray-500">{{ number_format($stats['pengumuman']) }}</span>
          </a>
          <a class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50" href="{{ route('anggota.forum.index') }}">
            <span>Forum diskusi</span>
            <span class="text-xs text-gray-500">{{ number_format($stats['forum']) }}</span>
          </a>
          <a class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50" href="{{ route('anggota.arsip.index') }}">
            <span>Arsip & dokumen</span>
            <span class="text-xs text-gray-500">{{ number_format($stats['arsip']) }}</span>
          </a>
          <a class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50" href="{{ route('anggota.dokumentasi.index') }}">
            <span>Dokumentasi kegiatan</span>
            <span class="text-xs text-gray-500">{{ number_format($stats['album']) }}</span>
          </a>
          <a class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50" href="{{ route('anggota.laporan.index') }}">
            <span>Laporan kegiatan & pengumuman</span>
            <span class="text-xs text-gray-500">{{ number_format($stats['laporan']) }}</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</x-anggota-layout>
