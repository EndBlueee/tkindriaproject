@extends('layouts.admin')

@section('title', 'Rincian Biaya')

@section('main')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Rincian Biaya</h1>
        <p class="text-sm text-gray-500 mt-1">
            @if ($tahunAjaran)
                Tahun Ajaran {{ $tahunAjaran->nama }}
            @else
                Belum ada tahun ajaran aktif
            @endif
        </p>
    </div>
    @if ($tahunAjaran)
    <a href="{{ route('admin.rincian-biaya.create') }}"
        class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-sm transition">
        + Tambah Rincian
    </a>
    @endif
</div>

{{-- Tabel --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#0068C4] border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 text-white font-semibold">No</th>
                <th class="text-left px-4 py-3 text-white font-semibold">Nama Komponen</th>
                <th class="text-left px-4 py-3 text-white font-semibold">Nominal</th>
                <th class="text-left px-4 py-3 text-white font-semibold">Keterangan</th>
                <th class="text-center px-4 py-3 text-white font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($rincians as $r)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-500">{{ $r->urutan }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $r->nama_komponen }}</td>
                <td class="px-4 py-3 font-semibold text-gray-800">{{ $r->nominal_format }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $r->keterangan ?? '-' }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('admin.rincian-biaya.edit', $r) }}"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </a>
                        <form method="POST" action="{{ route('admin.rincian-biaya.destroy', $r) }}"
                            onsubmit="return confirm('Hapus {{ $r->nama_komponen }}?')">
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
                <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                    Belum ada rincian biaya.
                    @if ($tahunAjaran)
                    <a href="{{ route('admin.rincian-biaya.create') }}" class="text-[#0068C4] hover:underline">Tambah sekarang</a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>

        {{-- Total --}}
        @if ($rincians->count() > 0)
        <tfoot class="bg-gray-50 border-t border-gray-200">
            <tr>
                <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">Total</td>
                <td class="px-4 py-3 text-sm font-bold text-gray-800">
                    Rp {{ number_format($rincians->sum('nominal'), 0, ',', '.') }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif

    </table>
</div>

@endsection