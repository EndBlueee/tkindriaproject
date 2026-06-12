@extends('layouts.admin')

@section('title', 'Calon Murid')

@section('main')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Calon Murid</h1>
        <p class="text-sm text-gray-500 mt-1">Tahun Ajaran {{ $tahunAjaran?->nama ?? '-' }}</p>
    </div>
</div>

{{-- Notifikasi --}}
@if (session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-sm text-sm text-green-700">
    {{ session('success') }}
</div>
@endif
@if ($errors->any())
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-sm text-sm text-red-700">
    {{ $errors->first() }}
</div>
@endif

{{-- Filter --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.calon-murid.index') }}" class="flex flex-wrap gap-3">

        <input type="text" name="cari" value="{{ request('cari') }}"
            placeholder="Cari nama anak..."
            class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">

        <select name="status" class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="menunggu_nis" {{ request('status') === 'menunggu_nis' ? 'selected' : '' }}>
                Menunggu NIS
            </option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>
                Aktif
            </option>
        </select>

        <button type="submit"
            class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-sm transition">
            Cari
        </button>

        @if (request('cari') || request('status'))
        <a href="{{ route('admin.calon-murid.index') }}"
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
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Nama Anak</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Orang Tua</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Gelombang</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">NIS</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Status</th>
                <th class="text-right px-4 py-3 text-white text-xs font-semibold uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($calonMurids as $cm)
            @php
                $pendaftaran = $cm->pendaftaran->first();
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800 capitalize">{{ $cm->nama_lengkap }}</p>
                    <p class="text-xs text-gray-400">{{ $cm->jenis_kelamin_lengkap }} · {{ $cm->usia }}</p>
                </td>
                <td class="px-4 py-3">
                    <p class="text-gray-700 capitalize">{{ $cm->orangTua->nama_penanggung_jawab ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $cm->orangTua->nomor_hp ?? '-' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $pendaftaran?->gelombang?->nama ?? '-' }}
                </td>
                <td class="px-4 py-3">
                    @if ($cm->nis)
                        <span class="font-mono font-semibold text-gray-800">{{ $cm->nis }}</span>
                    @else
                        <span class="text-gray-400 text-sm">Belum ada</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $pendaftaran?->status === 'menunggu_nis' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $pendaftaran?->status === 'aktif' ? 'bg-green-100 text-green-700' : '' }}">
                        {{ $pendaftaran?->status_label ?? '-' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    @if ($pendaftaran?->status === 'menunggu_nis')
                    {{-- Form Input NIS --}}
                    <form method="POST" action="{{ route('admin.calon-murid.input-nis', $cm) }}"
                        class="flex items-center justify-end gap-2">
                        @csrf
                        <input type="text" name="nis"
                            placeholder="Masukkan NIS"
                            class="border border-gray-300 rounded-sm px-3 py-1.5 text-sm w-32 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit"
                            class="bg-[#0068C4] hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-sm transition">
                            Simpan
                        </button>
                    </form>
                    @else
                    <span class="text-xs text-gray-400">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
                    Tidak ada data calon murid
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if ($calonMurids->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $calonMurids->withQueryString()->links() }}
    </div>
    @endif

</div>

@endsection