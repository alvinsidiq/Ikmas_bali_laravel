<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Bendahara — Iuran</h2></x-slot>
  <div class="p-6 space-y-6">
    <div class="grid md:grid-cols-4 gap-4">
      <div class="bg-white p-4 rounded shadow"><div class="text-sm text-gray-500">Total Tagihan</div><div class="text-2xl font-semibold">Rp {{ number_format($stats['total_tagihan'] ?? 0,0,',','.') }}</div></div>
      <div class="bg-white p-4 rounded shadow"><div class="text-sm text-gray-500">Terbayar</div><div class="text-2xl font-semibold">Rp {{ number_format($stats['total_terbayar'] ?? 0,0,',','.') }}</div></div>
      <div class="bg-white p-4 rounded shadow"><div class="text-sm text-gray-500">Sisa Piutang</div><div class="text-2xl font-semibold">Rp {{ number_format($stats['sisa'] ?? 0,0,',','.') }}</div></div>
      <div class="bg-white p-4 rounded shadow"><div class="text-sm text-gray-500">Overdue</div><div class="text-2xl font-semibold">{{ $stats['overdue'] ?? 0 }}</div></div>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold">Antrian Pembayaran (Submitted)</div>
        <a class="text-sm text-blue-600" href="{{ route('bendahara.pembayaran.index',['status'=>'submitted']) }}">Lihat semua</a>
      </div>
      <div class="divide-y">
        @forelse($submitted as $p)
          <div class="py-2 flex items-center justify-between">
            <div>
              <div class="font-medium">{{ $p->kode }} — Rp {{ number_format($p->amount,0,',','.') }}</div>
              <div class="text-xs text-gray-600">{{ $p->user->name }} • {{ optional($p->paid_at)->format('d M Y H:i') }} • Tagihan {{ optional($p->tagihan)->kode }}</div>
            </div>
            <div><a href="{{ route('bendahara.pembayaran.show',$p) }}" class="px-3 py-1 bg-gray-200 rounded">Verifikasi</a></div>
          </div>
        @empty
          <div class="py-4 text-gray-500">Tidak ada antrian.</div>
        @endforelse
      </div>
    </div>
  </div>
</x-app-layout>

