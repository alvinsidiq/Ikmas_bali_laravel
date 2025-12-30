<x-anggota-layout title="Iuran" subtitle="Tagihan dan pembayaran iuran">
  <div class="space-y-6">
    <div class="grid md:grid-cols-3 gap-4">
      <div class="bg-white p-4 shadow rounded">
        <div class="text-sm text-gray-500">Total Tertunggak</div>
        <div class="text-2xl font-semibold mt-1">Rp {{ number_format($summary['outstanding_total'],0,',','.') }}</div>
      </div>
      <div class="bg-white p-4 shadow rounded">
        <div class="text-sm text-gray-500">Jumlah Tagihan Aktif</div>
        <div class="text-2xl font-semibold mt-1">{{ $summary['unpaid_count'] }}</div>
      </div>
      <div class="bg-white p-4 shadow rounded">
        <div class="text-sm text-gray-500">Jatuh Tempo Terdekat</div>
        <div class="text-2xl font-semibold mt-1">{{ optional($summary['next_due'])->format('d M Y') ?? '-' }}</div>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <div class="bg-white p-4 shadow rounded">
        <div class="font-semibold mb-2">Tagihan Berjalan</div>
        <div class="divide-y">
          @forelse($unpaid as $t)
            <div class="py-2 flex items-center justify-between">
              <div>
                <div class="font-medium">{{ $t->judul }}</div>
                <div class="text-xs text-gray-600">Kode {{ $t->kode }} • Jatuh tempo {{ optional($t->jatuh_tempo)->format('d M Y') }}</div>
              </div>
              <div class="text-right">
                <div class="text-sm">Sisa: <span class="font-semibold">Rp {{ number_format($t->sisa_bayar,0,',','.') }}</span></div>
                <a class="text-xs text-blue-600" href="{{ route('anggota.iuran.tagihan.show',$t) }}">Detail</a>
              </div>
            </div>
          @empty
            <div class="py-4 text-gray-500">Tidak ada tagihan aktif.</div>
          @endforelse
        </div>
        <div class="mt-3"><a class="px-3 py-1 bg-gray-200 rounded" href="{{ route('anggota.iuran.tagihan.index') }}">Lihat semua tagihan</a></div>
      </div>

      <div class="bg-white p-4 shadow rounded">
        <div class="font-semibold mb-2">Pembayaran Terakhir</div>
        <div class="divide-y">
          @forelse($payments as $p)
            <div class="py-2 flex items-center justify-between">
              <div>
                <div class="font-medium">{{ $p->kode }}</div>
                <div class="text-xs text-gray-600">{{ ucfirst($p->channel ?? 'manual') }} • {{ strtoupper($p->method ?? '-') }} • {{ optional($p->paid_at)->format('d M Y H:i') }}</div>
              </div>
              <div class="text-right">
                <div class="font-semibold">Rp {{ number_format($p->amount,0,',','.') }}</div>
                @php($badge = [
                    'submitted'=>'bg-amber-100 text-amber-700',
                    'verified'=>'bg-emerald-100 text-emerald-700',
                    'rejected'=>'bg-red-100 text-red-700',
                    'pending_gateway'=>'bg-blue-100 text-blue-700'
                ][$p->status] ?? 'bg-gray-100')
                <span class="text-xs px-2 py-0.5 rounded {{ $badge }}">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span>
              </div>
            </div>
          @empty
            <div class="py-4 text-gray-500">Belum ada pembayaran.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</x-anggota-layout>
