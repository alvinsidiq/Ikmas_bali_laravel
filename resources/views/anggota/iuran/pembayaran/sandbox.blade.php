<x-anggota-layout title="Simulasi Pembayaran Xendit">
  <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
    <div class="flex items-center gap-3">
      <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-white font-bold">X</span>
      <div>
        <h1 class="text-xl font-semibold text-gray-900">Simulasi Pembayaran</h1>
        <p class="text-sm text-gray-600">Sandbox lokal karena tautan Xendit tidak tersedia.</p>
      </div>
    </div>

    <div class="space-y-1 text-sm text-gray-700">
      <div><strong>Kode Pembayaran:</strong> {{ $pembayaran->kode }}</div>
      <div><strong>Tagihan:</strong> {{ $pembayaran->tagihan?->judul }}</div>
      <div><strong>Nominal:</strong> Rp {{ number_format($pembayaran->amount,0,',','.') }}</div>
      <div><strong>Status:</strong> {{ ucfirst($pembayaran->status) }}</div>
    </div>

    <div class="space-y-2">
      <p class="text-sm text-gray-600">Klik tombol di bawah untuk menandai pembayaran sebagai berhasil (simulasi sandbox).</p>
      <div class="flex gap-2">
        <a class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500" href="{{ route('anggota.iuran.gateway.success', $pembayaran) }}">Tandai Berhasil</a>
        <a class="px-4 py-2 rounded bg-gray-200 text-gray-800 hover:bg-gray-100" href="{{ route('anggota.iuran.gateway.cancel', $pembayaran) }}">Batal</a>
      </div>
    </div>
  </div>
</x-anggota-layout>
