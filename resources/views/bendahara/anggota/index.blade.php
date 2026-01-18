<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Anggota</h2></x-slot>
  <div class="p-6 space-y-6">
    <div class="max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="text-xs uppercase text-gray-500">Total Anggota</div>
      <div class="mt-2 text-4xl font-bold text-gray-900">{{ number_format($totalAnggota) }}</div>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full divide-y text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left">Nama</th>
            <th class="px-4 py-2 text-left">Email</th>
            <th class="px-4 py-2 text-left">HP</th>
            <th class="px-4 py-2 text-left">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($users as $u)
            <tr>
              <td class="px-4 py-2">
                <div class="font-medium text-gray-900">{{ $u->name }}</div>
                <div class="text-xs text-gray-500">{{ $u->profile->nama_lengkap ?? '-' }}</div>
              </td>
              <td class="px-4 py-2">{{ $u->email }}</td>
              <td class="px-4 py-2">{{ $u->profile->phone ?? '-' }}</td>
              <td class="px-4 py-2">
                <span class="px-2 py-1 rounded text-xs {{ ($u->profile->is_active ?? false) ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                  {{ ($u->profile->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($users, 'links'))
      <div>{{ $users->links() }}</div>
    @endif
  </div>
</x-app-layout>
