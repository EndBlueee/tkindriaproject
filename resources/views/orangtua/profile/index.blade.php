@extends('layouts.orangtua')

@section('title', 'Profil Akun')

@section('main')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Profil Akun</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun Anda</p>
    </div>

    @if (session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-sm text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Info Profil Singkat --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
                <div class="w-24 h-24 bg-[#0068C4] rounded-full flex items-center justify-center text-white text-3xl font-bold mb-4 shadow-inner">
                    {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-gray-800">{{ Auth::user()->nama }}</h2>
                <div class="bg-blue-100 rounded-xl px-3 py-1 mt-1">
                    <p class="text-sm text-blue-500">Orang Tua / Wali</p>
                </div>
                <p class="text-sm text-gray-400 mt-1 mb-3">Bergabung Sejak {{ Auth::user()->created_at->format('d M Y') }}</p>
            </div>
        </div>

        {{-- Kolom Kanan: Form Update Profil --}}
        <div class="md:col-span-2">
            <form action="{{ route('orangtua.profile.update') }}" method="POST" class="bg-white rounded-sm shadow-sm border border-gray-100 p-6 md:p-8">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Informasi Dasar</h2>
                <div class="mb-5">
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" id="nama"
                        value="{{ old('nama', auth()->user()->nama) }}"
                        class="w-full border {{ $errors->has('nama') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        required>
                    @error('nama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="nomor_hp" class="block text-sm font-semibold text-gray-700 mb-1">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomor_hp" id="nomor_hp"
                        value="{{ old('nomor_hp', auth()->user()->nomor_hp) }}"
                        class="w-full border {{ $errors->has('nomor_hp') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        required>
                    @error('nomor_hp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2 mt-8">Ubah Password</h2>
                <p class="text-xs text-gray-500 mb-4">Biarkan kosong jika tidak ingin mengubah password.</p>

                <div class="mb-5">
                    <label for="password_lama" class="block text-sm font-semibold text-gray-700 mb-1">
                        Password Lama
                    </label>
                    <input type="password" name="password_lama" id="password_lama"
                        class="w-full border {{ $errors->has('password_lama') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('password_lama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                        Password Baru
                    </label>
                    <input type="password" name="password" id="password"
                        class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-8">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div class="pt-4 border-t md:text-right border-gray-100">
                    <button type="submit"
                        class="w-full md:w-auto bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-8 py-2.5 rounded-sm transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        <div class="md:hidden">
            <form action="{{ route('logout') }}" method="POST" class="flex md:justify-end">
                @csrf
                <button type="submit" 
                    class="w-full md:w-auto bg-gray-50 hover:bg-red-100 text-red-600 border border-red-200 text-sm font-semibold px-8 py-2.5 rounded-sm transition"
                    onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    Keluar Aplikasi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection