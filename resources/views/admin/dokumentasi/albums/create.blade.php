<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Buat Album</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.dokumentasi.albums._form')
    </div>
</x-app-layout>
