@extends('layouts.orangtua')

@section('title', 'Status Pembayaran')

@section('main')

@php
    $terminBerikutnya = $pembayaran->pendaftaran->pembayaran
        ->whereIn('status', ['menunggu', 'kedaluwarsa'])
        ->sortBy('termin_ke')
        ->first();
    $semuaLunas = ($pembayaran->status === 'lunas' && !$terminBerikutnya);
    $isBerhasil = ($status === 'success' || $pembayaran->status === 'lunas');
@endphp

@if ($isBerhasil)
{{-- ── Kondisi Berhasil (Lunas atau Cicilan) ─────────────────────── --}}
<div class="max-w-4xl mx-auto mt-8">
    <div class="space-y-4 overflow-hidden">
        <div class="bg-green-500 rounded-3xl px-6 py-8 flex flex-col md:flex-row items-center md:justify-between gap-4 text-center md:text-left">
            <div class="flex flex-col md:flex-row items-center gap-4">
                <div class="w-18 h-18 bg-white rounded-3xl flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-7 text-green-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <div>
                    @if ($semuaLunas)
                    <h2 class="text-2xl font-bold text-white">Pembayaran Lunas!</h2>
                    <p class="text-green-100 text-sm mt-1">Semua tagihan sudah dibayar</p>
                    @else
                    <h2 class="text-2xl font-bold text-white">Pembayaran Berhasil!</h2>
                    <p class="text-green-100 text-sm mt-1">Cicilan {{ $pembayaran->termin_ke }}/{{ $pembayaran->total_termin }} telah diterima</p>
                    @endif
                </div>
            </div>
            <div class="hidden md:block">
                @if ($semuaLunas)
                <p class="text-green-100 font-semibold text-sm">TOTAL DIBAYAR</p>
                <p class="text-white text-2xl font-bold mt-1">{{ $pembayaran->pendaftaran->gelombang->total_biaya_format }}</p>
                @else
                <p class="text-green-100 font-semibold text-sm">NOMINAL DIBAYAR</p>
                <p class="text-white text-2xl font-bold mt-1">{{ $pembayaran->nominal_format }}</p>
                @endif
            </div>
        </div>
        <div class="bg-white py-0 md:p-6 text-sm flex flex-col md:flex-row justify-between rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-row md:flex-col justify-between border-b md:border-b-0 border-gray-200 py-3 px-3 md:px-0">
                <span class="text-gray-500">Calon Murid</span>
                <span class="font-semibold text-gray-800 capitalize">{{ $pembayaran->pendaftaran->calonMurid->nama_lengkap }}</span>
            </div>
            <div class="flex flex-row md:flex-col justify-between border-b md:border-b-0 border-gray-200 py-3 px-3 md:px-0">
                <span class="text-gray-500">Gelombang</span>
                <span class="font-semibold text-gray-800">{{ $pembayaran->pendaftaran->gelombang->nama }}</span>
            </div>
            <div class="flex flex-row md:flex-col justify-between border-b md:border-b-0 border-gray-200 py-3 px-3 md:px-0">
                <span class="text-gray-500">No. Invoice</span>
                <span class="font-bold text-gray-600">{{ $pembayaran->order_id }}</span>
            </div>
            <div class="flex flex-row md:flex-col justify-between py-3 px-3 md:px-0">
                <span class="text-gray-500">No. Pendaftaran</span>
                <span class="font-bold text-gray-600">{{ $pembayaran->pendaftaran->nomor_daftar }}</span>
            </div>
        </div>

        {{-- List termin --}}
        <div class="pt-3 border-t border-gray-100 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Riwayat Pembayaran</p>
            <div class="space-y-2">
                @foreach ($pembayaran->pendaftaran->pembayaran->sortBy('termin_ke') as $p)
                <div class="flex items-center gap-2 py-4 px-3 md:px-4 border {{ $p->status === 'lunas' ? 'border-gray-200 bg-gray-50' : 'border-dashed border-gray-300 bg-white' }} rounded-xl">
                    @if ($p->status === 'lunas')
                    <x-heroicon-o-check-circle class="size-6 text-green-500 shrink-0" />
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-300 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    @endif
                    <span class="text-gray-600 text-sm">
                        @if ($p->total_termin === 1) Lunas Sekaligus
                        @else Cicilan {{ $p->termin_ke }}/{{ $p->total_termin }}
                        @endif
                    </span>

                    <div class="ml-auto text-right whitespace-nowrap">
                        <span class="font-semibold text-gray-800">{{ $p->nominal_format }}</span>
                        @if ($p->status === 'lunas')
                        <span class="ml-1 text-xs text-green-600 font-semibold">✓ Lunas</span>
                        @elseif ($p->status === 'menunggu')
                        <span class="ml-1 text-xs text-gray-400 font-semibold">Belum dibayar</span>
                        @else
                        <span class="ml-1 text-xs text-red-600 font-semibold">❌ {{ ucfirst($p->status) }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @if (!$semuaLunas && $terminBerikutnya)
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 flex flex-col md:flex-row items-center justify-between gap-4 mt-2 shadow-sm">
            <div>
                <p class="text-sm text-blue-800 font-bold mb-1">Tagihan Berikutnya</p>
                <p class="text-sm text-blue-700">
                    Selesaikan tagihan Cicilan {{ $terminBerikutnya->termin_ke }}/{{ $terminBerikutnya->total_termin }} 
                    sebesar <span class="font-bold">{{ $terminBerikutnya->nominal_format }}</span>
                </p>
            </div>
            <a href="{{ route('orangtua.pembayaran.index') }}"
                class="block w-full md:w-auto text-center bg-[#0068C4] hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-sm transition text-sm whitespace-nowrap">
                Bayar Cicilan Berikutnya →
            </a>
        </div>
        @endif

        <a href="{{ route('orangtua.pendaftaran.detail') }}"
            class="block text-center text-sm text-white hover:text-white transition bg-[#0068C4] py-3 rounded-sm mt-4">
            Kembali ke Detail Pendaftaran
        </a>
    </div>
</div>

@else
{{-- ── Kondisi Selesai Standar (Pending/Gagal) ──────────────────────── --}}
<div class="max-w-4xl mx-auto mt-8">
    <div class="bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">

        @if ($status === 'pending')

        {{-- ── Pending ─────────────────────────────────────────────── --}}
        <div class="bg-yellow-500 px-6 py-8 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-8 text-yellow-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white">Menunggu Pembayaran</h1>
            <p class="text-yellow-100 text-sm mt-1">Selesaikan pembayaran sesuai instruksi</p>
        </div>

        @else

        {{-- ── Gagal ───────────────────────────────────────────────── --}}
        <div class="bg-red-500 px-6 py-8 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-8 text-red-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white">Pembayaran Gagal</h1>
            <p class="text-red-100 text-sm mt-1">Pembayaran tidak berhasil diproses</p>
        </div>

        @endif

        {{-- Info Pembayaran --}}
        <div class="p-6 space-y-4">

            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">No. Invoice</span>
                    <span class="font-mono text-xs text-gray-700">{{ $pembayaran->order_id }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Calon Murid</span>
                    <span class="font-semibold text-gray-700">{{ $pembayaran->pendaftaran->calonMurid->nama_lengkap }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-semibold text-gray-700">{{ $pembayaran->metode_label }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Jenis</span>
                    <span class="font-semibold text-gray-700">
                        @if ($pembayaran->total_termin === 1)
                            Lunas Sekaligus
                        @else
                            Cicilan {{ $pembayaran->termin_ke }}/{{ $pembayaran->total_termin }}
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <span class="font-bold text-gray-800">Total Bayar</span>
                    <span class="font-bold text-lg text-[#0068C4]">{{ $pembayaran->nominal_format }}</span>
                </div>
            </div>

            @if ($status === 'pending')

            <a href="{{ route('orangtua.pembayaran.bayar', $pembayaran) }}"
                class="block w-full text-center bg-[#0068C4] hover:bg-blue-700 text-white font-bold py-3 rounded-sm transition text-sm">
                Lihat Kode Pembayaran →
            </a>

            @else

            <a href="{{ route('orangtua.pembayaran.index') }}"
                class="block w-full text-center bg-[#0068C4] hover:bg-blue-700 text-white font-bold py-3 rounded-sm transition text-sm">
                Coba Lagi
            </a>

            @endif

            <a href="{{ route('orangtua.pendaftaran.detail') }}"
                class="block text-center text-sm text-gray-400 hover:text-gray-600 transition">
                ← Kembali ke Detail Pendaftaran
            </a>

        </div>
    </div>
</div>
@endif

@endsection