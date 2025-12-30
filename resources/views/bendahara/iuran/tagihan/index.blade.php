<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Tagihan Iuran</h2></x-slot>
  <div class="p-6">
    <div class="flex flex-wrap items-end gap-2 mb-4">
      <form method="GET" class="flex flex-wrap items-end gap-2">
        <x-text-input name="q" placeholder="Cari kode/judul/nama" :value="$q" />
        <select name="status" class="border-gray-300 rounded">
          <option value="">Semua status</option>
          @foreach(['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue'] as $sv=>$sl)
            <option value="{{ $sv }}" @selected($st===$sv)>{{ $sl }}</option>
          @endforeach
        </select>
        <x-text-input name="year" placeholder="Tahun" :value="$yr" />
        <x-text-input name="month" placeholder="Bulan" :value="$mo" />
        <x-primary-button>Filter</x-primary-button>
      </form>
      <div class="ml-auto flex gap-2">
        <a href="{{ route('bendahara.bulk.index') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ Generate Massal</a>
        <a href="{{ route('bendahara.tagihan.create') }}" class="px-3 py-2 bg-emerald-600 text-white rounded">+ Tagihan</a>
        <a href="{{ route('bendahara.tagihan.export') }}" class="px-3 py-2 bg-gray-200 rounded">Export CSV</a>
      </div>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full divide-y">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left">Kode</th>
            <th class="px-4 py-2 text-left">Nama</th>
            <th class="px-4 py-2 text-left">Judul</th>
            <th class="px-4 py-2 text-left">Periode</th>
            <th class="px-4 py-2 text-left">Jatuh Tempo</th>
            <th class="px-4 py-2 text-left">Total</th>
            <th class="px-4 py-2 text-left">Terbayar</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($items as $t)
          @php($total = max(0,($t->nominal+$t->denda)-$t->diskon))
          <tr>
            <td class="px-4 py-2 font-mono">{{ $t->kode }}</td>
            <td class="px-4 py-2">{{ $t->user?->name }}</td>
            <td class="px-4 py-2"><a class="hover:underline" href="{{ route('bendahara.tagihan.show',$t) }}">{{ $t->judul }}</a></td>
            <td class="px-4 py-2">{{ $t->periode ?? '-' }}</td>
            <td class="px-4 py-2">{{ optional($t->jatuh_tempo)->format('d M Y') }}</td>
            <td class="px-4 py-2">Rp {{ number_format($total,0,',','.') }}</td>
            <td class="px-4 py-2">Rp {{ number_format($t->terbayar_verified,0,',','.') }}</td>
            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded {{ ['unpaid'=>'bg-amber-100 text-amber-700','partial'=>'bg-blue-100 text-blue-700','paid'=>'bg-emerald-100 text-emerald-700','overdue'=>'bg-red-100 text-red-700'][$t->status] }}">{{ ucfirst($t->status) }}</span></td>
            <td class="px-4 py-2 text-right"><a class="px-3 py-1 bg-gray-200 rounded" href="{{ route('bendahara.tagihan.show',$t) }}">Detail</a></td>
          </tr>
          @empty
            <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-app-layout>

