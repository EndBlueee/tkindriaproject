@extends('layouts.admin')

@section('title', 'Persyaratan Dokumen')

@section('main')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Persyaratan Dokumen</h1>
        <p class="text-sm text-gray-500 mt-1">
            @if ($tahunAjaran)
                Tahun Ajaran {{ $tahunAjaran->nama }}
            @else
                Belum ada tahun ajaran aktif
            @endif
        </p>
    </div>
    @if ($tahunAjaran)
    <a href="{{ route('admin.persyaratan-dokumen.create') }}"
        class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-sm transition">
        + Tambah Dokumen
    </a>
    @endif
</div>

{{-- Tabel --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#0068C4] border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">No</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Nama Dokumen</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Format</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Maks. Ukuran</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Status</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Keterangan</th>
                <th class="text-center px-4 py-3 text-white text-xs font-semibold uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($persyaratans as $p)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-500">{{ $p->urutan }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $p->nama_dokumen }}</td>
                <td class="px-4 py-3 text-gray-600 uppercase text-xs font-medium">
                    {{ $p->format_file_tampil }}
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $p->maks_ukuran_format }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->is_wajib ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $p->is_wajib ? 'Wajib' : 'Opsional' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $p->keterangan ?? '-' }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.persyaratan-dokumen.edit', $p) }}"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </a>
                        <form method="POST" action="{{ route('admin.persyaratan-dokumen.destroy', $p) }}"
                            onsubmit="return confirm('Hapus {{ $p->nama_dokumen }}?')">
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
                <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">
                    Belum ada persyaratan dokumen.
                    @if ($tahunAjaran)
                    <a href="{{ route('admin.persyaratan-dokumen.create') }}" class="text-[#0068C4] hover:underline">Tambah sekarang</a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection