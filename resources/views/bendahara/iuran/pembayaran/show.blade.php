<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Pembayaran {{ $pembayaran->kode }}</h2></x-slot>
  <div class="p-6 space-y-6">
    <div class="bg-white rounded shadow p-4 grid md:grid-cols-2 gap-4">
      <div>
        <div><strong>Anggota:</strong> {{ $pembayaran->user?->name }}</div>
        <div><strong>Tagihan:</strong> {{ $pembayaran->tagihan?->kode }} — {{ $pembayaran->tagihan?->judul }}</div>
        <div><strong>Jumlah:</strong> Rp {{ number_format($pembayaran->amount,0,',','.') }}</div>
        <div><strong>Metode:</strong> {{ strtoupper($pembayaran->method ?? '-') }}</div>
        <div><strong>Channel:</strong> {{ ucfirst($pembayaran->channel ?? 'manual') }}</div>
        <div><strong>Dibayar:</strong> {{ optional($pembayaran->paid_at)->format('d M Y H:i') }}</div>
        @php($badge = [
            'submitted'=>'bg-amber-100 text-amber-700',
            'verified'=>'bg-emerald-100 text-emerald-700',
            'rejected'=>'bg-red-100 text-red-700',
            'pending_gateway'=>'bg-blue-100 text-blue-700'
        ][$pembayaran->status] ?? 'bg-gray-200 text-gray-700')
        <div><strong>Status:</strong> <span class="px-2 py-1 text-xs rounded {{ $badge }}">{{ ucfirst(str_replace('_',' ',$pembayaran->status)) }}</span></div>
        @if($pembayaran->rejection_reason)
          <div class="text-red-700"><strong>Alasan Ditolak:</strong> {{ $pembayaran->rejection_reason }}</div>
        @endif
        @if($pembayaran->channel === 'gateway')
          <div><strong>Gateway Ref:</strong> {{ $pembayaran->gateway_reference ?? '-' }}</div>
          <div><strong>Link Bukti:</strong> @if($pembayaran->gateway_receipt_url)<a class="text-blue-600" href="{{ $pembayaran->gateway_receipt_url }}" target="_blank">{{ $pembayaran->gateway_receipt_url }}</a>@else - @endif</div>
        @endif
      </div>
      <div>
        @if($pembayaran->bukti_path)
          <div class="font-semibold mb-2">Bukti Pembayaran</div>
          <a class="text-blue-600" href="{{ Storage::disk('public')->url($pembayaran->bukti_path) }}" target="_blank">Lihat / Unduh</a>
          <div class="text-xs text-gray-500 mt-1">{{ $pembayaran->bukti_mime }} • {{ number_format(($pembayaran->bukti_size ?? 0)/1024,1) }} KB</div>
        @else
          <div class="text-gray-500">Tidak ada bukti diunggah.</div>
        @endif
      </div>
    </div>

    <div class="bg-white rounded shadow p-4 flex flex-wrap gap-2">
      @if($pembayaran->status==='submitted' && $pembayaran->channel === 'manual')
        <form method="POST" action="{{ route('bendahara.pembayaran.verify',$pembayaran) }}">@csrf<button class="px-4 py-2 bg-emerald-600 text-white rounded" onclick="return confirm('Verifikasi pembayaran ini?')">Verifikasi</button></form>
        <form method="POST" action="{{ route('bendahara.pembayaran.reject',$pembayaran) }}" onsubmit="return confirm('Tolak pembayaran ini?')">
          @csrf
          <input type="text" name="rejection_reason" placeholder="Alasan penolakan" class="border-gray-300 rounded px-2 py-1" />
          <button class="px-4 py-2 bg-red-600 text-white rounded">Tolak</button>
        </form>
      @elseif($pembayaran->channel === 'gateway')
        <span class="text-sm text-gray-600">Pembayaran diproses otomatis melalui Xendit.</span>
      @endif
      <form method="POST" action="{{ route('bendahara.pembayaran.destroy',$pembayaran) }}" onsubmit="return confirm('Hapus pembayaran ini?')">@csrf @method('DELETE')<button class="px-4 py-2 bg-gray-200 rounded">Hapus</button></form>
      <a href="{{ route('bendahara.pembayaran.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a>
    </div>
  </div>
</x-app-layout>
