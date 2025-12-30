<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Anggota</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.anggota._form', ['anggota' => $anggota])
    </div>
</x-app-layout>
