<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Ledger — {{ $user->name }}</h2></x-slot>
  <div class="p-6 space-y-6">
    <div class="bg-white rounded shadow p-4">
      <div><strong>Periode:</strong> {{ $start->format('d M Y') }} s/d {{ $end->format('d M Y') }}</div>
    </div>

    <div class="bg-white rounded shadow p-4">
      <h3 class="font-semibold mb-2">Tagihan</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y text-sm">
          <thead class="bg-gray-50"><tr>
            <th class="px-3 py-2 text-left">Kode</th>
            <th class="px-3 py-2 text-left">Judul</th>
            <th class="px-3 py-2 text-left">Jatuh Tempo</th>
            <th class="px-3 py-2 text-right">Total</th>
          </tr></thead>
          <tbody class="divide-y">
            @php($tTotal=0)
            @forelse($tagihan as $t)
              @php($tot = max(0,($t->nominal+$t->denda)-$t->diskon))
              @php($tTotal += $tot)
              <tr>
                <td class="px-3 py-2 font-mono">{{ $t->kode }}</td>
                <td class="px-3 py-2">{{ $t->judul }}</td>
                <td class="px-3 py-2">{{ optional($t->jatuh_tempo)->format('d M Y') }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($tot,0,',','.') }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">Tidak ada tagihan.</td></tr>
            @endforelse
          </tbody>
          <tfoot><tr class="bg-gray-50 font-semibold"><td colspan="3" class="px-3 py-2 text-right">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($tTotal,0,',','.') }}</td></tr></tfoot>
        </table>
      </div>
    </div>

    <div class="bg-white rounded shadow p-4">
      <h3 class="font-semibold mb-2">Pembayaran (Verified)</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y text-sm">
          <thead class="bg-gray-50"><tr>
            <th class="px-3 py-2 text-left">Kode</th>
            <th class="px-3 py-2 text-left">Tanggal</th>
            <th class="px-3 py-2 text-right">Jumlah</th>
            <th class="px-3 py-2 text-left">Metode</th>
          </tr></thead>
          <tbody class="divide-y">
            @php($pTotal=0)
            @forelse($pays as $p)
              @php($pTotal += $p->amount)
              <tr>
                <td class="px-3 py-2 font-mono">{{ $p->kode }}</td>
                <td class="px-3 py-2">{{ optional($p->paid_at)->format('d M Y H:i') }}</td>
                <td class="px-3 py-2 text-right">Rp {{ number_format($p->amount,0,',','.') }}</td>
                <td class="px-3 py-2">{{ strtoupper($p->method ?? '-') }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">Tidak ada pembayaran.</td></tr>
            @endforelse
          </tbody>
          <tfoot><tr class="bg-gray-50 font-semibold"><td colspan="2" class="px-3 py-2 text-right">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($pTotal,0,',','.') }}</td><td></td></tr></tfoot>
        </table>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('bendahara.laporan.export.ledger',$user) }}?from={{ $start->toDateString() }}&to={{ $end->toDateString() }}" class="px-3 py-2 bg-gray-200 rounded">Export CSV</a>
      <a href="{{ route('bendahara.laporan.anggota.index') }}?from={{ $start->toDateString() }}&to={{ $end->toDateString() }}" class="px-3 py-2 bg-gray-200 rounded">Kembali</a>
    </div>
  </div>
</x-app-layout>

