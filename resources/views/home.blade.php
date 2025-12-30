<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wide">Home</p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ikmast Bali Portal Anggota
            </h2>
        </div>
    </x-slot>

    @php
        $formatCurrency = function ($value) {
            return 'Rp '.number_format((int) $value, 0, ',', '.');
        };
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @php
                $tabs = [
                    ['label'=>'Home','route'=>'home','active'=>request()->routeIs('home')],
                    ['label'=>'Kegiatan','route'=>'anggota.kegiatan.index','active'=>request()->routeIs('anggota.kegiatan.*')],
                    ['label'=>'Pengumuman','route'=>'anggota.pengumuman.index','active'=>request()->routeIs('anggota.pengumuman.*')],
                    ['label'=>'Forum','route'=>'anggota.forum.index','active'=>request()->routeIs('anggota.forum.*')],
                    ['label'=>'Arsip','route'=>'anggota.arsip.index','active'=>request()->routeIs('anggota.arsip.*')],
                    ['label'=>'Dokumentasi','route'=>'anggota.dokumentasi.index','active'=>request()->routeIs('anggota.dokumentasi.*')],
                    ['label'=>'Keuangan','route'=>'anggota.iuran.dashboard','active'=>request()->routeIs('anggota.iuran.*'),'children'=>[
                        ['label'=>'Ringkasan Keuangan','route'=>'anggota.iuran.dashboard'],
                        ['label'=>'Tagihan Iuran','route'=>'anggota.iuran.tagihan.index'],
                    ]],
                    ['label'=>'Laporan','route'=>'anggota.laporan.index','active'=>request()->routeIs('anggota.laporan.*')],
                ];
            @endphp
            <div class="bg-white border border-slate-200 shadow-sm rounded-2xl px-4 py-3 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-indigo-600 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 001-1V10"></path></svg>
                    </span>
                    <span>{{ config('branding.app_name') ?? config('app.name','Ikmas Bali') }}</span>
                </div>
                <nav class="flex flex-wrap gap-4 text-sm font-semibold">
                    @foreach($tabs as $t)
                        @if(Route::has($t['route']))
                            @if(isset($t['children']))
                                <x-dropdown align="left" width="48">
                                    <x-slot name="trigger">
                                        @php
                                            $activeClass = 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-gray-900';
                                            $inactiveClass = 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
                                        @endphp
                                        <button class="{{ $t['active'] ? $activeClass : $inactiveClass }}">
                                            {{ $t['label'] }}
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        @foreach($t['children'] as $child)
                                            @if(Route::has($child['route']))
                                                <x-dropdown-link :href="route($child['route'])">
                                                    {{ $child['label'] }}
                                                </x-dropdown-link>
                                            @endif
                                        @endforeach
                                    </x-slot>
                                </x-dropdown>
                            @else
                                @php
                                    $activeClass = 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-gray-900';
                                    $inactiveClass = 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
                                @endphp
                                <a href="{{ route($t['route']) }}"
                                   class="{{ $t['active'] ? $activeClass : $inactiveClass }}"
                                   @if($t['active']) aria-current="page" @endif>
                                    {{ $t['label'] }}
                                </a>
                            @endif
                        @endif
                    @endforeach
                </nav>
            </div>

            <div class="relative overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="absolute -top-20 -right-16 w-72 h-72 bg-indigo-200/60 blur-3xl rounded-full"></div>
                <div class="grid lg:grid-cols-2">
                    <div class="p-10 lg:py-16 lg:px-14">
                        <p class="text-xs font-semibold uppercase tracking-[0.4em] text-indigo-600 mb-4">Beranda Utama</p>
                        <h3 class="text-4xl font-bold text-gray-900 leading-tight">Halo, {{ $user->name }}! Mari bangun solidaritas dan karya terbaik komunitas.</h3>
                        <p class="text-base text-gray-600 mt-4 max-w-2xl">
                            Ikuti perkembangan kegiatan, pantau forum dan arsip, serta cek iuran langsung dari satu jendela. Kami kumpulkan ringkasan penting agar kamu bisa fokus berkontribusi.
                        </p>
                        @php
                            $roleNames = method_exists($user, 'getRoleNames')
                                ? $user->getRoleNames()
                                : collect();
                        @endphp
                        @if($roleNames->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mt-6">
                                @foreach($roleNames as $role)
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 uppercase tracking-wide">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('anggota.kegiatan.index') }}" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-3 text-white font-semibold shadow hover:bg-indigo-500 transition">
                                Lihat Agenda
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <a href="{{ route('anggota.iuran.tagihan.index') }}" class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-5 py-3 text-gray-700 font-semibold hover:border-indigo-300 hover:text-indigo-700 transition">
                                Pantau Iuran
                            </a>
                        </div>
                        <div class="mt-8 flex items-center gap-6 text-xs uppercase tracking-wide text-gray-500">
                            <div>
                                <p class="text-gray-900 text-2xl font-bold">{{ now()->translatedFormat('d F Y') }}</p>
                                <p>Hari ini</p>
                            </div>
                            <div class="h-10 w-px bg-gray-200"></div>
                            <div>
                                <p class="text-gray-900 text-2xl font-bold">{{ config('app.name', 'Ikmas Bali') }}</p>
                                <p>Identitas Portal</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-6 rounded-3xl bg-gradient-to-br from-indigo-500/80 to-blue-500/80 blur-2xl"></div>
                        <div class="relative m-6 rounded-3xl overflow-hidden ring-4 ring-white/40 shadow-2xl">
                            <x-media-img src="https://imgs.search.brave.com/PzWMvi7IffjYC6rqkr9WywDuPSF8WJqEe5BSjLVX5FI/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly91bml2/ZXJzaXRhc3BhaGxh/d2FuLmFjLmlkL3dw/LWNvbnRlbnQvdXBs/b2Fkcy8yMDE5LzA0/L1doYXRzQXBwLUlt/YWdlLTIwMTgtMDkt/MTktYXQtMTQuMDAu/MDcuanBlZw" alt="Community collaboration" class="h-96 w-full object-cover lg:h-full" />
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-6 text-white">
                                <p class="text-xs uppercase tracking-[0.3em] text-indigo-200">Ikmas Bali</p>
                                <p class="text-xl font-semibold">Semangat Kolaborasi & Kebersamaan</p>
                                <p class="text-sm text-indigo-100 mt-2">Dokumentasi kegiatan terakhir | {{ now()->subWeek()->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach($stats as $label => $value)
                    <div class="bg-white shadow rounded-lg px-6 py-5">
                        <p class="text-sm text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">{{ number_format($value) }}</p>
                        <div class="mt-3 h-1.5 rounded-full bg-indigo-100">
                            <div class="h-1.5 rounded-full bg-indigo-500" style="width: {{ min(100, max(15, $value)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 relative rounded-3xl overflow-hidden shadow-xl">
                    <x-media-img src="" class="w-full h-80 object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                        <p class="text-xs uppercase tracking-[0.4em] text-indigo-200">Highlight</p>
                        <h3 class="text-3xl font-semibold">Ruang Kebersamaan ku</h3>
                        <p class="text-sm text-indigo-100 mt-2 max-w-2xl">
                            Foto kolase dari beberapa kegiatan terbaru. Jadikan inspirasi untuk program selanjutnya.
                        </p>
                    </div>
                </div>
                <div class="grid gap-4">
                    <div class="rounded-2xl overflow-hidden shadow-lg relative">
                        <x-media-img src="" alt="Forum diskusi" class="w-full h-40 object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 text-white">
                            <p class="text-xs uppercase tracking-[0.3em] text-indigo-200">Forum</p>
                            <p class="text-lg font-semibold">Diskusi Hangat</p>
                        </div>
                    </div>
                    <div class="rounded-2xl overflow-hidden shadow-lg relative">
                        <x-media-img src="" alt="Dokumentasi" class="w-full h-40 object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 text-white">
                            <p class="text-xs uppercase tracking-[0.3em] text-indigo-200">Galeri</p>
                            <p class="text-lg font-semibold">Momen Terbaik</p>
                        </div>
                    </div>
                </div>
            </div> -->

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase text-gray-500 tracking-wide">Pengumuman</p>
                            <h3 class="text-lg font-semibold text-gray-900">Sorotan terbaru</h3>
                        </div>
                        <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                            {{ $pengumuman->count() }} item
                        </span>
                    </div>
                    <div class="mt-5 space-y-5">
                        @forelse($pengumuman as $item)
                            <div class="border border-gray-100 rounded-lg p-4 hover:border-indigo-200 transition">
                                <p class="text-xs uppercase text-gray-500 tracking-wide flex items-center gap-2">
                                    {{ optional($item->published_at)->translatedFormat('d M Y') ?? 'Tanpa tanggal' }}
                                    @if($item->is_pinned)
                                        <span class="text-amber-600">• Dipin</span>
                                    @endif
                                </p>
                                <p class="mt-1 font-semibold text-gray-900">{{ $item->judul }}</p>
                                <p class="text-sm text-gray-600 mt-2">{{ \Illuminate\Support\Str::limit(strip_tags($item->isi ?? ''), 120) }}</p>
                                <p class="mt-3 text-xs text-gray-500">
                                    Oleh: {{ optional($item->author)->name ?? 'Sistem' }}
                                </p>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Belum ada pengumuman yang diterbitkan.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase text-gray-500 tracking-wide">Kegiatan</p>
                            <h3 class="text-lg font-semibold text-gray-900">Agenda menarik</h3>
                        </div>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                            {{ $kegiatan->count() }} agenda
                        </span>
                    </div>
                    <div class="mt-5 space-y-5">
                        @forelse($kegiatan as $item)
                            <div class="flex gap-4 border border-gray-100 rounded-lg p-4 hover:border-emerald-200 transition">
                                <div class="text-center">
                                    <p class="text-xl font-bold text-emerald-600">{{ optional($item->waktu_mulai)->format('d') }}</p>
                                    <p class="text-xs uppercase text-gray-500">{{ optional($item->waktu_mulai)->format('M') }}</p>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $item->judul }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ optional($item->waktu_mulai)->format('d M Y H:i') }} @if($item->waktu_selesai) - {{ optional($item->waktu_selesai)->format('H:i') }} @endif</p>
                                    <p class="text-sm text-gray-500">{{ $item->lokasi }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Belum ada jadwal kegiatan terbaru.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase text-gray-500 tracking-wide">Forum</p>
                            <h3 class="text-lg font-semibold text-gray-900">Diskusi aktif</h3>
                        </div>
                        <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                            {{ $forumTopics->count() }} topik
                        </span>
                    </div>
                    <div class="mt-5 space-y-4">
                        @forelse($forumTopics as $topic)
                            <div class="border border-gray-100 rounded-lg p-4 hover:border-purple-200 transition">
                                <p class="text-xs uppercase tracking-wide text-gray-500 flex items-center gap-2">
                                    {{ $topic->kategori ?? 'Umum' }}
                                    @if($topic->is_solved)
                                        <span class="text-emerald-600 font-semibold">• Selesai</span>
                                    @endif
                                </p>
                                <p class="font-semibold text-gray-900 mt-1">{{ $topic->judul }}</p>
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ optional($topic->author)->name ?? 'Anonim' }} •
                                    {{ optional($topic->last_post_at)->diffForHumans() ?? 'Belum ada balasan' }}
                                </p>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Forum masih sepi. Mulai diskusi baru sekarang.</p>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm uppercase text-gray-500 tracking-wide">Dokumentasi</p>
                                <h3 class="text-lg font-semibold text-gray-900">Album terbaru</h3>
                            </div>
                            <span class="text-xs font-semibold text-sky-600 bg-sky-50 px-3 py-1 rounded-full">
                                {{ $albums->count() }} album
                            </span>
                        </div>
                        <div class="mt-5 space-y-4">
                            @forelse($albums as $album)
                                <div class="border border-gray-100 rounded-lg p-4 hover:border-sky-200 transition">
                                    <p class="font-semibold text-gray-900">{{ $album->judul }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ optional($album->tanggal_kegiatan)->format('d M Y') }} • {{ $album->lokasi }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $album->media_count ?? 0 }} media</p>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Belum ada dokumentasi yang dirilis.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm uppercase text-gray-500 tracking-wide">Arsip</p>
                                <h3 class="text-lg font-semibold text-gray-900">Referensi pilihan</h3>
                            </div>
                            <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-1 rounded-full">
                                {{ $arsip->count() }} arsip
                            </span>
                        </div>
                        <div class="mt-5 space-y-4">
                            @forelse($arsip as $item)
                                <div class="border border-gray-100 rounded-lg p-4 hover:border-rose-200 transition">
                                    <p class="font-semibold text-gray-900">{{ $item->judul }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $item->kategori }} • {{ $item->tahun }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Nomor: {{ $item->nomor_dokumen ?? '-' }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Belum ada arsip yang dipublikasikan.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase text-gray-500 tracking-wide">Ringkasan Iuran</p>
                        <h3 class="text-lg font-semibold text-gray-900">Status keuangan komunitas</h3>
                    </div>
                    <div class="flex gap-3">
                        <div class="text-center px-4 py-3 rounded-xl bg-gray-50">
                            <p class="text-xs uppercase text-gray-500">Total Tagihan</p>
                            <p class="text-xl font-semibold text-gray-900">{{ number_format($iuranOverview['total']) }}</p>
                        </div>
                        <div class="text-center px-4 py-3 rounded-xl bg-emerald-50">
                            <p class="text-xs uppercase text-emerald-600">Lunas</p>
                            <p class="text-xl font-semibold text-emerald-700">{{ number_format($iuranOverview['paid']) }}</p>
                        </div>
                        <div class="text-center px-4 py-3 rounded-xl bg-amber-50">
                            <p class="text-xs uppercase text-amber-600">Belum Lunas</p>
                            <p class="text-xl font-semibold text-amber-700">{{ number_format($iuranOverview['open']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Tagihan milik Anda</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                                    <th class="py-2">Kode</th>
                                    <th class="py-2">Periode</th>
                                    <th class="py-2">Jatuh Tempo</th>
                                    <th class="py-2 text-right">Total</th>
                                    <th class="py-2 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @if($dueTagihan->isNotEmpty())
                                    @foreach($dueTagihan as $tagihan)
                                        <tr>
                                            <td class="py-3 font-medium text-gray-900">{{ $tagihan->kode }}</td>
                                            <td class="py-3 text-gray-600">{{ $tagihan->periode }}</td>
                                            <td class="py-3 text-gray-600">{{ optional($tagihan->jatuh_tempo)->format('d M Y') }}</td>
                                            <td class="py-3 text-right text-gray-900">{{ $formatCurrency($tagihan->nominal + $tagihan->denda - $tagihan->diskon) }}</td>
                                            <td class="py-3 text-right">
                                                @php
                                                    $badgeClasses = [
                                                        'paid' => 'bg-emerald-100 text-emerald-700',
                                                        'partial' => 'bg-amber-100 text-amber-700',
                                                        'unpaid' => 'bg-rose-100 text-rose-700',
                                                    ];
                                                    $status = strtolower($tagihan->status);
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClasses[$status] ?? 'bg-gray-100 text-gray-600' }}">
                                                    {{ \Illuminate\Support\Str::ucfirst($tagihan->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada tagihan tertunggak. Terima kasih!</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
