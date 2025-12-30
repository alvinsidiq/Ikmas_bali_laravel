<x-anggota-layout title="Iuran" subtitle="Tagihan dan pembayaran iuran">
  <div class="space-y-6">
    <div class="grid md:grid-cols-3 gap-4">
      <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4">
        <p class="text-xs uppercase text-gray-500">Total Tunggakan</p>
        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($summary['outstanding_total'] ?? 0,0,',','.') }}</p>
      </div>
      <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4">
        <p class="text-xs uppercase text-gray-500">Jumlah Tagihan Aktif</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['unpaid_count'] ?? 0) }}</p>
      </div>
      <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4">
        <p class="text-xs uppercase text-gray-500">Jatuh Tempo Terdekat</p>
        <p class="text-2xl font-bold text-gray-900">
          {{ optional($summary['next_due'] ?? null)->format('d M Y') ?? '-' }}
        </p>
      </div>
    </div>
    <div class="bg-white shadow rounded-xl p-4">
      <form method="GET" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
        <div class="lg:col-span-2">
          <label class="text-xs text-gray-600">Pencarian</label>
          <div class="relative">
            <span class="absolute left-2 top-2.5 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg></span>
            <input name="q" value="{{ $q }}" placeholder="Judul / kode / periode" class="pl-8 w-full border-gray-300 rounded-md" />
          </div>
        </div>
        <div>
          <label class="text-xs text-gray-600">Status</label>
          <select name="status" class="w-full border-gray-300 rounded">
            <option value="">Semua status</option>
            @foreach(['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue'] as $sv=>$sl)
              <option value="{{ $sv }}" @selected(($st ?? null)===$sv)>{{ $sl }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-xs text-gray-600">Tahun</label>
          <input name="year" value="{{ $yr }}" placeholder="Tahun" class="w-full border-gray-300 rounded" />
        </div>
        <div>
          <label class="text-xs text-gray-600">Bulan</label>
          <input name="month" value="{{ $mo }}" placeholder="1-12" class="w-full border-gray-300 rounded" />
        </div>
        <div>
          <x-primary-button class="w-full sm:w-auto">Filter</x-primary-button>
        </div>
      </form>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($items as $t)
        <div class="bg-white shadow rounded-xl p-4 border border-gray-100">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-xs text-gray-500 font-mono">{{ $t->kode }}</div>
              <a class="font-semibold hover:underline" href="{{ route('anggota.iuran.tagihan.show',$t) }}">{{ $t->judul }}</a>
              <div class="mt-1 text-xs text-gray-600">Periode: {{ $t->periode ?? '-' }} • Jatuh Tempo: {{ optional($t->jatuh_tempo)->format('d M Y') }}</div>
            </div>
            <span class="px-2 py-1 text-xs rounded {{ ['unpaid'=>'bg-amber-100 text-amber-700','partial'=>'bg-blue-100 text-blue-700','paid'=>'bg-emerald-100 text-emerald-700','overdue'=>'bg-red-100 text-red-700'][$t->status] }}">{{ ucfirst($t->status) }}</span>
          </div>
          <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
            <div class="bg-slate-50 rounded p-2">
              <div class="text-xs text-gray-500">Total</div>
              <div class="font-semibold">Rp {{ number_format($t->total_tagihan,0,',','.') }}</div>
            </div>
            <div class="bg-slate-50 rounded p-2">
              <div class="text-xs text-gray-500">Sisa</div>
              <div class="font-semibold">Rp {{ number_format($t->sisa_bayar,0,',','.') }}</div>
            </div>
          </div>
          <div class="mt-4 flex flex-wrap gap-2">
            <a class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded" href="{{ route('anggota.iuran.tagihan.show',$t) }}">Detail</a>
            @if($t->sisa_bayar > 0 && $t->status !== 'paid')
              <a class="px-3 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-500" href="{{ route('anggota.iuran.tagihan.show',$t) }}">Bayar sekarang</a>
            @endif
          </div>
        </div>
      @empty
        <div class="col-span-full">
          <div class="bg-white border border-dashed rounded-xl p-10 text-center text-gray-500">
            Belum ada tagihan. Hubungi bendahara jika ada iuran yang harus diterbitkan.
          </div>
        </div>
      @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-anggota-layout>
