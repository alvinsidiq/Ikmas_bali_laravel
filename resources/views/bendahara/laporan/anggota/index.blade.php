<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Laporan — Per Anggota</h2></x-slot>
  <div class="p-6 space-y-4">
    <form method="GET" class="flex flex-wrap items-end gap-2">
      <div><x-input-label value="Dari"/><x-text-input type="date" name="from" :value="request('from', $start->toDateString())"/></div>
      <div><x-input-label value="Sampai"/><x-text-input type="date" name="to" :value="request('to', $end->toDateString())"/></div>
      <x-primary-button class="h-10">Terapkan</x-primary-button>
      <a href="{{ route('bendahara.laporan.export.anggota', request()->query()) }}" class="px-3 py-2 bg-gray-200 rounded">Export CSV</a>
    </form>

    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full divide-y text-sm">
        <thead class="bg-gray-50"><tr>
          <th class="px-3 py-2 text-left">Anggota</th>
          <th class="px-3 py-2 text-right">Total Tagihan</th>
          <th class="px-3 py-2 text-right">Total Pembayaran</th>
          <th class="px-3 py-2 text-right">Outstanding (as of {{ $end->format('d M Y') }})</th>
          <th class="px-3 py-2"></th>
        </tr></thead>
        <tbody class="divide-y">
          @forelse($out as $row)
            <tr>
              <td class="px-3 py-2">{{ $row['name'] }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($row['tagihan'],0,',','.') }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($row['bayar'],0,',','.') }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($row['sisa'],0,',','.') }}</td>
              <td class="px-3 py-2 text-right"><a href="{{ route('bendahara.laporan.anggota.show',$row['id']) }}?from={{ request('from',$start->toDateString()) }}&to={{ request('to',$end->toDateString()) }}" class="px-3 py-1 bg-gray-200 rounded">Ledger</a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-app-layout>

