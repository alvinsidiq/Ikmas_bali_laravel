<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Laporan — Ringkasan</h2></x-slot>
  <div class="p-6 space-y-6">
    <form method="GET" class="flex flex-wrap items-end gap-2">
      <div>
        <x-input-label for="from" value="Dari"/>
        <x-text-input id="from" type="date" name="from" :value="request('from', $start->toDateString())" />
      </div>
      <div>
        <x-input-label for="to" value="Sampai"/>
        <x-text-input id="to" type="date" name="to" :value="request('to', $end->toDateString())" />
      </div>
      <div>
        <x-input-label for="user_id" value="Anggota (opsional)"/>
        <select id="user_id" name="user_id" class="border-gray-300 rounded">
          <option value="">Semua</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(($userId ?? null)==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <x-input-label for="method" value="Metode"/>
        <select id="method" name="method" class="border-gray-300 rounded">
          <option value="">Semua</option>
          @foreach(['transfer'=>'Transfer','cash'=>'Cash','qris'=>'QRIS'] as $v=>$l)
            <option value="{{ $v }}" @selected(($method ?? null)===$v)>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <x-primary-button class="h-10">Terapkan</x-primary-button>
      <a href="{{ route('bendahara.laporan.export.index', request()->query()) }}" class="px-3 py-2 bg-gray-200 rounded">Export CSV</a>
    </form>

    <div class="grid md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Total Tagihan (berdasar jatuh tempo)</div>
        <div class="text-2xl font-semibold">Rp {{ number_format($totalTagihan ?? 0,0,',','.') }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Total Pembayaran (verified)</div>
        <div class="text-2xl font-semibold">Rp {{ number_format($totalBayar ?? 0,0,',','.') }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Outstanding per {{ $end->format('d M Y') }}</div>
        <div class="text-2xl font-semibold">Rp {{ number_format($outstanding ?? 0,0,',','.') }}</div>
      </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <div class="font-semibold mb-2">Arus Kas (verified) per Hari</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">Tanggal</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
          <tbody class="divide-y">
            @forelse($arus as $row)
              <tr><td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($row->d)->format('d M Y') }}</td><td class="px-3 py-2 text-right">Rp {{ number_format($row->total,0,',','.') }}</td></tr>
            @empty
              <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Tidak ada pemasukan terverifikasi pada periode ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between mb-2"><div class="font-semibold">Top 10 Piutang per Anggota (as of {{ $end->format('d M Y') }})</div>
        <a class="text-sm text-blue-600" href="{{ route('bendahara.laporan.piutang',['as_of'=>$end->toDateString()]) }}">Lihat semua</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50"><tr>
            <th class="px-3 py-2 text-left">Anggota</th>
            <th class="px-3 py-2 text-right">Total</th>
            <th class="px-3 py-2 text-right">0-30</th>
            <th class="px-3 py-2 text-right">31-60</th>
            <th class="px-3 py-2 text-right">61-90</th>
            <th class="px-3 py-2 text-right">90+</th>
          </tr></thead>
          <tbody class="divide-y">
            @forelse(($topPiutang ?? []) as $row)
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
              <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada piutang.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>

