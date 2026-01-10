<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Tambah Pengumuman</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.pengumuman._form', ['categories' => $categories])
    </div>
</x-app-layout>
