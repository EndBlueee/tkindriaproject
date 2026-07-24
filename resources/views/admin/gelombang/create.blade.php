@extends('layouts.admin')

@section('title', 'Tambah Gelombang')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.gelombang.index') }}" class="hover:text-[#0068C4] transition">Gelombang</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Tambah</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Gelombang</h1>
    <p class="text-sm text-gray-500 mt-1">Total biaya dihitung otomatis dari rincian biaya dikurangi diskon</p>
</div>

<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('admin.gelombang.store') }}">
        @csrf

        {{-- Tahun Ajaran --}}
        <div class="mb-5 flex items-center">
            <label for="tahun_ajaran_id" class="w-1/3 text-sm font-semibold text-gray-700">
                Tahun Ajaran <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <select name="tahun_ajaran_id" id="tahun_ajaran_id"
                    class="w-full border {{ $errors->has('tahun_ajaran_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}"
                        {{ old('tahun_ajaran_id', $ta->status === 'aktif' ? $ta->id : '') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama }} {{ $ta->status === 'aktif' ? '(Aktif)' : '' }}
                    </option>
                    @endforeach
                </select>
                @error('tahun_ajaran_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Nama --}}
        <div class="mb-5 flex items-center">
            <label for="nama" class="w-1/3 text-sm font-semibold text-gray-700">
                Nama Gelombang <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="text" name="nama" id="nama"
                value="{{ old('nama') }}"
                placeholder="Contoh: Gelombang 1"
                class="w-full border {{ $errors->has('nama') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nama')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Tanggal Buka & Tutup --}}
        
            <div class="mb-5 flex items-center">
                <label for="tanggal_buka" class="w-1/3 text-sm font-semibold text-gray-700">
                    Tanggal Buka <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <input type="date" name="tanggal_buka" id="tanggal_buka"
                    value="{{ old('tanggal_buka') }}"
                    class="w-full border {{ $errors->has('tanggal_buka') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('tanggal_buka')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mb-5 flex items-center">
                <label for="tanggal_tutup" class="w-1/3 text-sm font-semibold text-gray-700">
                    Tanggal Tutup <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <input type="date" name="tanggal_tutup" id="tanggal_tutup"
                        value="{{ old('tanggal_tutup') }}"
                        class="w-full border {{ $errors->has('tanggal_tutup') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('tanggal_tutup')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        {{-- Batas Pembayaran --}}
        <div class="mb-5 flex items-center">
            <label for="batas_pembayaran" class="w-1/3 text-sm font-semibold text-gray-700">
                Batas Pembayaran <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="date" name="batas_pembayaran" id="batas_pembayaran"
                    value="{{ old('batas_pembayaran') }}"
                    class="w-full border {{ $errors->has('batas_pembayaran') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <p class="text-xs text-gray-400 mt-1">Batas akhir pembayaran untuk pendaftar di gelombang ini.</p>
                @error('batas_pembayaran')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Diskon --}}
        <div class="mb-6 flex items-center">
            <label for="diskon_persen" class="w-1/3 text-sm font-semibold text-gray-700">
                Diskon <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <div class="relative">
                    <input type="number" name="diskon_persen" id="diskon_persen"
                    value="{{ old('diskon_persen', 0) }}"
                    min="0" max="100" placeholder="0"
                    class="w-full border {{ $errors->has('diskon_persen') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">%</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Isi 0 jika tidak ada diskon. Contoh: 10 = diskon 10%</p>
                @error('diskon_persen')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.gelombang.index') }}"
                class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-sm bg-gray-100 hover:bg-gray-200 transition">
                Kembali
            </a>
            <button type="submit"
                class="bg-[#0068C4] hover:bg-[#005ca3] text-white text-sm font-semibold px-6 py-2.5 rounded-sm transition">
                Simpan
            </button>
        </div>

    </form>
</div>

@endsection