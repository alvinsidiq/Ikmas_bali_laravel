<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Pengumuman</h2>
    </x-slot>
    <div class="p-6 bg-white shadow rounded">
        @include('admin.pengumuman._form', ['pengumuman' => $pengumuman])
    </div>
</x-app-layout>
