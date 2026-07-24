@extends('layouts.admin')

@section('title', 'Gelombang')

@section('main')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Gelombang</h1>
        <p class="text-sm text-gray-500 mt-1">
            @if ($tahunAjaran)
                Tahun Ajaran {{ $tahunAjaran->nama }}
            @else
                Belum ada tahun ajaran aktif
            @endif
        </p>
    </div>
    @if ($tahunAjaran)
    <a href="{{ route('admin.gelombang.create') }}"
        class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-sm transition">
        + Tambah Gelombang
    </a>
    @endif
</div>

{{-- Tabel --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#0068C4] border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase ">Nama</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase ">Tanggal Buka</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase ">Tanggal Tutup</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase ">Batas Pembayaran</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase ">Diskon</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase ">Total Bayar</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase ">Status</th>
                <th class="text-center px-4 py-3 text-white text-xs font-semibold uppercase ">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($gelombang as $g)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $g->nama }}</td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $g->tanggal_buka->translatedFormat('d F Y') }}
                </td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $g->tanggal_tutup->translatedFormat('d F Y') }}
                </td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $g->batas_pembayaran->translatedFormat('d F Y') }}
                </td>
                <td class="px-4 py-3">
                    @if ($g->diskon_persen > 0)
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                            {{ $g->diskon_persen }}% off
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">Tidak ada</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">{{ $g->total_biaya_format }}</p>
                    @if ($g->diskon_persen > 0)
                        <p class="text-xs text-gray-400">Sudah diskon {{ $g->diskon_persen }}%</p>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if ($g->isBuka())
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            Sedang Buka
                        </span>
                    @elseif (today()->lt($g->tanggal_buka))
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            Belum Buka
                        </span>
                    @else
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                            Sudah Tutup
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.gelombang.edit', $g) }}"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </a>
                         <form method="POST" action="{{ route('admin.gelombang.destroy', $g) }}"
                            onsubmit="return confirm('Hapus {{ $g->nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded hover:bg-red-50 transition">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">
                    Belum ada gelombang.
                    @if ($tahunAjaran)
                    <a href="{{ route('admin.gelombang.create') }}" class="text-[#0068C4] hover:underline">Tambah sekarang</a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection