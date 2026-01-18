<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        @php
            $user = Auth::user();
            $useSidebar = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin','bendahara']);
        @endphp
        <div class="min-h-screen bg-gray-100">
            @if($useSidebar)
                <div class="flex min-h-screen">
                    @include('layouts.sidebar', ['user' => $user])

                    <div class="flex-1 flex flex-col min-h-screen bg-gray-50">
                        <div class="bg-white border-b px-4 py-3 flex items-center justify-between lg:hidden">
                            <div class="flex items-center gap-2">
                                <button @click="sidebarOpen = true" class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <x-application-logo class="h-7 w-7 rounded-full" />
                            </div>
                            <div class="flex items-center gap-3">
                                <x-application-logo class="h-7 w-7 rounded-full" />
                                <div class="text-sm text-gray-700">{{ $user->name }}</div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="text-sm text-red-600 hover:underline">Logout</button>
                                </form>
                            </div>
                        </div>

                        <!-- Page Heading -->
                        @isset($header)
                            <header class="bg-white shadow-sm">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <!-- Page Content -->
                        <main class="flex-1">
                            {{ $slot }}
                        </main>
                    </div>
                </div>
            @else
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            @endif
        </div>
    </body>
</html>
