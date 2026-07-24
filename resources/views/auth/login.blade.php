<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo svg.svg') }}" type="image/svg+xml">
    <title>Login — PMB TK Indria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-inter">
    <section class="min-h-screen flex flex-col md:flex-row md:h-screen">

        {{-- Kolom Kiri: ilustrasi --}}
        
            <div class="hidden md:block md:w-7/12 overflow-hidden p-16">
                <img src="{{ asset('images/gambar2.png') }}" alt="Ilustrasi Login" class="w-full h-full object-cover rounded-xl">
            </div>
        

        {{-- Kolom Kanan: form login --}}
        <div class="w-full md:w-5/12 bg-white flex items-center justify-center py-10 md:py-0">
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
                    <div class="flex justify-between border-t border-gray-200 mt-6 pt-4">

                        <p class="text-center text-xs text-gray-400 mt-4">
                            TK Indria Depok &copy; {{ date('Y') }}
                        </p>

                        <p class="text-left text-xs text-gray-400 mt-4">
                            <a href="#kontak" class="hover:underline">Butuh bantuan?</a> &bull;
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class=" flex flex-col items-center justify-center bg-gray-50 px-4 py-16">
        <div class="max-w-4xl w-full">
            <div class="text-center mb-5">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">Alur Pendaftaran PMB Online</h2>
            </div>
            <img src="{{ asset('images/gambar4.png') }}" alt="Alur Pendaftaran" class="w-full opacity-90 rounded-xl shadow-lg">
        </div>
    </section>
    <section class="py-20 bg-white max-w-6xl mx-auto px-4 min-h-screen flex flex-col">
        <div class="text-center md:mb-0 mb-8">
            <h2 class="text-4xl font-bold">Periode Gelombang Pendaftaran</h2>
            <p class="max-w-xl mx-auto mt-6 text-gray-600">Silakan perhatikan jadwal pendaftaran untuk setiap gelombang. Pastikan Anda mendaftar sesuai dengan waktu yang telah ditentukan.</p>
        </div>
        <div class="grow flex items-center mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
                @forelse ($gelombangs as $gelombang)
                <div class="border border-gray-200 rounded-lg shadow-md overflow-hidden">
                    <div class="bg-[#0068C4] text-white">
                        <p class="font-semibold text-lg text-center px-4 py-4">{{ $gelombang->nama }}</p>
                        <div class="bg-white p-6">
                            <div class="bg-gray-100 py-6 rounded-lg flex items-center justify-center gap-x-2.5">
                                <x-uni-calender class="h-6 w-6 text-[#0068C4] flex-shrink-0" />
                                <p class="text-xl font-semibold text-gray-600">
                                    {{ \Carbon\Carbon::parse($gelombang->tanggal_buka)->isoFormat('D MMMM') }}
                                    -
                                    {{ \Carbon\Carbon::parse($gelombang->tanggal_tutup)->isoFormat('D MMMM Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="md:col-span-3 text-center py-8 px-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-500">Saat ini belum ada periode gelombang pendaftaran yang dibuka.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
    <section id="kontak" class="bg-gray-50 py-16 px-4 scroll-mt-16">
        <div class="max-w-4xl mx-auto bg-[#e6eef5] text-white rounded-xl shadow-lg p-8 md:p-15 flex flex-col md:flex-row items-center gap-8 border border-gray-200">
            {{-- Ikon --}}
            <div class="shrink-0">
                <div class="bg-blue-200 p-10 rounded-full">
                    <img src="{{ asset('images/headset.svg') }}" alt="Headset" class="h-20 w-20">
                </div>
            </div>
            {{-- Konten Teks --}}
            <div class="text-center md:text-left grow">
                <h3 class="text-3xl text-gray-800 font-bold">Butuh Bantuan atau Ada Pertanyaan?</h3>
                <p class="mt-2 text-sm text-gray-600 max-w-lg">
                    Tim kami siap membantu Anda melalui proses pendaftaran. Jangan ragu untuk menghubungi kami.
                </p>
            </div>
            <div class="shrink-0 space-y-4">
                <div class="shrink-0 mt-4 md:mt-0">
                    <a href="https://wa.me/62895635777179" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 bg-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:bg-green-200 transition-transform transform hover:scale-105">
                        <div class="bg-green-100 p-2 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M17.21,3.312C15.348,1.448,12.783,0.406,10,0.406C4.719,0.406,0.406,4.719,0.406,10c0,1.75,0.453,3.398,1.25,4.86L0.406,20l5.141-1.344c1.406,0.75,3,1.156,4.625,1.156h0.016c5.281,0,9.594-4.312,9.594-9.594C19.781,7.217,18.738,5.176,17.21,3.312 M10,18.219c-1.422,0-2.812-0.375-4.062-1.094L5.5,16.875l-3,0.781l0.812-2.938l-0.219-0.344C2.312,12.938,1.906,11.5,1.906,10c0-4.469,3.625-8.094,8.094-8.094c2.188,0,4.219,0.859,5.75,2.375c1.531,1.531,2.375,3.562,2.375,5.75C18.094,14.594,14.469,18.219,10,18.219 M14.406,12.062c-0.234-0.125-1.375-0.688-1.594-0.766c-0.219-0.078-0.375-0.125-0.531,0.125c-0.156,0.234-0.609,0.766-0.75,0.922c-0.141,0.156-0.281,0.172-0.516,0.062c-0.234-0.109-1-0.375-1.906-1.188c-0.703-0.625-1.172-1.391-1.312-1.625c-0.141-0.234-0.016-0.359,0.109-0.484c0.109-0.109,0.234-0.281,0.359-0.422c0.125-0.141,0.156-0.234,0.234-0.391c0.078-0.156,0.031-0.297-0.016-0.422c-0.047-0.125-0.531-1.281-0.719-1.75C6.312,5.531,6.109,5.594,5.953,5.594c-0.141,0-0.297,0-0.453,0C5.344,5.594,5.094,5.656,4.891,5.859C4.688,6.062,4.125,6.578,4.125,7.734c0,1.156,0.844,2.25,0.969,2.422c0.125,0.156,1.656,2.531,4.062,3.578c0.562,0.25,1,0.406,1.344,0.516c0.562,0.172,1.062,0.141,1.469,0.094c0.453-0.062,1.375-0.562,1.578-1.109c0.203-0.547,0.203-1.016,0.141-1.109C14.719,12.25,14.562,12.188,14.406,12.062" />
                            </svg>
                        </div>
                        <div class="text-left text-xs space-y-1">
                            <span class="block leading-tight text-gray-800 uppercase">Admin WhatsApp</span>
                            <span class="block font-normal text-gray-800 leading-tight">0895-6357-7717</span>
                        </div>
                    </a>
                </div>
                <div class="shrink-0 mt-4 md:mt-0">
                    <a href="tel:081905105663" class="inline-flex items-center gap-4 bg-white text-gray-800 font-bold py-2.5 px-6 rounded-xl shadow-md hover:bg-blue-200 transition-transform transform hover:scale-105">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs leading-tight uppercase">Kontak Sekolah</span>
                            <span class="block text-xs font-normal leading-tight text-gray-600">0819-0510-5663</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
</body>

</html>