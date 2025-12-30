<x-anggota-layout :title="'Kwitansi '.$pembayaran->kode" subtitle="Bukti pembayaran terverifikasi">
  <style> .box{border:1px solid #ddd; padding:16px; border-radius:8px;} .muted{color:#666; font-size:12px;} .right{text-align:right;} .mt{margin-top:8px;} @media print {.noprint{display:none}} </style>
  <div class="noprint mb-3">
    <button onclick="window.print()" class="px-3 py-1.5 bg-gray-200 rounded">Cetak</button>
  </div>
  <div class="box bg-white">
    <h2 class="text-xl font-semibold mb-2">Kwitansi Pembayaran</h2>
    <div class="muted">Nomor: {{ $pembayaran->kode }}</div>
    <hr class="mt" />
    <div class="mt-2 overflow-x-auto">
      <table class="w-full text-sm">
        <tr>
          <td>Nama</td>
          <td>: {{ $pembayaran->user->name }}</td>
          <td class="right">Tanggal</td>
          <td class="right">: {{ optional($pembayaran->verified_at ?? $pembayaran->paid_at)->format('d M Y H:i') }}</td>
        </tr>
        <tr>
          <td>Tagihan</td>
          <td>: {{ $pembayaran->tagihan->judul }} ({{ $pembayaran->tagihan->kode }})</td>
          <td class="right">Metode</td>
          <td class="right">: {{ strtoupper($pembayaran->method ?? '-') }}</td>
        </tr>
        <tr>
          <td>Periode</td>
          <td>: {{ $pembayaran->tagihan->periode ?? '-' }}</td>
          <td class="right">Status</td>
          <td class="right">: {{ ucfirst($pembayaran->status) }}</td>
        </tr>
        @if($pembayaran->channel === 'gateway')
        <tr>
          <td>Gateway</td>
          <td>: Xendit ({{ strtoupper($pembayaran->method ?? '-') }})</td>
          <td class="right">Ref</td>
          <td class="right">: {{ $pembayaran->gateway_reference ?? '-' }}</td>
        </tr>
        @endif
      </table>
    </div>
    <h3 class="right text-lg font-semibold mt-4">Jumlah: Rp {{ number_format($pembayaran->amount,0,',','.') }}</h3>
    @if($pembayaran->gateway_receipt_url)
      <p class="mt-2 text-sm">Link bukti Xendit: <a class="text-blue-600" href="{{ $pembayaran->gateway_receipt_url }}" target="_blank">{{ $pembayaran->gateway_receipt_url }}</a></p>
    @endif
    <p class="muted mt-2">Kwitansi ini sah dan dihasilkan otomatis oleh sistem.</p>
  </div>
</x-anggota-layout>
