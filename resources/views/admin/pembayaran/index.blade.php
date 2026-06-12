@extends('layouts.admin')

@section('title', 'Pembayaran')

@section('main')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pembayaran</h1>
        <p class="text-sm text-gray-500 mt-1">Tahun Ajaran {{ $tahunAjaran?->nama ?? '-' }}</p>
    </div>
    <a href="{{ route('admin.pembayaran.create') }}"
        class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-sm transition">
        + Tambah Pembayaran
    </a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.pembayaran.index') }}" class="flex flex-wrap gap-3">

        <input type="text" name="cari" value="{{ request('cari') }}"
            placeholder="Cari nama anak atau no. daftar..."
            class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">

        <select name="status" class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Belum Dibayar</option>
            <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="kedaluwarsa" {{ request('status') === 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
        </select>

        <select name="metode" class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Metode</option>
            <option value="bca_va" {{ request('metode') === 'bca_va' ? 'selected' : '' }}>BCA VA</option>
            <option value="bni_va" {{ request('metode') === 'bni_va' ? 'selected' : '' }}>BNI VA</option>
            <option value="bri_va" {{ request('metode') === 'bri_va' ? 'selected' : '' }}>BRI VA</option>
            <option value="mandiri_va" {{ request('metode') === 'mandiri_va' ? 'selected' : '' }}>Mandiri VA</option>
            <option value="permata_va" {{ request('metode') === 'permata_va' ? 'selected' : '' }}>Permata VA</option>
            <option value="alfamart" {{ request('metode') === 'alfamart' ? 'selected' : '' }}>Alfamart</option>
            <option value="indomaret" {{ request('metode') === 'indomaret' ? 'selected' : '' }}>Indomaret</option>
            <option value="tunai" {{ request('metode') === 'tunai' ? 'selected' : '' }}>Tunai</option>
            <option value="transfer" {{ request('metode') === 'transfer' ? 'selected' : '' }}>Transfer</option>
        </select>

        <button type="submit"
            class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-sm transition">
            Cari
        </button>

        @if (request('cari') || request('status') || request('metode'))
        <a href="{{ route('admin.pembayaran.index') }}"
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
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Jalur</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Total Biaya</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Termin</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Metode</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Status</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($pendaftarans as $p)
            @php
            $totalTermin = $p->pembayaran->first()?->total_termin ?? 1;
            $terminLunas = $p->pembayaran->where('status', 'lunas')->count();
            $activePembayaran = $p->pembayaran->firstWhere('status', 'menunggu')
                ?? $p->pembayaran->firstWhere('status', 'kedaluwarsa')
                ?? $p->pembayaran->last();
            $statusBayar = $activePembayaran?->status ?? 'menunggu';
            @endphp
            <tr class="hover:bg-gray-50 transition align-middle">
                <td class="px-4 py-3 font-mono text-xs text-gray-600">
                    {{ $p->nomor_daftar }}
                </td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800 capitalize">{{ $p->calonMurid->nama_lengkap }}</p>
                    
                </td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->jalur === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                        {{ ucfirst($p->jalur) }}
                    </span>
                </td>
                <td class="px-4 py-3 font-semibold text-gray-800">
                    {{ $p->gelombang?->total_biaya_format ?? '-' }}
                </td>
                <td class="px-4 py-3 text-gray-600 text-sm">
                    {{ $terminLunas }}/{{ $totalTermin }}
                </td>
                <td class="px-4 py-3">
                    @php $metode = $p->pembayaran->last()?->metode; @endphp
                    @if ($metode && !in_array($metode, ['tunai', 'transfer']))
                    <img src="{{ asset('images/bank/' . str_replace('_va', '', $metode) . '.png') }}"
                        alt="{{ $p->pembayaran->last()?->metode_label }}"
                        class="h-3.5 object-contain">
                    @else
                    <span class="text-sm text-gray-600">{{ $p->pembayaran->last()?->metode_label ?? '-' }}</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $statusBayar === 'menunggu' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $statusBayar === 'lunas' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $statusBayar === 'kedaluwarsa' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ $activePembayaran?->status_label ?? '-' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-left">
                    <a href="{{ route('admin.pembayaran.show', $p) }}"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">
                    Tidak ada data pembayaran
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