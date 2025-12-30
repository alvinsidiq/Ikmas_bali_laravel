@php
    $isAdmin = $user && $user->hasRole('admin');
    $isBendahara = $user && $user->hasRole('bendahara');
    $linkClass = function (bool $active) {
        return ($active ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900') . ' flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition';
    };
@endphp

<div x-cloak>
    <div x-show="sidebarOpen" class="fixed inset-0 z-30 bg-black/60 lg:hidden" @click="sidebarOpen = false"></div>

    <aside class="h-full fixed inset-y-0 left-0 z-40 w-72 transform bg-white text-gray-800 border-r border-gray-200 shadow-lg transition duration-200 ease-in-out lg:static lg:translate-x-0" :class="{'-translate-x-full lg:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen}">
        <div class="h-full flex flex-col">
            <div class="p-4 h-16 border-b border-gray-200 flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg text-gray-900">
                  <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-indigo-600 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 001-1V10"></path></svg>
                  </span>
                  <span>{{ config('app.name', 'Ikmas Bali') }}</span>
                </a>
                <button class="ml-auto text-gray-500 hover:text-gray-800 lg:hidden" @click="sidebarOpen = false">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-4 space-y-6">
                @if($isAdmin)
                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Admin</p>
                        <div class="space-y-1">
                            <a class="{{ $linkClass(request()->routeIs('admin.dashboard')) }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            <a class="{{ $linkClass(request()->routeIs('admin.anggota.*')) }}" href="{{ route('admin.anggota.index') }}">Manajemen Anggota</a>
                            <a class="{{ $linkClass(request()->routeIs('admin.kegiatan.*')) }}" href="{{ route('admin.kegiatan.index') }}">Kegiatan</a>
                            <a class="{{ $linkClass(request()->routeIs('admin.pengumuman.*')) }}" href="{{ route('admin.pengumuman.index') }}">Pengumuman</a>
                            <a class="{{ $linkClass(request()->routeIs('admin.forum.*')) }}" href="{{ route('admin.forum.index') }}">Forum</a>
                            <a class="{{ $linkClass(request()->routeIs('admin.arsip.*')) }}" href="{{ route('admin.arsip.index') }}">Arsip</a>
                            <a class="{{ $linkClass(request()->routeIs('admin.dokumentasi.albums.*')) }}" href="{{ route('admin.dokumentasi.albums.index') }}">Dokumentasi</a>
                            @if(Route::has('admin.laporan.index'))
                                <a class="{{ $linkClass(request()->routeIs('admin.laporan.*')) }}" href="{{ route('admin.laporan.index') }}">Laporan</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if($isBendahara)
                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Bendahara</p>
                        <div class="space-y-1">
                            <a class="{{ $linkClass(request()->routeIs('bendahara.dashboard')) }}" href="{{ route('bendahara.dashboard') }}">Dashboard</a>
                            <a class="{{ $linkClass(request()->routeIs('bendahara.iuran.*')) }}" href="{{ route('bendahara.iuran.dashboard') }}">Ringkasan Iuran</a>
                            <a class="{{ $linkClass(request()->routeIs('bendahara.tagihan.*')) }}" href="{{ route('bendahara.tagihan.index') }}">Tagihan</a>
                            <a class="{{ $linkClass(request()->routeIs('bendahara.pembayaran.*')) }}" href="{{ route('bendahara.pembayaran.index') }}">Pembayaran</a>
                            <a class="{{ $linkClass(request()->routeIs('bendahara.bulk.*')) }}" href="{{ route('bendahara.bulk.index') }}">Generate Massal</a>
                            <a class="{{ $linkClass(request()->routeIs('bendahara.laporan.*')) }}" href="{{ route('bendahara.laporan.index') }}">Laporan</a>
                        </div>
                    </div>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-200 space-y-3 bg-gray-50">
                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="w-full text-center px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200">Profil</a>
                    <form class="w-full" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
</div>
