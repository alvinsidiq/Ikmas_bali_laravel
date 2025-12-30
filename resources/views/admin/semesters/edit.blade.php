<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Periode Pengajaran</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.semesters._form', ['semester' => $semester])
    </div>
</x-app-layout>
