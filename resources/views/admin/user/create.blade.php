@extends('layouts.admin')

@section('title', 'Tambah User')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.user.index') }}" class="hover:text-[#0068C4] transition">Manajemen User</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Tambah User</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah User</h1>
    <p class="text-sm text-gray-500 mt-1">Buat akun baru untuk admin atau orang tua pendaftar</p>
</div>

<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6 w-full">
    <form method="POST" action="{{ route('admin.user.store') }}">
        @csrf

        {{-- Nama --}}
        <div class="mb-5 flex items-center">
            <label for="nama" class="w-1/3 text-sm font-semibold text-gray-700">
                Nama <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="text" name="nama" id="nama"
                    value="{{ old('nama') }}"
                    placeholder="Nama lengkap"
                    class="w-full border {{ $errors->has('nama') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nama')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Nomor HP --}}
        <div class="mb-5 flex items-center">
            <label for="nomor_hp" class="w-1/3 text-sm font-semibold text-gray-700">
                Nomor HP <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="text" name="nomor_hp" id="nomor_hp"
                    value="{{ old('nomor_hp') }}"
                    placeholder="08xxxxxxxxxx"
                    class="w-full border {{ $errors->has('nomor_hp') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nomor_hp')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-5 flex items-center">
            <label for="password" class="w-1/3 text-sm font-semibold text-gray-700">
                Password <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="password" name="password" id="password"
                    placeholder="Minimal 6 karakter"
                    class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Role --}}
        <div class="mb-6 flex items-center">
            <label for="role" class="w-1/3 text-sm font-semibold text-gray-700">
                Role <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <select name="role" id="role"
                    class="w-full border {{ $errors->has('role') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="orangtua" {{ old('role') === 'orangtua' ? 'selected' : '' }}>Orang Tua</option>
                </select>
                @error('role')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.user.index') }}"
                class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-sm bg-gray-100 hover:bg-gray-200 transition">
                Kembali
            </a>
            <button type="submit"
                class="bg-[#0068C4] hover:bg-[#005ca3] text-white text-sm font-semibold px-6 py-2.5 rounded-sm transition">
                Simpan User
            </button>
        </div>

    </form>
</div>

@endsection