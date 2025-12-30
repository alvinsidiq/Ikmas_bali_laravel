@props(['title' => null, 'subtitle' => null, 'image' => null])
@php
    $brand = config('branding.app_name') ?: config('app.name', 'Ikmas Bali');
    $user = auth()->user();
    $tabs = [
        ['label'=>'Home','route'=>'anggota.home','active'=>request()->routeIs('anggota.home')],
        ['label'=>'Kegiatan','route'=>'anggota.kegiatan.index','active'=>request()->routeIs('anggota.kegiatan.*')],
        ['label'=>'Pengumuman','route'=>'anggota.pengumuman.index','active'=>request()->routeIs('anggota.pengumuman.*')],
        ['label'=>'Forum','route'=>'anggota.forum.index','active'=>request()->routeIs('anggota.forum.*')],
        ['label'=>'Arsip','route'=>'anggota.arsip.index','active'=>request()->routeIs('anggota.arsip.*')],
        ['label'=>'Dokumentasi','route'=>'anggota.dokumentasi.index','active'=>request()->routeIs('anggota.dokumentasi.*')],
        ['label'=>'Keuangan','route'=>'anggota.iuran.dashboard','active'=>request()->routeIs('anggota.iuran.*'),'children'=>[
            ['label'=>'Ringkasan Keuangan','route'=>'anggota.iuran.dashboard'],
            ['label'=>'Tagihan Iuran','route'=>'anggota.iuran.tagihan.index'],
            ['label'=>'Pembayaran','route'=>'anggota.iuran.dashboard', 'anchor'=>'#pembayaran'],
        ]],
        ['label'=>'Laporan','route'=>'anggota.laporan.index','active'=>request()->routeIs('anggota.laporan.*')],
    ];
    $pageTitle = $title ?: (isset($header) ? trim(strip_tags($header)) : $brand);
    $brandTagline = $subtitle ?? (config('branding.tagline') ?: 'Ruang kolaborasi anggota');
    $roleNames = ($user && method_exists($user, 'getRoleNames')) ? $user->getRoleNames() : collect();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} — {{ $brand }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="font-sans antialiased bg-slate-50">
    <div x-data="{ open:false }" class="min-h-screen flex flex-col bg-slate-50 text-gray-900">
      <header class="bg-white/95 backdrop-blur border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <a href="{{ route('anggota.home') }}" class="flex items-center gap-2 font-bold text-lg text-gray-900">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-indigo-600 text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 001-1V10"></path></svg>
              </span>
              <span>{{ $brand }}</span>
            </a>
          </div>
          <nav class="hidden lg:flex items-center gap-1 text-sm font-medium">
            @foreach($tabs as $t)
              @if(Route::has($t['route']))
                @if(isset($t['children']))
                  <x-dropdown align="left" width="48">
                    <x-slot name="trigger">
                      <button class="inline-flex items-center gap-1 px-3 py-2 rounded-full {{ $t['active'] ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-gray-600 hover:text-gray-900 hover:bg-indigo-50' }}">
                        {{ $t['label'] }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                      </button>
                    </x-slot>
                    <x-slot name="content">
                      @foreach($t['children'] as $child)
                        @if(Route::has($child['route']))
                          @php($href = isset($child['anchor']) ? route($child['route']).$child['anchor'] : route($child['route']))
                          <x-dropdown-link :href="$href">
                            {{ $child['label'] }}
                          </x-dropdown-link>
                        @endif
                      @endforeach
                    </x-slot>
                  </x-dropdown>
                @else
                  <a href="{{ route($t['route']) }}"
                     class="px-3 py-2 rounded-full {{ $t['active'] ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-gray-600 hover:text-gray-900 hover:bg-indigo-50' }}"
                     @if($t['active']) aria-current="page" @endif>
                    {{ $t['label'] }}
                  </a>
                @endif
              @endif
            @endforeach
          </nav>
          <div class="hidden md:flex items-center gap-3 text-sm">
            @if($user)
              <div class="text-right">
                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="text-gray-500">{{ $user->email }}</p>
              </div>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="inline-flex items-center gap-2 rounded-full bg-gray-900 px-4 py-2 text-white hover:bg-gray-800 transition">
                  Keluar
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-4 0v-1m0-8V7a2 2 0 114 0v1"></path></svg>
                </button>
              </form>
            @endif
          </div>
          <button @click="open=!open" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100" aria-label="Toggle navigation">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
          </button>
        </div>
        <div x-show="open" x-transition class="lg:hidden border-t border-slate-200 bg-white">
          <nav class="px-4 py-3 flex flex-col gap-2 text-sm">
            @foreach($tabs as $t)
              @if(Route::has($t['route']))
                @if(isset($t['children']))
                  <div class="rounded-lg border border-slate-200">
                    <div class="px-3 py-2 font-semibold text-gray-800 bg-slate-50 rounded-t-lg">{{ $t['label'] }}</div>
                    <div class="py-1">
                      @foreach($t['children'] as $child)
                        @if(Route::has($child['route']))
                          @php($href = isset($child['anchor']) ? route($child['route']).$child['anchor'] : route($child['route']))
                          <a href="{{ $href }}"
                             class="block px-3 py-1.5 {{ request()->routeIs($child['route']) ? 'text-indigo-700 font-semibold' : 'text-gray-700' }}">
                            {{ $child['label'] }}
                          </a>
                        @endif
                      @endforeach
                    </div>
                  </div>
                @else
                  <a href="{{ route($t['route']) }}"
                     class="px-3 py-1.5 rounded-full {{ $t['active'] ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-gray-800' }}"
                     @if($t['active']) aria-current="page" @endif>
                    {{ $t['label'] }}
                  </a>
                @endif
              @endif
            @endforeach
          </nav>
          <div class="px-4 pb-3 flex items-center justify-between text-sm">
            @if($user)
              <div>
                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="text-gray-500">{{ $user->email }}</p>
              </div>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-3 py-1.5 rounded-full bg-gray-900 text-white">Logout</button>
              </form>
            @endif
          </div>
        </div>
      </header>

      <section class="px-4 pt-6">
        <div class="max-w-7xl mx-auto space-y-3">
          <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
              <p class="text-xs uppercase tracking-[0.25em] text-indigo-500">Area Anggota</p>
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
              <p class="text-sm text-gray-600 max-w-3xl">{{ $brandTagline }}</p>
              @if($roleNames->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                  @foreach($roleNames as $role)
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-gray-800 uppercase tracking-wide">{{ $role }}</span>
                  @endforeach
                </div>
              @endif
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
              @if(Route::has('anggota.kegiatan.index'))
                <a href="{{ route('anggota.kegiatan.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white font-semibold shadow hover:bg-indigo-500 transition">Kegiatan</a>
              @endif
              @if(Route::has('anggota.iuran.tagihan.index'))
                <a href="{{ route('anggota.iuran.tagihan.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-white font-semibold hover:bg-slate-800 transition">Iuran</a>
              @endif
              @if(Route::has('anggota.pengumuman.index'))
                <a href="{{ route('anggota.pengumuman.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-gray-800 font-semibold border border-slate-200 hover:border-indigo-300 hover:text-indigo-700 transition">Pengumuman</a>
              @endif
            </div>
          </div>

        </div>
      </section>

      <main class="flex-1 px-4 pb-14 pt-4">
        <div class="max-w-7xl mx-auto">
          <div class="mt-8">
            {{ $slot }}
          </div>
        </div>
      </main>

      <footer class="bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 py-6 text-xs text-gray-500 flex flex-wrap items-center justify-between gap-3">
          <span>© {{ date('Y') }} {{ $brand }} — Area Anggota</span>
          <span class="text-gray-400">Dibuat konsisten dengan tampilan Home</span>
        </div>
      </footer>
    </div>
  </body>
</html>
