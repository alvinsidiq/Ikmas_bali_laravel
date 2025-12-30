<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Laporan — Arus Kas (Verified)</h2></x-slot>
  <div class="p-6 space-y-4">
    <form method="GET" class="flex flex-wrap items-end gap-2">
      <div><x-input-label value="Dari"/><x-text-input type="date" name="from" :value="request('from', $start->toDateString())"/></div>
      <div><x-input-label value="Sampai"/><x-text-input type="date" name="to" :value="request('to', $end->toDateString())"/></div>
      <div><x-input-label value="Metode"/>
        <select name="method" class="border-gray-300 rounded">
          <option value="">Semua</option>
          @foreach(['transfer'=>'Transfer','cash'=>'Cash','qris'=>'QRIS'] as $v=>$l)
            <option value="{{ $v }}" @selected(($method ?? null)===$v)>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div><x-input-label value="Anggota (opsional)"/>
        <select name="user_id" class="border-gray-300 rounded">
          <option value="">Semua</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(($userId ?? null)==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <x-primary-button class="h-10">Terapkan</x-primary-button>
      <a href="{{ route('bendahara.laporan.export.arus', request()->query()) }}" class="px-3 py-2 bg-gray-200 rounded">Export CSV</a>
    </form>

    <div class="bg-white rounded shadow">
      <table class="min-w-full text-sm divide-y">
        <thead class="bg-gray-50"><tr>
          <th class="px-3 py-2 text-left">Tanggal</th>
          <th class="px-3 py-2 text-left">Kode</th>
          <th class="px-3 py-2 text-left">Anggota</th>
          <th class="px-3 py-2 text-left">Tagihan</th>
          <th class="px-3 py-2 text-right">Jumlah</th>
          <th class="px-3 py-2 text-left">Metode</th>
        </tr></thead>
        <tbody class="divide-y">
          @forelse($items as $p)
            <tr>
              <td class="px-3 py-2">{{ optional($p->paid_at)->format('d M Y H:i') }}</td>
              <td class="px-3 py-2 font-mono">{{ $p->kode }}</td>
              <td class="px-3 py-2">{{ $p->user?->name }}</td>
              <td class="px-3 py-2">{{ $p->tagihan?->kode }}</td>
              <td class="px-3 py-2 text-right">Rp {{ number_format($p->amount,0,',','.') }}</td>
              <td class="px-3 py-2">{{ strtoupper($p->method ?? '-') }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between">
      <div class="text-sm text-gray-600">Total halaman ini: <span class="font-semibold">Rp {{ number_format($total ?? 0,0,',','.') }}</span></div>
      <div>{{ $items->links() }}</div>
    </div>
  </div>
</x-app-layout>

