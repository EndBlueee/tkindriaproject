@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('main')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola akun admin dan orang tua pendaftar</p>
    </div>
    <a href="{{ route('admin.user.create') }}"
        class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-sm transition">
        + Tambah User
    </a>
</div>

{{-- Filter & Pencarian --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.user.index') }}" class="flex flex-wrap gap-3">

        <input type="text" name="cari" value="{{ request('cari') }}"
            placeholder="Cari nama atau nomor HP..."
            class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">

        <select name="role" class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="orangtua" {{ request('role') === 'orangtua' ? 'selected' : '' }}>Orang Tua</option>
        </select>

        <select name="status" class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <button type="submit"
            class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-sm transition">
            Cari
        </button>

        @if(request('cari') || request('role') || request('status'))
        <a href="{{ route('admin.user.index') }}"
            class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-sm hover:bg-gray-100 transition">
            Reset
        </a>
        @endif

    </form>
</div>

{{-- Tabel User --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#0068C4] border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">No</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Nama</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Nomor HP</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Role</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Status</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Terdaftar</th>
                <th class="text-center px-4 py-3 text-white text-xs font-semibold uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium text-gray-800 capitalize">{{ $user->nama }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $user->nomor_hp }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $user->role === 'admin' ? 'Admin' : 'Orang Tua' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $user->is_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500">
                    {{ $user->created_at->format('d M Y') }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-2">

                        {{-- Edit --}}
                        <a href="{{ route('admin.user.edit', $user) }}"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </a>

                        {{-- Toggle Aktif/Nonaktif --}}
                        @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.user.toggle-aktif', $user) }}">
                            @csrf
                            <button type="submit"
                                class="text-xs font-medium px-2 py-1 rounded transition
                                {{ $user->is_aktif ? 'text-red-600 hover:text-red-800 hover:bg-red-50' : 'text-green-600 hover:text-green-800 hover:bg-green-50' }}">
                                {{ $user->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-gray-300 px-2 py-1">—</span>
                        @endif

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm">
                    Tidak ada user ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if ($users->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif

</div>

@endsection