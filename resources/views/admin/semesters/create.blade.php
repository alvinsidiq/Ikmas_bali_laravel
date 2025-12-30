<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Tambah Periode Pengajaran</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.semesters._form')
    </div>
</x-app-layout>
