<x-anggota-layout :title="'Tagihan: '.$tagihan->kode">
  <style>[x-cloak]{display:none !important;}</style>
  <div class="space-y-6">
    @foreach(['success'=>'green','error'=>'red','info'=>'amber'] as $k=>$c)
      @if(session($k))<div class="p-3 bg-{{ $c }}-100 border border-{{ $c }}-300 rounded">{{ session($k) }}</div>@endif
    @endforeach

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 flex flex-wrap items-center justify-between gap-3">
      <div>
        <p class="text-xs uppercase text-gray-500">Tagihan</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ $tagihan->kode }} — {{ $tagihan->judul }}</h1>
        <p class="text-sm text-gray-600">Periode {{ $tagihan->periode ?? '-' }} • Jatuh tempo {{ optional($tagihan->jatuh_tempo)->format('d M Y') }}</p>
      </div>
      <div class="text-right space-y-1">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ ['unpaid'=>'bg-amber-100 text-amber-700','partial'=>'bg-blue-100 text-blue-700','paid'=>'bg-emerald-100 text-emerald-700','overdue'=>'bg-red-100 text-red-700'][$tagihan->status] ?? 'bg-slate-100 text-slate-700' }}">{{ ucfirst($tagihan->status) }}</span>
        <div class="text-lg font-semibold text-gray-900">Total: Rp {{ number_format($tagihan->total_tagihan,0,',','.') }}</div>
        <div class="text-sm text-gray-600">Sisa: Rp {{ number_format($tagihan->sisa_bayar,0,',','.') }}</div>
      </div>
    </div>

    <div class="bg-white shadow rounded p-4">
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <div><strong>Judul:</strong> {{ $tagihan->judul }}</div>
          <div><strong>Periode:</strong> {{ $tagihan->periode ?? '-' }}</div>
          <div><strong>Jatuh Tempo:</strong> {{ optional($tagihan->jatuh_tempo)->format('d M Y') }}</div>
          <div><strong>Status:</strong> <span class="px-2 py-1 text-xs rounded {{ ['unpaid'=>'bg-amber-100 text-amber-700','partial'=>'bg-blue-100 text-blue-700','paid'=>'bg-emerald-100 text-emerald-700','overdue'=>'bg-red-100 text-red-700'][$tagihan->status] }}">{{ ucfirst($tagihan->status) }}</span></div>
        </div>
        <div>
          <div><strong>Nominal:</strong> Rp {{ number_format($tagihan->nominal,0,',','.') }}</div>
          <div><strong>Denda:</strong> Rp {{ number_format($tagihan->denda,0,',','.') }}</div>
          <div><strong>Diskon:</strong> Rp {{ number_format($tagihan->diskon,0,',','.') }}</div>
          <div class="text-lg mt-1"><strong>Total:</strong> Rp {{ number_format($tagihan->total_tagihan,0,',','.') }}</div>
          <div class="text-lg"><strong>Terbayar (verified):</strong> Rp {{ number_format($tagihan->terbayar_verified,0,',','.') }}</div>
          <div class="text-lg"><strong>Sisa Bayar:</strong> Rp {{ number_format($tagihan->sisa_bayar,0,',','.') }}</div>
        </div>
      </div>
    </div>

    <div class="bg-white shadow rounded p-4">
      <h3 class="font-semibold mb-2">Instruksi Pembayaran</h3>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <div><strong>Bank:</strong> {{ $bank['bank_name'] ?? '—' }}</div>
          <div><strong>No. Rekening:</strong> <span class="font-mono">{{ $bank['bank_account'] ?? '—' }}</span></div>
          <div><strong>Atas Nama:</strong> {{ $bank['account_name'] ?? '—' }}</div>
        </div>
        <div class="prose max-w-none">{!! nl2br(e($bank['instructions'] ?? 'Transfer sesuai nominal dan unggah bukti pembayaran.')) !!}</div>
      </div>
    </div>

    @if($tagihan->sisa_bayar > 0)
    <div class="bg-white shadow rounded p-4">
      <h3 class="font-semibold mb-3">Kirim Pembayaran</h3>
      <form method="POST" action="{{ route('anggota.iuran.tagihan.bayar',$tagihan) }}" enctype="multipart/form-data" class="grid md:grid-cols-4 gap-4" x-data="{ channel: '{{ old('channel','gateway') }}' }">
        @csrf
        <div>
          <x-input-label for="amount" value="Nominal (Rp)" />
          <x-text-input id="amount" name="amount" type="number" min="1000" class="mt-1 block w-full" value="{{ old('amount', $tagihan->sisa_bayar) }}" required />
          <x-input-error :messages="$errors->get('amount')" class="mt-2" />
        </div>
        <div>
          <x-input-label for="paid_at" value="Tanggal Bayar" />
          <x-text-input id="paid_at" name="paid_at" type="datetime-local" class="mt-1 block w-full" value="{{ old('paid_at') }}" />
          <x-input-error :messages="$errors->get('paid_at')" class="mt-2" />
        </div>
        <div class="md:col-span-2">
          <x-input-label value="Pilih Jalur Pembayaran" />
          <div class="mt-2 space-y-2">
            <label class="flex items-center gap-2">
              <input type="radio" name="channel" value="gateway" x-model="channel">
              <span>Transfer / E-Wallet (Xendit, otomatis diverifikasi)</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" name="channel" value="manual" x-model="channel">
              <span>Manual (upload bukti, verifikasi bendahara)</span>
            </label>
          </div>
          <x-input-error :messages="$errors->get('channel')" class="mt-2" />
        </div>
        <div class="md:col-span-4" x-show="channel === 'gateway'" x-cloak>
          <x-input-label value="Metode Xendit" />
          <select name="gateway_method" class="mt-1 block w-full border-gray-300 rounded">
            <option value="transfer" @selected(old('gateway_method')==='transfer')>Virtual Account / Transfer</option>
            <option value="ewallet" @selected(old('gateway_method')==='ewallet')>E-Wallet</option>
          </select>
          <x-input-error :messages="$errors->get('gateway_method')" class="mt-2" />
          <p class="text-xs text-gray-500 mt-1">Anda akan diarahkan ke halaman sandbox Xendit setelah menekan tombol kirim.</p>
        </div>
        <div class="md:col-span-4 space-y-3" x-show="channel === 'manual'" x-cloak>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <x-input-label value="Metode Manual" />
              <select name="manual_method" class="mt-1 block w-full border-gray-300 rounded">
                <option value="transfer" @selected(old('manual_method')==='transfer')>Transfer Manual</option>
                <option value="cash" @selected(old('manual_method')==='cash')>Cash</option>
              </select>
              <x-input-error :messages="$errors->get('manual_method')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="bukti" value="Unggah Bukti (jpg/png/pdf, maks 8MB)" />
              <input id="bukti" type="file" name="bukti" class="block w-full" accept=".jpg,.jpeg,.png,.webp,.pdf" />
              <x-input-error :messages="$errors->get('bukti')" class="mt-2" />
            </div>
          </div>
        </div>
        <div class="md:col-span-4"><x-primary-button>Kirim Pembayaran</x-primary-button></div>
      </form>
    </div>
    @endif

    <div class="bg-white shadow rounded p-4">
      <h3 class="font-semibold mb-2">Riwayat Pembayaran</h3>
      <div class="divide-y">
        @forelse($tagihan->payments as $p)
          @php($isSuccess = $p->status === 'verified')
          <div class="py-2 flex items-start justify-between">
            <div>
              <div class="font-medium">{{ $p->kode }} — Rp {{ number_format($p->amount,0,',','.') }}</div>
              <div class="text-xs text-gray-600 uppercase">
                {{ $p->channel === 'gateway' ? 'Xendit' : 'Manual' }} • {{ strtoupper($p->method ?? '-') }} • {{ optional($p->paid_at)->format('d M Y H:i') }}
              </div>
              @if($p->rejection_reason)
                <div class="text-xs text-red-700">Ditolak: {{ $p->rejection_reason }}</div>
              @endif
              @if($p->bukti_path)
                <a class="text-xs text-blue-600" href="{{ route('anggota.iuran.pembayaran.bukti',$p) }}">Download Bukti</a>
              @endif
              @if($p->gateway_receipt_url)
                <a class="text-xs text-blue-600" href="{{ $p->gateway_receipt_url }}" target="_blank">Bukti Xendit</a>
              @endif
              <div class="text-xs text-gray-500">Status: {{ strtoupper($p->status_pembayaran ?? ($isSuccess ? 'PAID' : 'FAILED')) }}</div>
            </div>
            <div class="text-right space-y-2">
              <div>
                <span class="px-2 py-1 text-xs rounded {{ $isSuccess ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                  {{ $isSuccess ? 'Berhasil' : 'Gagal' }}
                </span>
              </div>
              @if($isSuccess)
                <a class="text-xs px-2 py-1 bg-gray-200 rounded" href="{{ route('anggota.iuran.pembayaran.receipt',$p) }}" target="_blank">Kwitansi</a>
              @endif
            </div>
          </div>
        @empty
          <div class="py-4 text-gray-500">Belum ada pembayaran.</div>
        @endforelse
      </div>
    </div>

    <div><a href="{{ route('anggota.iuran.tagihan.index') }}" class="px-4 py-2 bg-gray-200 rounded">Kembali</a></div>
  </div>
</x-anggota-layout>
