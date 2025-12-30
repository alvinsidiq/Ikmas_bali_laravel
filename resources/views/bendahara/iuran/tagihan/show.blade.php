<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Tagihan {{ $tagihan->kode }}</h2></x-slot>
  <div class="p-6 space-y-6">
    <div class="bg-white rounded shadow p-4 grid md:grid-cols-2 gap-4">
      <div>
        <div><strong>Nama:</strong> {{ $tagihan->user?->name }}</div>
        <div><strong>Judul:</strong> {{ $tagihan->judul }}</div>
        <div><strong>Periode:</strong> {{ $tagihan->periode ?? '-' }}</div>
        <div><strong>Jatuh Tempo:</strong> {{ optional($tagihan->jatuh_tempo)->format('d M Y') }}</div>
        <div><strong>Status:</strong> <span class="px-2 py-1 text-xs rounded {{ ['unpaid'=>'bg-amber-100 text-amber-700','partial'=>'bg-blue-100 text-blue-700','paid'=>'bg-emerald-100 text-emerald-700','overdue'=>'bg-red-100 text-red-700'][$tagihan->status] }}">{{ ucfirst($tagihan->status) }}</span></div>
      </div>
      <div>
        @php($total = max(0,($tagihan->nominal+$tagihan->denda)-$tagihan->diskon))
        <div><strong>Nominal:</strong> Rp {{ number_format($tagihan->nominal,0,',','.') }}</div>
        <div><strong>Denda:</strong> Rp {{ number_format($tagihan->denda,0,',','.') }}</div>
        <div><strong>Diskon:</strong> Rp {{ number_format($tagihan->diskon,0,',','.') }}</div>
        <div class="text-lg mt-1"><strong>Total:</strong> Rp {{ number_format($total,0,',','.') }}</div>
        <div class="text-lg"><strong>Terbayar:</strong> Rp {{ number_format($tagihan->terbayar_verified,0,',','.') }}</div>
      </div>
    </div>

    <div class="bg-white rounded shadow p-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold">Riwayat Pembayaran</h3>
        <a href="{{ route('bendahara.tagihan.edit',$tagihan) }}" class="px-3 py-1 bg-gray-200 rounded">Edit Tagihan</a>
      </div>
      <div class="divide-y">
        @forelse($tagihan->payments as $p)
          <div class="py-2 flex items-start justify-between">
            <div>
              <div class="font-medium">{{ $p->kode }} — Rp {{ number_format($p->amount,0,',','.') }}</div>
              <div class="text-xs text-gray-600">{{ $p->user?->name ?? '-' }} • {{ strtoupper($p->method ?? '-') }} • {{ optional($p->paid_at)->format('d M Y H:i') }}</div>
              @if($p->rejection_reason)
                <div class="text-xs text-red-700">Ditolak: {{ $p->rejection_reason }}</div>
              @endif
              @if($p->bukti_path)
                <a class="text-xs text-blue-600" href="{{ Storage::disk('public')->url($p->bukti_path) }}" target="_blank">Lihat Bukti</a>
              @endif
            </div>
            <div class="text-right space-y-2">
              @php($badge = [
                'submitted'=>'bg-amber-100 text-amber-700',
                'pending_gateway'=>'bg-blue-100 text-blue-700',
                'verified'=>'bg-emerald-100 text-emerald-700',
                'rejected'=>'bg-red-100 text-red-700',
              ][$p->status] ?? 'bg-gray-200 text-gray-700')
              <span class="px-2 py-1 text-xs rounded {{ $badge }}">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span>
              <a class="text-xs px-2 py-1 bg-gray-200 rounded" href="{{ route('bendahara.pembayaran.show',$p) }}">Kelola</a>
            </div>
          </div>
        @empty
          <div class="py-4 text-gray-500">Belum ada pembayaran.</div>
        @endforelse
      </div>
    </div>
  </div>
</x-app-layout>
