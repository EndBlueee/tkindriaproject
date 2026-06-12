@extends('layouts.admin')

@section('title', 'Detail Pembayaran')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.pembayaran.index') }}" class="hover:text-[#0068C4] transition">Pembayaran</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">{{ $pendaftaran->nomor_daftar }}</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Detail Pembayaran</h1>
    <p class="text-sm text-gray-500 mt-1">
        No. Daftar: <span class="font-semibold font-mono text-gray-700">{{ $pendaftaran->nomor_daftar }}</span>
    </p>
</div>

{{-- Notifikasi --}}
@if (session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-sm text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Kolom Kiri --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Info Pendaftaran --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Info Pendaftaran</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Nama Anak</p>
                    <p class="font-semibold text-gray-800">{{ $pendaftaran->calonMurid->nama_lengkap }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Orang Tua</p>
                    <p class="font-semibold text-gray-800">
                        {{ $pendaftaran->calonMurid->orangTua->nama_penanggung_jawab ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Gelombang</p>
                    <p class="font-semibold text-gray-800">{{ $pendaftaran->gelombang?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Jalur</p>
                    <p class="font-semibold text-gray-800">{{ ucfirst($pendaftaran->jalur) }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Total Biaya</p>
                    <p class="font-bold text-[#0068C4]">{{ $pendaftaran->gelombang?->total_biaya_format ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Status Pendaftaran</p>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $pendaftaran->status === 'menunggu_pembayaran' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $pendaftaran->status === 'cicilan_berjalan' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $pendaftaran->status === 'menunggu_nis' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $pendaftaran->status === 'aktif' ? 'bg-green-100 text-green-700' : '' }}">
                        {{ $pendaftaran->status_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- List Termin --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Rincian Pembayaran</h2>

            <div class="space-y-3">
                @forelse ($pendaftaran->pembayaran->sortBy('termin_ke') as $p)
                <div class="border border-gray-100 rounded-sm p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                @if ($p->total_termin === 1)
                                    Pembayaran Lunas
                                @else
                                    Cicilan {{ $p->termin_ke }} dari {{ $p->total_termin }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $p->metode_label }}
                                @if ($p->tanggal_bayar)
                                    · Dibayar {{ $p->tanggal_bayar->translatedFormat('d M Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">{{ $p->nominal_format }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $p->status === 'lunas' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $p->status === 'menunggu' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $p->status === 'kedaluwarsa' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ $p->status_label }}
                            </span>
                        </div>
                    </div>

                    {{-- Form catat offline (jika belum lunas & jalur offline) --}}
                    @if ($p->status !== 'lunas' && $pendaftaran->jalur === 'offline')
                    <form method="POST" action="{{ route('admin.pembayaran.catat', $p) }}"
                        class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap gap-3 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Metode</label>
                            <select name="metode"
                                class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}"
                                class="border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-sm transition">
                            ✓ Catat Lunas
                        </button>
                    </form>
                    @endif

                </div>
                @empty
                <p class="text-sm text-gray-400">Belum ada data pembayaran.</p>
                @endforelse
            </div>

            {{-- Ringkasan --}}
            @if ($pendaftaran->pembayaran->count() > 0)
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
                <span class="text-gray-500">
                    Terbayar {{ $pendaftaran->pembayaran->where('status', 'lunas')->count() }}
                    dari {{ $pendaftaran->pembayaran->first()->total_termin }} termin
                </span>
                <span class="font-bold text-gray-800">
                    Rp {{ number_format($pendaftaran->pembayaran->where('status', 'lunas')->sum('nominal'), 0, ',', '.') }}
                    / {{ $pendaftaran->gelombang?->total_biaya_format }}
                </span>
            </div>
            @endif

        </div>

    </div>

    {{-- Kolom Kanan --}}
    <div class="space-y-4">

        {{-- Aksi --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Aksi</h2>
            <a href="{{ route('admin.pendaftaran.show', $pendaftaran) }}"
                class="block w-full text-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 rounded-sm transition text-sm">
                Lihat Detail Pendaftaran
            </a>
        </div>

        {{-- Progress Pembayaran --}}
        @if ($pendaftaran->pembayaran->count() > 0)
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Progress Pembayaran</h2>
            @php
                $totalTermin = $pendaftaran->pembayaran->first()->total_termin;
                $terminLunas = $pendaftaran->pembayaran->where('status', 'lunas')->count();
                $persen      = $totalTermin > 0 ? ($terminLunas / $totalTermin) * 100 : 0;
            @endphp
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span>{{ $terminLunas }}/{{ $totalTermin }} termin lunas</span>
                <span>{{ number_format($persen, 0) }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all"
                    style="width: '{{ $persen }}%'"></div>
            </div>
        </div>
        @endif

    </div>

</div>

@endsection