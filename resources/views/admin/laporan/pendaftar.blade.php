@extends('layouts.admin')

@section('title', 'Laporan Pendaftar')

@section('main')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Pendaftaran</h1>
        <p class="text-sm text-gray-500 mt-1">Tahun Ajaran {{ $tahunAjaran?->nama ?? '-' }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.laporan.pendaftar') }}" class="flex flex-wrap gap-3 items-end">

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Gelombang</label>
            <select name="gelombang_id"
                class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Gelombang</option>
                @foreach ($gelombangs as $g)
                <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>
                    {{ $g->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
            <select name="status"
                class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="perlu_revisi" {{ request('status') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="menunggu_pembayaran" {{ request('status') === 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="cicilan_berjalan" {{ request('status') === 'cicilan_berjalan' ? 'selected' : '' }}>Cicilan Berjalan</option>
                <option value="menunggu_nis" {{ request('status') === 'menunggu_nis' ? 'selected' : '' }}>Menunggu NIS</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            </select>
        </div>

        <button type="submit"
            class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-sm transition">
            Tampilkan
        </button>

        @if (request('gelombang_id') || request('status'))
        <a href="{{ route('admin.laporan.pendaftar') }}"
            class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-sm hover:bg-gray-100 transition">
            Reset
        </a>
        @endif

        {{-- Tombol Export PDF --}}
        @if ($pendaftarans->count() > 0)
        <a href="{{ route('admin.laporan.pendaftar.pdf', request()->query()) }}"
            class="ml-auto bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-sm transition">
            ⬇ Export PDF
        </a>
        @endif

    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-600">
            Total: <span class="font-semibold">{{ $pendaftarans->count() }}</span> pendaftar
        </p>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-[#0068C4] border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-4 text-white font-semibold">No</th>
                <th class="text-left px-4 py-4 text-white font-semibold">No. Daftar</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Nama Anak</th>
                <th class="text-left px-4 py-4 text-white font-semibold">L/P</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Nama Orang Tua</th>
                <th class="text-left px-4 py-4 text-white font-semibold">No. HP</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Gelombang</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Status</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Tgl Daftar</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($pendaftarans as $i => $p)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $p->nomor_daftar }}</td>
                <td class="px-4 py-3 font-medium text-gray-800 capitalize">{{ $p->calonMurid->nama_lengkap }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $p->calonMurid->jenis_kelamin }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $p->calonMurid->orangTua->nama_penanggung_jawab ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $p->calonMurid->orangTua->nomor_hp ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $p->gelombang?->nama ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->status === 'aktif' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $p->status === 'menunggu_verifikasi' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $p->status === 'perlu_revisi' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $p->status === 'menunggu_pembayaran' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $p->status === 'cicilan_berjalan' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $p->status === 'menunggu_nis' ? 'bg-purple-100 text-purple-700' : '' }}">
                        {{ $p->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ $p->created_at->translatedFormat('d M Y') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-gray-400 text-sm">
                    Tidak ada data pendaftar
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection