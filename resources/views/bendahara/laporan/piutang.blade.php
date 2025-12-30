<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Laporan — Piutang & Aging</h2></x-slot>
  <div class="p-6 space-y-4">
    <form method="GET" class="flex flex-wrap items-end gap-2">
      <div><x-input-label value="As Of"/><x-text-input type="date" name="as_of" :value="request('as_of', $asOf->toDateString())"/></div>
      <x-primary-button class="h-10">Terapkan</x-primary-button>
      <a href="{{ route('bendahara.laporan.export.piutang', ['as_of'=>request('as_of',$asOf->toDateString())]) }}" class="px-3 py-2 bg-gray-200 rounded">Export CSV</a>
    </form>

    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full divide-y text-sm">
        <thead class="bg-gray-50"><tr>
          <th class="px-3 py-2 text-left">Anggota</th>
          <th class="px-3 py-2 text-right">Total</th>
          <th class="px-3 py-2 text-right">0-30</th>
          <th class="px-3 py-2 text-right">31-60</th>
          <th class="px-3 py-2 text-right">61-90</th>
          <th class="px-3 py-2 text-right">90+</th>
        </tr></thead>
        <tbody class="divide-y">
          @forelse($rows as $row)
            @php($b=$row['buckets']??[])
            <tr>
              <td class="px-3 py-2">{{ $row['name'] }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($row['total'],0,',','.') }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($b['0-30']??0,0,',','.') }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($b['31-60']??0,0,',','.') }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($b['61-90']??0,0,',','.') }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($b['90+']??0,0,',','.') }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Tidak ada piutang.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-app-layout>

