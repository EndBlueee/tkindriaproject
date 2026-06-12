<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Pendaftar') - TK Indria</title>
    @vite('resources/css/app.css')
</head>

<body class="flex h-screen font-inter">

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
    <aside class="hidden md:flex flex-col w-72 h-screen bg-[#0068C4] shrink-0">

        {{-- Logo --}}
        <div class="flex items-center justify-center py-3 pt-4 gap-3">
            <div class="bg-white p-2 rounded-xl">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo TK Indria" class="w-10 rounded-full">
            </div>
            <h1 class="text-xl font-bold text-white">TK Indria</h1>
        </div>

        {{-- Menu --}}
        <div class="overflow-y-auto overflow-x-hidden grow">
            <ul class="flex flex-col py-4 space-y-1 px-2">

                {{-- Dashboard --}}
                <li>
                    <a href="{{ route('orangtua.dashboard.index') }}"
                        class="flex items-center px-4 py-3 rounded-md transition-colors
                        {{ request()->routeIs('orangtua.dashboard.index') ? 'bg-white text-[#0068C4] font-semibold' : 'text-white hover:bg-white hover:text-[#0068C4]' }}">
                        <x-heroicon-o-squares-2x2 class="w-6 h-6" />
                        <span class="ml-2">Dashboard</span>
                    </a>
                </li>

                {{-- Formulir Pendaftaran --}}
                <li>
                    <a href="{{ route('orangtua.pendaftaran.index') }}"
                        class="flex items-center px-4 py-3 rounded-md transition-colors
                        {{ request()->routeIs('orangtua.pendaftaran.*') ? 'bg-white text-[#0068C4] font-semibold' : 'text-white hover:bg-white hover:text-[#0068C4]' }}">
                        <x-heroicon-o-document-text class="w-6 h-6" />
                        <span class="ml-2">Formulir Pendaftaran</span>
                    </a>
                </li>

                {{-- Pembayaran --}}
                <li>
                    <a href="{{ route('orangtua.pembayaran.index') }}"
                        class="flex items-center px-4 py-3 rounded-md transition-colors
                        {{ request()->routeIs('orangtua.pembayaran.*') ? 'bg-white text-[#0068C4] font-semibold' : 'text-white hover:bg-white hover:text-[#0068C4]' }}">
                        <x-heroicon-o-credit-card class="w-6 h-6" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                        <span class="ml-2">Pembayaran</span>
                    </a>
                </li>



            </ul>
        </div>

        {{-- Logout — FIX: tambah border atas agar terlihat --}}
        <div class="px-2 pb-10 border-t border-blue-400">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center w-full px-4 py-3 mt-2 text-white hover:bg-blue-700 rounded-md transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                    </svg>
                    <span class="ml-2">Keluar</span>
                </button>
            </form>
        </div>

    </aside>

    {{-- ── Konten Utama ─────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-100">

        {{-- Navbar --}}
        <nav class="bg-white py-3 w-full flex items-center justify-between shadow-sm px-4 md:px-0 md:justify-end">
            {{-- Logo - hanya tampil di mobile --}}
            <div class="flex items-center gap-2 md:hidden">
                <div class="border border-gray-200 p-1.5 rounded-xl">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo TK Indria" class="w-7 rounded-full">
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-[#0068C4]">TK Indria</span>
                    <span class="text-xs text-gray-600">Depok</span>
                </div>
            </div>

            {{-- Profil --}}
            <a href="{{ route('orangtua.profile.index') }}" class="flex items-center space-x-3 md:px-6 hover:opacity-80 transition">
                <div class="w-9 h-9 bg-[#0068C4] rounded-full flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium leading-none">{{ Auth::user()->nama }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Orang Tua / Wali</p>
                </div>
            </a>
        </nav>

        {{-- Flash success --}}
        @if (session('success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Flash error --}}
        @if (session('error'))
        <div class="mx-6 mt-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Konten --}}
        <main class="flex-1 p-4 md:p-6 pb-24 md:pb-6">
            @yield('main')
        </main>

        <nav class="fixed bottom-0 w-full bg-white flex justify-around p-3 md:hidden shadow-[0_-1px_4px_rgba(0,0,0,0.08)]">
            <a href="{{ route('orangtua.dashboard.index') }}"
               class="flex flex-col items-center {{ request()->routeIs('orangtua.dashboard.index') ? 'text-blue-600' : 'text-gray-400' }}">
                <x-heroicon-o-squares-2x2 class="w-6 h-6" />
                <span class="text-xs mt-1">Beranda</span>
            </a>
            <a href="{{ route('orangtua.pendaftaran.index') }}"
               class="flex flex-col items-center {{ request()->routeIs('orangtua.pendaftaran.*') ? 'text-blue-600' : 'text-gray-400' }}">
                <x-heroicon-o-document-text class="w-6 h-6" />
                <span class="text-xs mt-1">Formulir</span>
            </a>
            <a href="{{ route('orangtua.pembayaran.index') }}"
               class="flex flex-col items-center {{ request()->routeIs('orangtua.pembayaran.*') ? 'text-blue-600' : 'text-gray-400' }}">
                <x-heroicon-o-credit-card class="w-6 h-6" />
                <span class="text-xs mt-1">Bayar</span>
            </a>
            <a href="{{ route('orangtua.profile.index') }}"
               class="flex flex-col items-center {{ request()->routeIs('orangtua.profile.*') ? 'text-blue-600' : 'text-gray-400' }}">
                <x-heroicon-o-user class="w-6 h-6" />
                <span class="text-xs mt-1">Profil</span>
            </a>
        </nav>

        <footer class="p-4 text-center text-sm text-gray-400 bg-white">
            &copy; {{ date('Y') }} TK Indria. All rights reserved.
        </footer>

    </div>

</body>

</html>