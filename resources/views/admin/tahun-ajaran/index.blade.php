@extends('layouts.admin')

@section('title', 'Tahun Ajaran')

@section('main')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tahun Ajaran</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola periode penerimaan murid baru</p>
    </div>
    <a href="{{ route('admin.tahun-ajaran.create') }}" class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-sm transition">+ Tambah Tahun Ajaran</a>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#0068C4] border-b border-gray-100">
            <tr>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Tahun Ajaran</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Status</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Maks. Cicilan</th>
                <th class="text-left px-4 py-3 text-white text-xs font-semibold uppercase">Gelombang</th>
                <th class="text-center px-4 py-3 text-white text-xs font-semibold uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($tahunAjarans as $ta)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-semibold text-gray-800">{{ $ta->nama }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $ta->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $ta->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $ta->maks_termin }}x</td>
                <td class="px-4 py-3 text-gray-600">{{ $ta->gelombang->count() }} gelombang</td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-2">

                        {{-- Edit --}}
                        <a href="{{ route('admin.tahun-ajaran.edit', $ta) }}"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </a>

                        {{-- Aktifkan (jika nonaktif) --}}
                        @if ($ta->status === 'nonaktif')
                        <form method="POST" action="{{ route('admin.tahun-ajaran.aktifkan', $ta) }}">
                            @csrf
                            <button type="submit"
                                class="text-xs text-green-600 hover:text-green-800 font-medium px-2 py-1 rounded hover:bg-green-50 transition">
                                Aktifkan
                            </button>
                        </form>

                        {{-- Hapus (hanya jika nonaktif) --}}
                        <form method="POST" action="{{ route('admin.tahun-ajaran.destroy', $ta) }}"
                            onsubmit="return confirm('Hapus tahun ajaran {{ $ta->nama }}? Semua data gelombang dan rincian biaya akan ikut terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded hover:bg-red-50 transition">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </form>
                        @endif

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm">
                    Belum ada tahun ajaran.
                    <a href="{{ route('admin.tahun-ajaran.create') }}" class="text-[#0068C4] hover:underline">Tambah sekarang</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection