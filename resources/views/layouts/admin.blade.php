<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - TK Indria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex h-screen font-inter font-medium">

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
    <aside class="flex flex-col w-72 h-screen bg-white shrink-0">

        {{-- Logo --}}
        <div class="flex items-center justify-center bg-[#0068C4] py-3 pt-4 gap-3">
            <div class="">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo TK Indria" class="w-11 rounded-full">
            </div>
            <h1 class="text-xl font-bold text-white">TK Indria</h1>
        </div>

        {{-- Menu --}}
        <div class="overflow-y-auto overflow-x-hidden grow">
            <ul class="flex flex-col py-4 space-y-1 px-2">

                {{-- Dashboard — belum dibuat --}}
                <li>
                    <a href="{{ route('admin.dashboard.index') }}"
                        class="flex items-center px-4 py-3 border-[#0068C4] transition-colors
                        {{ request()->routeIs('admin.dashboard.index') ? 'border-r-[3px] text-[#0068C4] bg-gray-50' : 'text-gray-700 hover:text-[#0068C4] hover:border-r-[3px]' }}">
                        <x-heroicon-o-squares-2x2 class="w-6 h-6" />
                        <span class="ml-2">Dashboard</span>
                    </a>
                </li>

                {{-- Pendaftaran — belum dibuat --}}
                <li>
                    <a href="{{ route('admin.pendaftaran.index') }}"
                        class="flex items-center px-4 py-3 border-[#0068C4] transition-colors
                        {{ request()->routeIs('admin.pendaftaran.*') ? 'border-r-[3px] text-[#0068C4] bg-gray-50' : 'text-gray-700 hover:text-[#0068C4] hover:border-r-[3px]' }}">
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                        <span class="ml-2 whitespace-nowrap">Pendaftaran</span>
                    </a>
                </li>

                {{-- Pembayaran — belum dibuat --}}
                <li>
                    <a href="{{ route('admin.pembayaran.index') }}"
                        class="flex items-center px-4 py-3 border-[#0068C4] transition-colors
                        {{ request()->routeIs('admin.pembayaran.*') ? 'border-r-[3px] text-[#0068C4] bg-gray-50' : 'text-gray-700 hover:text-[#0068C4] hover:border-r-[3px]' }}">
                        <x-heroicon-o-credit-card class="w-6 h-6" />
                        <span class="ml-2 whitespace-nowrap">Pembayaran</span>
                    </a>
                </li>
                {{-- Manajemen User --}}
                <li>
                    <a href="{{ route('admin.user.index') }}"
                        class="flex items-center px-4 py-3 border-[#0068C4] transition-colors
                        {{ request()->routeIs('admin.user.*') ? 'border-r-[3px] text-[#0068C4] bg-gray-50' : 'text-gray-700 hover:text-[#0068C4] hover:border-r-[3px]' }}">
                        <x-heroicon-o-user-circle class="w-6 h-6" />
                        <span class="ml-2 whitespace-nowrap">Manajemen User</span>
                    </a>
                </li>
                {{-- Data Master (dropdown) — belum dibuat --}}
                <li>
                    <details class="group" {{ request()->routeIs('admin.calon-murid.*', 'admin.tahun-ajaran.*', 'admin.gelombang.*', 'admin.rincian-biaya.*', 'admin.persyaratan-dokumen.*') ? 'open' : '' }}>
                        <summary class="flex justify-between items-center cursor-pointer px-4 py-3 border-[#0068C4] transition-colors
                            {{ request()->routeIs('admin.calon-murid.*', 'admin.tahun-ajaran.*') ? 'border-r-[3px] text-[#0068C4] bg-gray-50' : 'text-gray-700 hover:text-[#0068C4] hover:border-r-[3px]' }}">
                            <div class="flex items-center">
                                <x-heroicon-o-circle-stack class="w-6 h-6" />
                                <span class="ml-2">Data Master</span>
                            </div>
                            <span class="transition group-open:rotate-180">
                                <svg fill="none" height="16" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="16">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </span>
                        </summary>
                        <ul class="mt-1 ml-4 space-y-1">
                            <li>
                                <a href="{{ route('admin.calon-murid.index') }}"
                                    class="block px-4 py-2 rounded-md text-sm transition-colors
                                    {{ request()->routeIs('admin.calon-murid.*') ? 'bg-gray-50 text-[#0068C4] font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0068C4]' }}">
                                    <span class="inline-flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>Calon Murid</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.tahun-ajaran.index') }}"
                                    class="block px-4 py-2 rounded-md text-sm transition-colors
                                    {{ request()->routeIs('admin.tahun-ajaran.*') ? 'bg-gray-50 text-[#0068C4] font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0068C4]' }}">
                                    <span class="inline-flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>Tahun Ajaran</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.gelombang.index') }}"
                                    class="block px-4 py-2 rounded-md text-sm transition-colors
                                    {{ request()->routeIs('admin.gelombang.*') ? 'bg-gray-50 text-[#0068C4] font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0068C4]' }}">
                                    <span class="inline-flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>Gelombang</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.rincian-biaya.index') }}"
                                    class="block px-4 py-2 rounded-md text-sm transition-colors
                                {{ request()->routeIs('admin.rincian-biaya.*') ? 'bg-gray-50 text-[#0068C4] font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0068C4]' }}">
                                    <span class="inline-flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>Rincian Biaya</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.persyaratan-dokumen.index') }}"
                                    class="block px-4 py-2 rounded-md text-sm transition-colors
                                {{ request()->routeIs('admin.persyaratan-dokumen.*') ? 'bg-gray-50 text-[#0068C4] font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0068C4]' }}">
                                    <span class="inline-flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>Persyaratan Dokumen</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                </li>


                {{-- Laporan (dropdown) --}}
                <li>
                    <details class="group" {{ request()->routeIs('admin.laporan.*') ? 'open' : '' }}>
                        <summary class="flex justify-between items-center cursor-pointer px-4 py-3 border-[#0068C4] transition-colors
                            {{ request()->routeIs('admin.laporan.*') ? 'border-r-[3px] text-[#0068C4] bg-gray-50' : 'text-gray-700 hover:text-[#0068C4] hover:border-r-[3px]' }}">
                            <div class="flex items-center">
                                <x-heroicon-o-clipboard-document class="w-6 h-6" />
                                <span class="ml-2 whitespace-nowrap">Laporan</span>
                            </div>
                            <span class="transition group-open:rotate-180">
                                <svg fill="none" height="16" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="16">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </span>
                        </summary>
                        <ul class="mt-1 ml-4 space-y-1">
                            <li>
                                <a href="{{ route('admin.laporan.pendaftar') }}"
                                    class="block px-4 py-2 rounded-md text-sm transition-colors
                                    {{ request()->routeIs('admin.laporan.pendaftar*') ? 'bg-gray-50 text-[#0068C4] font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0068C4]' }}">
                                    <span class="inline-flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>Pendaftaran</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.laporan.pembayaran') }}"
                                    class="block px-4 py-2 rounded-md text-sm transition-colors
                                    {{ request()->routeIs('admin.laporan.pembayaran*') ? 'bg-gray-50 text-[#0068C4] font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0068C4]' }}">
                                    <span class="inline-flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>Pembayaran</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                </li>

            </ul>
        </div>

        {{-- Logout --}}
        <div class="px-2 pb-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center w-full px-4 py-3 bg-[#0068C4] text-white hover:bg-blue-700 rounded-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                    </svg>
                    <span class="ml-2 text-sm">Keluar</span>
                </button>
            </form>
        </div>

    </aside>

    {{-- ── Konten Utama ─────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-100">

        <nav class="bg-white py-3 px-6 w-full flex items-center justify-between shadow-sm">
            <p class="text-sm text-gray-500">
                Selamat datang, <span class="font-semibold text-gray-700">{{ Auth::user()->nama }}</span>
            </p>

            <div class="flex items-center gap-4">

                {{-- ── Icon Notifikasi ─────────────────────────────────────── --}}
                @php
                $notifikasis = \App\Models\Notifikasi::latest()->take(3)->get();
                $notifCount  = \App\Models\Notifikasi::belumDibaca()->count();
                @endphp

                <div class="relative" x-data="{ open: false }">

                    {{-- Tombol Bell --}}
                    <button @click="open = !open"
                        class="relative bg-gray-100 p-2 rounded-full hover:bg-gray-200 transition">
                        <x-heroicon-o-bell class="w-5 h-5 text-[#0068C4]" />

                        {{-- Badge --}}
                        @if ($notifCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold
                    w-4 h-4 rounded-full flex items-center justify-center leading-none">
                            {{ $notifCount > 9 ? '9+' : $notifCount }}
                        </span>
                        @endif
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-80 bg-white rounded-sm shadow-lg border border-gray-100 z-50">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-700">Notifikasi</p>
                            @if ($notifCount > 0)
                            <form method="POST" action="{{ route('admin.notifikasi.baca-semua') }}">
                                @csrf
                                <button type="submit"
                                    class="text-xs text-[#0068C4] hover:underline">
                                    Tandai semua dibaca
                                </button>
                            </form>
                            @endif
                        </div>

                        {{-- List Notifikasi --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                            @forelse ($notifikasis as $notif)
                            <a href="{{ route('admin.notifikasi.baca-satu', $notif->id) }}"
                                class="flex items-start gap-3 px-4 py-3 transition {{ $notif->is_read ? 'hover:bg-gray-50' : 'bg-blue-50 hover:bg-blue-50' }}">

                                {{-- Icon --}}
                                <div class="mt-0.5 shrink-0">
                                    <x-dynamic-component :component="$notif->icon" class="w-5 h-5 {{ $notif->warna }}" />
                                </div>

                                {{-- Konten --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-700">{{ $notif->judul }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $notif->pesan }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notif->waktu }}</p>
                                </div>

                                {{-- Dot belum dibaca --}}
                                @if (!$notif->is_read)
                                <div class="w-2 h-2 bg-[#0068C4] rounded-full shrink-0 mt-1"></div>
                                @endif

                            </a>
                            @empty
                            <div class="px-4 py-8 text-center">
                                <x-heroicon-o-bell class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                                <p class="text-sm text-gray-400">Tidak ada notifikasi baru</p>
                            </div>
                            @endforelse
                        </div>

                    </div>
                </div>

                {{-- ── Profile ──────────────────────────────────────────────── --}}
                <div class="flex items-center gap-3 px-4 py-2 rounded-sm">
                    <div class="w-8 h-8 bg-[#0068C4] rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-700 truncate">{{ Auth::user()->nama }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>

            </div>
        </nav>

        @if (session('success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-sm text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mx-6 mt-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-sm text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 p-6">
            @yield('main')
        </main>

        <footer class="p-4 text-center text-sm text-gray-400 bg-white">
            &copy; {{ date('Y') }} TK Indria. All rights reserved.
        </footer>
    </div>
    <script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
</body>

</html>