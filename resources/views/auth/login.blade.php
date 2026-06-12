<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PMB TK Indria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col md:flex-row md:h-screen">

    {{-- Kolom Kiri: ilustrasi --}}
    <div class="hidden md:block md:w-3/4 overflow-hidden">
        <img src="{{ asset('images/gambar2.png') }}" alt="Ilustrasi Login" class="w-full h-full object-cover">
    </div>

    {{-- Kolom Kanan: form login --}}
    <div class="w-full md:w-3/5 bg-white flex items-center justify-center py-10 md:py-0">
        <div class="w-full max-w-md px-8">
        {{-- Card Login --}}
        <div class="">
        {{-- Logo & Judul --}}
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo PMB" class="mx-auto h-16 w-16 mb-2 rounded-full object-cover">
            <h1 class="text-2xl font-bold text-gray-800">PMB Online</h1>
            <p class="text-gray-500 text-sm mt-1">TK Indria Depok</p>
        </div>

            @if (session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                {{ session('error') }}
            </div>
            @endif

            @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-600">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                {{-- Nomor HP --}}
                <div class="mb-4">
                    <label for="nomor_hp" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nomor HP
                    </label>
                    <input type="text" name="nomor_hp" id="nomor_hp"
                        value="{{ old('nomor_hp') }}"
                        placeholder="08xxxxxxxxxx"
                        autofocus
                        class="w-full border {{ $errors->has('nomor_hp') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('nomor_hp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password
                    </label>
                    <input type="password" name="password" id="password"
                        placeholder="••••••••"
                        class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Login --}}
                <button type="submit"
                    class="w-full bg-[#0068C4] hover:bg-gray-500 text-white font-semibold py-2.5 rounded-sm text-sm transition">
                    Masuk
                </button>

            </form>

            {{-- Link ke Register --}}
            <p class="text-center text-sm text-gray-500 mt-4">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-[#0068C4] hover:underline font-medium">Daftar di sini</a>
            </p>

            <p class="text-center text-xs text-gray-400 mt-6">
                TK Indria Depok &copy; {{ date('Y') }}
            </p>
        </div>
        </div>
    </div>

</body>

</html>