<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — PMB TK Indria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col md:flex-row md:h-screen font-inter">

    {{-- Kolom Kiri: ilustrasi --}}
    <div class="hidden md:block md:w-7/12 overflow-hidden p-16">
        <img src="{{ asset('images/gambar2.png') }}" alt="Ilustrasi Register" class="w-full h-full object-cover rounded-xl">
    </div>

    {{-- Kolom Kanan: form register --}}
    <div class="w-full md:w-5/12 bg-white flex items-center justify-center overflow-y-auto">
        <div class="w-full max-w-md px-8 py-8">

            {{-- Logo & Judul --}}
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">PMB Online</h1>
                <p class="text-gray-500 text-sm mt-1">TK Indria Depok</p>
            </div>

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-sm text-sm text-red-600">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                {{-- Nama --}}
                <div class="mb-4">
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" id="nama"
                        value="{{ old('nama') }}"
                        placeholder="Nama orang tua / wali"
                        class="w-full border {{ $errors->has('nama') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor HP --}}
                <div class="mb-4">
                    <label for="nomor_hp" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomor_hp" id="nomor_hp"
                        value="{{ old('nomor_hp') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full border {{ $errors->has('nomor_hp') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('nomor_hp')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password"
                        placeholder="Minimal 6 karakter"
                        class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Ulangi password"
                        class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                {{-- Tombol Daftar --}}
                <button type="submit"
                    class="w-full bg-[#0068C4] hover:bg-gray-500 text-white font-semibold py-2.5 rounded-sm text-sm transition">
                    Buat Akun
                </button>

            </form>

            {{-- Link ke Login --}}
            <p class="text-center text-sm text-gray-500 mt-4">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#0068C4] hover:underline font-medium">Masuk di sini</a>
            </p>

            <p class="text-center text-xs text-gray-400 mt-6">
                TK Indria Depok &copy; {{ date('Y') }}
            </p>

        </div>
    </div>

</body>
</html>
