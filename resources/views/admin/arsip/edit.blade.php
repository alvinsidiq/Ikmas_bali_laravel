<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Arsip</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.arsip._form', ['arsip' => $arsip])
    </div>
</x-app-layout>
