<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Tambah Arsip</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.arsip._form')
    </div>
</x-app-layout>
