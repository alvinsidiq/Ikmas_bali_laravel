<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Pembayaran — Verifikasi</h2></x-slot>
  <div class="p-6">
    <div class="mb-4">
      <a href="{{ route('bendahara.pembayaran.index',['status'=>'submitted']) }}" class="px-3 py-1 rounded {{ $st==='submitted'?'bg-blue-600 text-white':'bg-gray-200' }}">Submitted</a>
      <a href="{{ route('bendahara.pembayaran.index',['status'=>'verified']) }}" class="px-3 py-1 rounded {{ $st==='verified'?'bg-blue-600 text-white':'bg-gray-200' }}">Verified</a>
      <a href="{{ route('bendahara.pembayaran.index',['status'=>'rejected']) }}" class="px-3 py-1 rounded {{ $st==='rejected'?'bg-blue-600 text-white':'bg-gray-200' }}">Rejected</a>
    </div>
    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full divide-y">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left">Kode</th>
            <th class="px-4 py-2 text-left">Anggota</th>
            <th class="px-4 py-2 text-left">Tagihan</th>
            <th class="px-4 py-2 text-left">Jumlah</th>
            <th class="px-4 py-2 text-left">Metode</th>
            <th class="px-4 py-2 text-left">Channel</th>
            <th class="px-4 py-2 text-left">Dibayar</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($items as $p)
          <tr>
            <td class="px-4 py-2 font-mono">{{ $p->kode }}</td>
            <td class="px-4 py-2">{{ $p->user?->name }}</td>
            <td class="px-4 py-2">{{ $p->tagihan?->kode }}</td>
            <td class="px-4 py-2">Rp {{ number_format($p->amount,0,',','.') }}</td>
            <td class="px-4 py-2">{{ strtoupper($p->method ?? '-') }}</td>
            <td class="px-4 py-2">{{ ucfirst($p->channel ?? 'manual') }}</td>
            <td class="px-4 py-2">{{ optional($p->paid_at)->format('d M Y H:i') }}</td>
            @php($badge = [
                'submitted'=>'bg-amber-100 text-amber-700',
                'pending_gateway'=>'bg-blue-100 text-blue-700',
                'verified'=>'bg-emerald-100 text-emerald-700',
                'rejected'=>'bg-red-100 text-red-700'
            ][$p->status] ?? 'bg-gray-200 text-gray-700')
            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded {{ $badge }}">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span></td>
            <td class="px-4 py-2 text-right"><a class="px-3 py-1 bg-gray-200 rounded" href="{{ route('bendahara.pembayaran.show',$p) }}">Detail</a></td>
          </tr>
          @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-app-layout>
