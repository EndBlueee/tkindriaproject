@extends('layouts.admin')

@section('title', 'Pendaftaran')

@section('main')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pendaftaran</h1>
        <p class="text-sm text-gray-500 mt-1">
            Tahun Ajaran {{ $tahunAjaran?->nama ?? '-' }}
        </p>
    </div>
    <a href="{{ route('admin.pendaftaran.create') }}"
        class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-sm transition">
        + Tambah Pendaftaran
    </a>
</div>

{{-- Filter & Pencarian --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.pendaftaran.index') }}" class="flex flex-wrap gap-3">

        <input type="text" name="cari" value="{{ request('cari') }}"
            placeholder="Cari nama anak atau no. daftar..."
            class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">

        <select name="status" class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="perlu_revisi" {{ request('status') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
            <option value="menunggu_pembayaran" {{ request('status') === 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
            <option value="cicilan_berjalan" {{ request('status') === 'cicilan_berjalan' ? 'selected' : '' }}>Cicilan Berjalan</option>
            <option value="menunggu_nis" {{ request('status') === 'menunggu_nis' ? 'selected' : '' }}>Menunggu NIS</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
        </select>

        <select name="jalur" class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Jalur</option>
            <option value="online" {{ request('jalur') === 'online' ? 'selected' : '' }}>Online</option>
            <option value="offline" {{ request('jalur') === 'offline' ? 'selected' : '' }}>Offline</option>
        </select>

        <button type="submit"
            class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-sm transition">
            Cari
        </button>

        @if(request('cari') || request('status') || request('jalur'))
        <a href="{{ route('admin.pendaftaran.index') }}"
            class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-sm hover:bg-gray-100 transition">
            Reset
        </a>
        @endif

    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#0068C4] border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">No. Daftar</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Nama Anak</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Gelombang</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Jalur</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Status</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Tgl Submit</th>
                <th class="text-center px-4 py-3 text-white text-xs font-semibold uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($pendaftarans as $p)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-mono text-xs text-gray-600">
                    {{ $p->nomor_daftar }}
                </td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800 capitalize">{{ $p->calonMurid->nama_lengkap }}</p>
                
                </td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $p->gelombang?->nama ?? '-' }}
                </td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                        {{ $p->jalur === 'online' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($p->jalur) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->status === 'menunggu_verifikasi' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $p->status === 'perlu_revisi' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $p->status === 'menunggu_pembayaran' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $p->status === 'cicilan_berjalan' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $p->status === 'menunggu_nis' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $p->status === 'aktif' ? 'bg-green-100 text-green-700' : '' }}">
                        {{ $p->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ $p->tgl_submit?->translatedFormat('d M Y') ?? '-' }}
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.pendaftaran.show', $p) }}"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                            <x-heroicon-o-eye class="w-5 h-5" />
                        </a>
                        <a href="{{ route('admin.pendaftaran.edit', $p) }}"
                            class="text-xs text-gray-600 hover:text-gray-800 font-medium px-2 py-1 rounded hover:bg-gray-100 transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">
                    Tidak ada data pendaftaran
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if ($pendaftarans->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $pendaftarans->withQueryString()->links() }}
    </div>
    @endif

</div>

@endsection