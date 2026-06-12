@extends('layouts.admin')

@section('title', 'Tambah Rincian Biaya')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.rincian-biaya.index') }}" class="hover:text-[#0068C4] transition">Rincian Biaya</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Tambah</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Rincian Biaya</h1>
    <p class="text-sm text-gray-500 mt-1">Tambah komponen biaya pendaftaran</p>
</div>

<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('admin.rincian-biaya.store') }}">
        @csrf

        {{-- Tahun Ajaran --}}
        <div class="mb-5 flex items-center">
            <label for="tahun_ajaran_id" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
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

        {{-- Nama Komponen --}}
        <div class="mb-5 flex items-center">
            <label for="nama_komponen" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                Nama Komponen <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="text" name="nama_komponen" id="nama_komponen"
                    value="{{ old('nama_komponen') }}"
                    placeholder="Contoh: Uang Pangkal"
                    class="w-full border {{ $errors->has('nama_komponen') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nama_komponen')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Nominal --}}
        <div class="mb-5 flex items-center">
            <label for="nominal" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                Nominal <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                    <input type="number" name="nominal" id="nominal"
                        value="{{ old('nominal') }}"
                        placeholder="Contoh: 1500000"
                        class="w-full border {{ $errors->has('nominal') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                @error('nominal')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Keterangan --}}
        <div class="mb-6 flex items-center">
            <label for="keterangan" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                Keterangan <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <div class="w-full">
                <input type="text" name="keterangan" id="keterangan"
                    value="{{ old('keterangan') }}"
                    placeholder="Contoh: Dibayar sekali saat pertama masuk"
                    class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.rincian-biaya.index') }}"
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