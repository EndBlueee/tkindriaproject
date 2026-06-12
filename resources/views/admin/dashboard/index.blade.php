@extends('layouts.admin')
@section('title', 'Dashboard')

@section('main')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">Ringkasan data penerimaan murid baru TK Indria.</p>
</div>

{{-- ── 4 Stat Cards ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

    {{-- Total Pendaftar --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
            <x-heroicon-o-users class="w-6 h-6 text-blue-600" />
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Pendaftar</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total_pendaftar'] }}</p>
        </div>
    </div>

    {{-- Menunggu Verifikasi --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="shrink-0 w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
            <x-heroicon-o-clock class="w-6 h-6 text-yellow-600" />
        </div>
        <div>
            <p class="text-sm text-gray-500">Menunggu Verifikasi</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['menunggu_verifikasi'] }}</p>
        </div>
    </div>

    {{-- Menunggu NIS --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="shrink-0 w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
            <x-heroicon-o-identification class="w-6 h-6 text-purple-600" />
        </div>
        <div>
            <p class="text-sm text-gray-500">Menunggu NIS</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['menunggu_nis'] }}</p>
        </div>
    </div>

    {{-- Murid Aktif --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="shrink-0 w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
            <x-heroicon-o-check-badge class="w-6 h-6 text-green-600" />
        </div>
        <div>
            <p class="text-sm text-gray-500">Murid Aktif</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['murid_aktif'] }}</p>
        </div>
    </div>

</div>

{{-- ── 2 Kolom Bawah ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mt-5">

    {{-- Ringkasan Pembayaran --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center gap-2 mb-4">
            
            <h2 class="font-semibold text-gray-800">Ringkasan Pembayaran</h2>
        </div>

        <div class="mb-4 p-4 bg-green-50 rounded-lg border border-green-100">
            <p class="text-xs text-green-600 mb-1">Total Nominal Terkumpul</p>
            <p class="text-2xl font-bold text-green-700">
                Rp {{ number_format($ringkasanPembayaran['nominal_terkumpul'], 0, ',', '.') }}
            </p>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 shrink-0"></span>
                    <span class="text-sm font-medium text-gray-600">Lunas</span>
                </div>
                <span class="text-sm font-medium text-gray-500">{{ $ringkasanPembayaran['total_lunas'] }} transaksi</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 shrink-0"></span>
                    <span class="text-sm font-medium text-gray-600">Menunggu Pembayaran</span>
                </div>
                <span class="text-sm font-medium text-gray-500">{{ $ringkasanPembayaran['menunggu'] }} transaksi</span>
            </div>
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400 shrink-0"></span>
                    <span class="text-sm font-medium text-gray-600">Kedaluwarsa</span>
                </div>
                <span class="text-sm font-medium text-gray-500">{{ $ringkasanPembayaran['kedaluwarsa'] }} transaksi</span>
            </div>
        </div>

        <a href="{{ route('admin.pembayaran.index') }}"
            class="mt-auto pt-4 flex items-center justify-center gap-1 text-sm text-[#0068C4] hover:underline">
            Lihat semua pembayaran
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>

    {{-- Info Gelombang Aktif --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center gap-2 mb-4">
            
            <h2 class="font-semibold text-gray-800">Gelombang Aktif</h2>
            @if ($gelombangAktif->isNotEmpty())
                <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                    {{ $gelombangAktif->count() }} aktif
                </span>
            @endif
        </div>

        @forelse ($gelombangAktif as $gelombang)
        <div class="border border-gray-200 rounded-sm p-5 mb-3">
            <div class="flex items-start justify-between gap-2 mb-5">
                <div>
                    <p class="font-semibold text-gray-800">{{ $gelombang->nama }}</p>
                    <p class="text-xs mt-0.5 text-gray-500">Tahun Ajaran {{ $gelombang->tahunAjaran->nama ?? '-' }}</p>
                </div>
                @if ($gelombang->diskon_persen > 0)
                <span class="shrink-0 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">
                    Diskon {{ $gelombang->diskon_persen }}%
                </span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-2 text-sm  pt-2 border-t border-blue-100">
                <div class="mt-3">
                    <p class="text-xs font-medium text-gray-500">Tanggal Buka</p>
                    <p class="text-gray-700">{{ $gelombang->tanggal_buka->translatedFormat('d M Y') }}</p>
                </div>
                <div class="mt-3">
                    <p class="text-xs font-medium text-gray-500">Tanggal Tutup</p>
                    <p class="text-gray-700">{{ $gelombang->tanggal_tutup->translatedFormat('d M Y') }}</p>
                </div>
                <div class="mt-6">
                    <p class="text-xs font-medium text-gray-500">Total Biaya</p>
                    <p class="font-semibold text-[#0068C4]">{{ $gelombang->total_biaya_format }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="#d1d5db" class="size-12 mb-3">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <p class="text-sm font-medium text-gray-400">Tidak ada gelombang aktif</p>
            <p class="text-xs text-gray-400 mt-1">Saat ini tidak ada gelombang pendaftaran yang sedang buka.</p>
        </div>
        @endforelse

        <a href="{{ route('admin.gelombang.index') }}"
            class="mt-auto pt-4 flex items-center justify-center gap-1 text-sm text-[#0068C4] hover:underline">
            Kelola gelombang
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>

</div>

@endsection
