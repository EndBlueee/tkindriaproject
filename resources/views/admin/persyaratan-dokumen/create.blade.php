@extends('layouts.admin')

@section('title', 'Tambah Persyaratan Dokumen')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.persyaratan-dokumen.index') }}" class="hover:text-[#0068C4] transition">Persyaratan Dokumen</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Tambah</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Persyaratan Dokumen</h1>
    <p class="text-sm text-gray-500 mt-1">Tambah dokumen yang wajib atau opsional diupload pendaftar</p>
</div>

<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('admin.persyaratan-dokumen.store') }}">
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

        {{-- Nama Dokumen --}}
        <div class="mb-5 flex items-center">
            <label for="nama_dokumen" class="w-1/3 text-sm font-semibold text-gray-700">
                Nama Dokumen <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="text" name="nama_dokumen" id="nama_dokumen"
                value="{{ old('nama_dokumen') }}"
                placeholder="Contoh: Akta Lahir"
                class="w-full border {{ $errors->has('nama_dokumen') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nama_dokumen')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Format File --}}
        <div class="mb-5 flex items-center">
            <label for="format_file" class="w-1/3 text-sm font-semibold text-gray-700">
                Format File <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="text" name="format_file" id="format_file"
                    value="{{ old('format_file', 'pdf,jpg,png') }}"
                    placeholder="Contoh: pdf,jpg,png"
                    class="w-full border {{ $errors->has('format_file') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma. Contoh: pdf,jpg,png</p>
                @error('format_file')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Maks Ukuran --}}
        <div class="mb-5 flex items-center">
            <label for="maks_ukuran_kb" class="w-1/3 text-sm font-semibold text-gray-700">
                Maksimal Ukuran File <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <div class="relative">
                    <input type="number" name="maks_ukuran_kb" id="maks_ukuran_kb"
                    value="{{ old('maks_ukuran_kb', 2048) }}"
                    min="100" placeholder="2048"
                    class="w-full border {{ $errors->has('maks_ukuran_kb') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 pr-14 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">KB</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Contoh: 2048 = 2 MB, 1024 = 1 MB</p>
            @error('maks_ukuran_kb')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        </div>

        {{-- Status Wajib --}}
        <div class="mb-6 flex items-center">
            <label class="w-1/3 text-sm font-semibold text-gray-700 ">
                Status <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_wajib" value="1"
                        {{ old('is_wajib', '1') === '1' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Wajib</span>
                    </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_wajib" value="0"
                        {{ old('is_wajib') === '0' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Opsional</span>
                    </label>
                </div>
                @error('is_wajib')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Keterangan --}}
        <div class="mb-6 flex items-center">
            <label for="keterangan" class="w-1/3 text-sm font-semibold text-gray-700">
                Keterangan <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <div class="w-full">
                <input type="text" name="keterangan" id="keterangan"
                value="{{ old('keterangan') }}"
                placeholder="Contoh: Scan atau foto yang jelas"
                class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>
            </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.persyaratan-dokumen.index') }}"
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