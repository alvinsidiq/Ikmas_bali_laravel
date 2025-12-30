<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Kegiatan</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.kegiatan._form', ['kegiatan' => $kegiatan])
    </div>
</x-app-layout>
