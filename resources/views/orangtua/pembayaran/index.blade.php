@extends('layouts.orangtua')

@section('title', 'Pembayaran')

@section('main')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pembayaran</h1>
        <p class="text-sm text-gray-500 mt-1">Tahun Ajaran {{ $tahunAjaran?->nama ?? '-' }}</p>
    </div>

    {{-- Notifikasi --}}
    @if (session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-sm text-sm">
        {{ session('error') }}
    </div>
    @endif
    @if (session('warning'))
    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-sm text-sm">
        {{ session('warning') }}
    </div>
    @endif
    @if (session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-sm text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Kondisi 1: Belum Ada Pendaftaran ─────────────────────────────── --}}
    @if ($kondisi === 'belum_daftar')
    <div class="">
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Pendaftaran</h2>
            <p class="text-sm text-gray-500 mb-4">
                Invoice pembayaran akan tampil setelah Anda menyelesaikan formulir pendaftaran.
            </p>
            <a href="{{ route('orangtua.pendaftaran.index') }}"
                class="inline-block bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-sm transition">
                Mulai Pendaftaran →
            </a>
        </div>
    </div>

    {{-- ── Kondisi 2: Belum Waktunya Bayar ──────────────────────────────── --}}
    @elseif ($kondisi === 'belum_bayar')
    <div class="">
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-yellow-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Menunggu Verifikasi</h2>
            <p class="text-sm text-gray-500 mb-4">
                Invoice pembayaran akan tampil setelah dokumen Anda diverifikasi oleh admin.
            </p>
            <a href="{{ route('orangtua.pendaftaran.detail') }}"
                class="inline-block border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold px-6 py-2.5 rounded-sm transition">
                Lihat Status Pendaftaran
            </a>
        </div>
    </div>

    {{-- ── Kondisi 4: Invoice + Form Bayar ──────────────────────────────── --}}
    @else
    <div class="">
        <div class="space-y-2">

            {{-- Info Pendaftaran --}}
            <div class="text-sm bg-white p-4 border border-gray-100 rounded-sm flex flex-col md:flex-row justify-between px-4 md:px-10 gap-3 md:gap-4">
                <div class="flex flex-row justify-between md:flex-col md:items-start">
                    <span class="text-gray-500 w-32">No. Daftar</span>
                    <span class="font-mono font-semibold text-gray-700">{{ $pendaftaran->nomor_daftar }}</span>
                </div>
                <div class="flex flex-row justify-between md:flex-col md:items-start">
                    <span class="text-gray-500 w-32">Calon Murid</span>
                    <span class="font-semibold text-gray-700 capitalize">{{ $pendaftaran->calonMurid->nama_lengkap }}</span>
                </div>
                <div class="flex flex-row justify-between md:flex-col md:items-start">
                    <span class="text-gray-500 w-32">Gelombang</span>
                    <span class="font-semibold text-gray-700">{{ $gelombang->nama }}</span>
                </div>
                <div class="flex flex-row justify-between md:flex-col md:items-start">
                    <span class="text-gray-500 w-32">Tahun Ajaran</span>
                    <span class="font-semibold text-gray-700">{{ $tahunAjaran->nama }}</span>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Rincian Biaya --}}
            <div class="w-full overflow-x-auto bg-white border border-gray-100 rounded-sm p-4">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-800">
                            <th class="py-2 font-semibold w-full text-[#0068C4]">Rincian Biaya</th>
                            <th colspan="2" class="py-2 font-semibold text-left">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($rincianBiaya as $r)
                        <tr>
                            <td class="py-3 text-gray-800">{{ $r->nama_komponen }}</td>
                            <td class="py-3 text-gray-800 w-8">Rp</td>
                            <td class="py-3 font-medium text-gray-800 text-left w-28 whitespace-nowrap">
                                {{ number_format($r->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-gray-100">
                        <tr>
                            <td class="py-2 text-gray-500 font-semibold pr-6">Subtotal</td>
                            <td class="py-2 text-gray-700 w-8">Rp</td>
                            <td class="py-2 text-gray-700 font-medium w-28 whitespace-nowrap">
                                {{ number_format($rincianBiaya->sum('nominal'), 0, ',', '.') }}
                            </td>
                        </tr>
                        @if ($gelombang->diskon_persen > 0)
                        <tr>
                            <td class="py-2 text-orange-500 font-semibold text-right pr-6">Diskon {{ $gelombang->diskon_persen }}%</td>
                            <td class="py-2 text-orange-500 w-8">- Rp</td>
                            <td class="py-2 text-orange-500 font-medium w-28 whitespace-nowrap">
                                {{ number_format($rincianBiaya->sum('nominal') * $gelombang->diskon_persen / 100, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="py-3 font-bold text-[#0068C4] pr-6">Total</td>
                            <td class="py-3 font-bold text-lg text-[#0068C4] w-8">Rp</td>
                            <td class="py-3 font-bold text-lg text-[#0068C4] w-28 whitespace-nowrap">
                                {{ number_format($gelombang->totalBiaya(), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <hr class="border-gray-100">

            {{-- Form Pilih Pembayaran --}}
            <form method="POST" action="{{ route('orangtua.pembayaran.pilih') }}">
                @csrf

                <div class=" gap-6 ">

                    {{-- Kolom Kiri: Jenis Pembayaran --}}
                    <div class="mb-4">
                        @if (! $sudahAdaTagihan)
                        <div class="bg-white p-4">
                            <label for="jumlah_termin" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Jenis Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <select name="jumlah_termin" id="jumlah_termin"
                                class="w-full border {{ $errors->has('jumlah_termin') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">-- Pilih Jenis Pembayaran --</option>
                                <option value="1" {{ old('jumlah_termin') === '1' ? 'selected' : '' }}>
                                    Lunas — {{ $gelombang->total_biaya_format }}
                                </option>
                                @for ($i = 2; $i <= $tahunAjaran->maks_termin; $i++)
                                    <option value="{{ $i }}" {{ old('jumlah_termin') === (string) $i ? 'selected' : '' }}>
                                        Cicil {{ $i }}x — Rp {{ number_format(ceil($gelombang->totalBiaya() / $i), 0, ',', '.') }} / termin
                                    </option>
                                    @endfor
                            </select>
                            @error('jumlah_termin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @else
                        {{-- Sudah ada tagihan → tampil info --}}
                        @php
                        $terminBelumBayar = $pendaftaran->pembayaran
                        ->whereIn('status', ['menunggu', 'kedaluwarsa'])
                        ->sortBy('termin_ke')
                        ->first();
                        @endphp
                        <input type="hidden" name="sudah_ada" value="1">
                        @if ($terminBelumBayar)
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-sm text-sm">
                            <p class="font-semibold text-blue-700 mb-1">
                                @if ($terminBelumBayar->total_termin === 1)
                                Pembayaran Lunas
                                @else
                                Cicilan {{ $terminBelumBayar->termin_ke }}/{{ $terminBelumBayar->total_termin }}
                                @endif
                            </p>
                            <p class="text-blue-600">
                                Nominal: <span class="font-semibold">{{ $terminBelumBayar->nominal_format }}</span>
                            </p>
                        </div>
                        @endif
                        @endif
                    </div>

                    {{-- Kolom Kanan: Metode Pembayaran --}}
                    <div>
                        <div class="mb-4 bg-white p-4 rounded-sm">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Metode Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <div class=" grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($daftarMetode as $kode => $nama)
                                @php $namaFile = str_replace('_va', '', $kode); @endphp
                                <label class="flex items-center gap-3 p-3 border rounded-sm cursor-pointer transition
                                has-[:checked]:border-[#0068C4] has-[:checked]:bg-blue-50 border-gray-200 hover:border-gray-300">
                                    <input type="radio" name="metode" value="{{ $kode }}"
                                        {{ old('metode') === $kode ? 'checked' : '' }}
                                        class="accent-[#0068C4]">
                                    <img src="{{ asset('images/bank/' . $namaFile . '.png') }}"
                                        alt="{{ $nama }}" class="h-3.5 object-contain w-16">
                                    <span class="text-sm font-medium text-gray-700">{{ $nama }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('metode')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                {{-- Batas Pembayaran Gelombang --}}
                @if ($gelombang?->batas_pembayaran)
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-sm">
                    <p class="text-xs text-blue-700">
                        <span class="font-semibold">Info:</span>
                        Batas pembayaran untuk <strong>{{ $gelombang->nama }}</strong> adalah
                        <span class="font-semibold">
                            {{ $gelombang->batas_pembayaran->translatedFormat('d F Y') }}
                        </span>
                    </p>
                </div>
                @endif

                <button type="submit"
                    class="w-full bg-[#0068C4] hover:bg-blue-700 text-white font-bold py-3 rounded-sm transition text-sm">
                    Proses Bayar Sekarang →
                </button>

            </form>

        </div>
    </div>
    @endif
</div>

@endsection