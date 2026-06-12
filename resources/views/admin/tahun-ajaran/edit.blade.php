@extends('layouts.admin')

@section('title', 'Edit Tahun Ajaran')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.tahun-ajaran.index') }}" class="hover:text-[#0068C4] transition">Tahun Ajaran</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Edit</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Tahun Ajaran</h1>
    <p class="text-sm text-gray-500 mt-1">
        Perbarui pengaturan tahun ajaran
        <span class="font-medium text-gray-700">{{ $tahunAjaran->nama }}</span>
    </p>
</div>

<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('admin.tahun-ajaran.update', $tahunAjaran) }}">
        @csrf
        @method('PUT')

        {{-- Nama (read only) --}}
        <div class="mb-5 flex items-center">
            <label class="w-1/3 text-sm font-semibold text-gray-700">
                Nama Tahun Ajaran
            </label>
            <div class="w-full">
                <input type="text" value="{{ $tahunAjaran->nama }}" disabled
                class="w-full border border-gray-200 bg-gray-50 text-gray-400 rounded-sm px-4 py-2.5 text-sm cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">Nama tidak dapat diubah</p>
            </div>
        </div>

        {{-- Batas Akhir Pembayaran --}}
        <div class="mb-5 flex items-center">
            <label for="batas_akhir_pembayaran" class="w-1/3 text-sm font-semibold text-gray-700 ">
                Batas Akhir Pembayaran <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="date" name="batas_akhir_pembayaran" id="batas_akhir_pembayaran"
                value="{{ old('batas_akhir_pembayaran', $tahunAjaran->batas_akhir_pembayaran->format('Y-m-d')) }}"
                class="w-full border {{ $errors->has('batas_akhir_pembayaran') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('batas_akhir_pembayaran')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Maks Termin --}}
        <div class="mb-6 flex items-center">
            <label for="maks_termin" class="w-1/3 text-sm font-semibold text-gray-700">
                Maksimal Cicilan <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="number" name="maks_termin" id="maks_termin"
                    value="{{ old('maks_termin', $tahunAjaran->maks_termin) }}"
                    min="1" max="4" placeholder="Contoh: 3"
                    class="w-full border {{ $errors->has('maks_termin') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <p class="text-xs text-gray-400 mt-1">Minimal 1 (lunas saja), maksimal 4x cicilan.</p>
                @error('maks_termin')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.tahun-ajaran.index') }}"
                class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-sm bg-gray-100 hover:bg-gray-200 transition">
                Kembali
            </a>
            <button type="submit"
                class="bg-[#0068C4] hover:bg-[#005ca3] text-white text-sm font-semibold px-6 py-2.5 rounded-sm transition">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

@endsection