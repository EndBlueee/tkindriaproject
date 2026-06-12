@extends('layouts.admin')

@section('title', 'Edit User')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.user.index') }}" class="hover:text-[#0068C4] transition">Manajemen User</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Edit User</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
    <p class="text-sm text-gray-500 mt-1">Perbarui data akun <span class="font-medium text-gray-700">{{ $user->nama }}</span></p>
</div>

<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6 w-full">
    <form method="POST" action="{{ route('admin.user.update', $user) }}">
        @csrf
        @method('PUT')

        {{-- Nama --}}
        <div class="mb-5 flex items-center">
            <label for="nama" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                Nama <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="text" name="nama" id="nama"
                value="{{ old('nama', $user->nama) }}"
                placeholder="Nama lengkap"
                class="w-full border {{ $errors->has('nama') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nama')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Nomor HP --}}
        <div class="mb-5 flex items-center">
            <label for="nomor_hp" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                Nomor HP <span class="text-red-500">*</span>
            </label>
            <div class="w-full">
                <input type="text" name="nomor_hp" id="nomor_hp"
                    value="{{ old('nomor_hp', $user->nomor_hp) }}"
                    placeholder="08xxxxxxxxxx"
                    class="w-full border {{ $errors->has('nomor_hp') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nomor_hp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-6 flex items-center">
            <label for="password" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                Password Baru
               
            </label>
            <div class="w-full">
                <input type="password" name="password" id="password"
                    placeholder="Minimal 6 karakter"
                    class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <span class="text-gray-400 font-normal text-xs">(kosongkan jika tidak ingin mengubah)</span>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Info Role (tidak bisa diubah) --}}
        <div class="mb-6 p-3 bg-gray-50 rounded-sm border border-gray-200">
            <p class="text-xs text-gray-500">
                Role: <span class="font-semibold text-gray-700">{{ $user->role === 'admin' ? 'Admin' : 'Orang Tua' }}</span>
                — Role tidak dapat diubah setelah akun dibuat
            </p>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.user.index') }}"
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