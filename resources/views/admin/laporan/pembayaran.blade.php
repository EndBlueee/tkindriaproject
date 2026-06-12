@extends('layouts.admin')

@section('title', 'Laporan Pembayaran')

@section('main')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Pembayaran</h1>
        <p class="text-sm text-gray-500 mt-1">Tahun Ajaran {{ $tahunAjaran?->nama ?? '-' }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('admin.laporan.pembayaran') }}" class="flex flex-wrap gap-3 items-end">

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
                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Belum Dibayar</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="kedaluwarsa" {{ request('status') === 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
            </select>
        </div>

        <button type="submit"
            class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-sm transition">
            Tampilkan
        </button>

        @if (request('gelombang_id') || request('status'))
        <a href="{{ route('admin.laporan.pembayaran') }}"
            class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-sm hover:bg-gray-100 transition">
            Reset
        </a>
        @endif

        {{-- Tombol Export PDF --}}
        @if ($pembayarans->count() > 0)
        <a href="{{ route('admin.laporan.pembayaran.pdf', request()->query()) }}"
            class="ml-auto bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-sm transition">
            ⬇ Export PDF
        </a>
        @endif

    </form>
</div>

{{-- Ringkasan --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 mb-4">
    <div class="flex items-center gap-8 text-sm">
        <div>
            <p class="text-gray-400 text-xs mb-0.5">Total Data</p>
            <p class="font-bold text-gray-800">{{ $pembayarans->count() }} transaksi</p>
        </div>
        <div>
            <p class="text-gray-400 text-xs mb-0.5">Total Terkumpul</p>
            <p class="font-bold text-green-600">Rp {{ number_format($totalLunas, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#0068C4] border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-4 text-white font-semibold">No</th>
                <th class="text-left px-4 py-4 text-white font-semibold">No. Daftar</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Nama Anak</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Termin</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Metode</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Nominal</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Status</th>
                <th class="text-left px-4 py-4 text-white font-semibold">Tgl Bayar</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($pembayarans as $i => $p)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                <td class="px-4 py-3 font-mono text-xs text-gray-600">
                    {{ $p->pendaftaran->nomor_daftar }}
                </td>
                <td class="px-4 py-3 font-medium text-gray-800 capitalize">
                    {{ $p->pendaftaran->calonMurid->nama_lengkap }}
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs">
                    {{ $p->termin_ke }}/{{ $p->total_termin }}
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $p->metode_label }}</td>
                <td class="px-4 py-3 font-semibold text-gray-800">{{ $p->nominal_format }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->status === 'lunas' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $p->status === 'menunggu' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $p->status === 'kedaluwarsa' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ $p->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ $p->tanggal_bayar?->translatedFormat('d M Y') ?? '-' }}
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
</div>

@endsection