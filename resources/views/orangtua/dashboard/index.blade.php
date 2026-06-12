@extends('layouts.orangtua')

@section('main')

{{-- Sambutan --}}
<div class="max-w-4xl mx-auto">
    <div class="bg-[#0068C4] rounded-2xl px-5 py-5 md:px-8 md:py-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between shadow-md">
        <div>
            <p class="text-blue-200 text-sm font-medium mb-1">Selamat datang,</p>
            <h2 class="text-white text-xl md:text-2xl font-bold">Halo, {{ Auth::user()->nama }}!</h2>
            @if ($tahunAjaran)
            <div class="flex flex-wrap items-center gap-2 md:flex-nowrap md:gap-3 md:text-sm mt-2">
                <span class="bg-white/20 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-sm font-medium flex items-center gap-2 text-xs md:text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <span class="md:hidden">TA</span>
                    <span class="hidden md:inline">Tahun Ajaran</span>
                    {{ $tahunAjaran->nama }}
                </span>

                @if ($gelombangAktif)
                <span class="bg-white text-[#0068C4] px-3 py-1.5 md:px-4 md:py-2 rounded-sm font-semibold flex items-center gap-2 text-xs md:text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ $gelombangAktif->nama }} Aktif
                </span>
                @else
                <span class="bg-white/10 text-blue-200 px-3 py-1.5 md:px-4 md:py-2 rounded-full font-medium text-xs md:text-sm">
                    Belum ada gelombang aktif
                </span>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Notifikasi Status Pendaftaran --}}
    @if ($pendaftaran)
    <div class="mt-5">

        {{-- Menunggu Verifikasi --}}
        @if ($pendaftaran->status === 'menunggu_verifikasi')
        <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-sm px-4 py-4 md:px-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-yellow-500 shrink-0 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-yellow-800">
                    Formulir pendaftaran {{ $pendaftaran->calonMurid->nama_lengkap }} sedang menunggu verifikasi.
                </p>
                <p class="text-xs text-yellow-600 mt-0.5">
                    Dokumen Anda sedang diperiksa oleh admin. Kami akan segera menghubungi Anda.
                </p>
            </div>
        </div>

        {{-- Perlu Revisi --}}
        @elseif ($pendaftaran->status === 'perlu_revisi')
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-sm px-4 py-4 md:px-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500 shrink-0 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-800">
                    Dokumen {{ $pendaftaran->calonMurid->nama_lengkap }} perlu diperbaiki.
                </p>
                @if ($pendaftaran->catatan_admin)
                <p class="text-xs text-red-600 mt-0.5">
                    Catatan: {{ $pendaftaran->catatan_admin }}
                </p>
                @endif
                <a href="{{ route('orangtua.pendaftaran.revisi') }}"
                    class="inline-block mt-2 text-xs font-semibold text-white bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-sm transition">
                    Upload Ulang Dokumen →
                </a>
            </div>
        </div>

        {{-- Menunggu Pembayaran --}}
        @elseif ($pendaftaran->status === 'menunggu_pembayaran')
        <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-sm px-4 py-4 md:px-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-blue-500 shrink-0 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
            </svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-blue-800">
                    Formulir {{ $pendaftaran->calonMurid->nama_lengkap }} telah diverifikasi. Silakan lakukan pembayaran.
                </p>
                <p class="text-xs text-blue-600 mt-0.5">
                    Total biaya: <span class="font-semibold">{{ $pendaftaran->gelombang->total_biaya_format }}</span>
                </p>
                <a href="{{ route('orangtua.pembayaran.index') }}"
                    class="inline-block mt-2 text-xs font-semibold text-white bg-[#0068C4] hover:bg-blue-700 px-3 py-1.5 rounded-sm transition">
                    Lakukan Pembayaran →
                </a>
            </div>
        </div>

        {{-- Cicilan Berjalan --}}
        @elseif ($pendaftaran->status === 'cicilan_berjalan')
        @php
        $terminLunas = $pendaftaran->pembayaran->where('status', 'lunas')->count();
        $totalTermin = $pendaftaran->pembayaran->first()?->total_termin ?? 1;
        $totalTerbayar = $pendaftaran->pembayaran->where('status', 'lunas')->sum('nominal');
        $sisaTagihan = $pendaftaran->gelombang->totalBiaya() - $totalTerbayar;
        $terminBerikutnya = $pendaftaran->pembayaran->where('status', 'menunggu')->sortBy('termin_ke')->first();
        @endphp
        <div class="flex items-start gap-3 bg-orange-50 border border-orange-200 rounded-sm px-4 py-4 md:px-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-orange-500 shrink-0 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-orange-800">
                    Cicilan {{ $pendaftaran->calonMurid->nama_lengkap }} sedang berjalan ({{ $terminLunas }}/{{ $totalTermin }} termin lunas).
                </p>
                <p class="text-xs text-orange-600 mt-0.5">
                    Terbayar: <span class="font-semibold">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</span>
                    · Sisa: <span class="font-semibold">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                </p>
                @if ($terminBerikutnya)
                <a href="{{ route('orangtua.pembayaran.index') }}"
                    class="inline-block mt-2 text-xs font-semibold text-white bg-orange-500 hover:bg-orange-600 px-3 py-1.5 rounded-sm transition">
                    Bayar Termin Berikutnya →
                </a>
                @endif
            </div>
        </div>

        {{-- Menunggu NIS --}}
        @elseif ($pendaftaran->status === 'menunggu_nis')
        <div class="flex items-center gap-3 bg-purple-50 border border-purple-200 rounded-xl px-4 py-4 md:px-5">
            <x-heroicon-o-check-circle class="w-8 h-8 text-green-500 shrink-0" />
            <div>
                <p class="text-sm font-semibold text-purple-800">
                    Pembayaran {{ $pendaftaran->calonMurid->nama_lengkap }} telah lunas!
                </p>
                <p class="text-xs text-purple-600 mt-0.5">
                    Menunggu NIS dari sekolah. Kami sedang memproses nomor induk siswa putra/putri Anda.
                </p>
            </div>
        </div>

        {{-- Aktif / Diterima --}}
        @elseif ($pendaftaran->status === 'aktif')
        <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-sm px-4 py-4 md:px-5">
            <x-heroicon-o-check-circle class="w-6 h-6 text-green-500 shrink-0 mt-0.5" />
            <div>
                <p class="text-sm font-semibold text-green-800">
                    Selamat! {{ $pendaftaran->calonMurid->nama_lengkap }} resmi diterima sebagai murid TK Indria Bogor.
                </p>
                <p class="text-xs text-green-600 mt-0.5">
                    NIS: <span class="font-bold font-mono text-green-700">{{ $pendaftaran->calonMurid->nis }}</span>
                    · Tahun Ajaran {{ $tahunAjaran->nama }}
                </p>
            </div>
        </div>

        @endif

    </div>
    @endif

    <div class="border-t border-gray-100 my-4 pt-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Biaya Pendaftaran</h3>
        
        @if ($tahunAjaran && $tahunAjaran->rincianBiaya->count() > 0)
        <div class="bg-white border border-gray-100 rounded-sm shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 font-semibold text-gray-700">Komponen Biaya</th>
                        <th class="px-5 py-3 font-semibold text-gray-700 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($tahunAjaran->rincianBiaya as $rincian)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $rincian->nama_komponen }}</p>
                            @if($rincian->keterangan)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $rincian->keterangan }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-700">
                            {{ $rincian->nominal_format }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-100">
                    @php
                        $adaDiskon = $gelombangAktif && $gelombangAktif->diskon_persen > 0;
                    @endphp
                    <tr>
                        <td class="px-5 py-3 font-semibold {{ $adaDiskon ? 'text-gray-500' : 'text-gray-800' }}">Total Biaya</td>
                        <td class="px-5 py-3 text-right font-bold {{ $adaDiskon ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                            Rp {{ number_format($tahunAjaran->rincianBiaya->sum('nominal'), 0, ',', '.') }}
                        </td>
                    </tr>
                    @if ($adaDiskon)
                    <tr>
                        <td class="px-5 py-3 font-semibold text-green-700">
                            Total Biaya Saat Ini ({{ $gelombangAktif->nama }} - Diskon {{ $gelombangAktif->diskon_persen }}%)
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-green-700 text-base">
                            {{ $gelombangAktif->total_biaya_format }}
                        </td>
                    </tr>
                    @elseif ($gelombangAktif)
                    <tr>
                        <td class="px-5 py-3 font-semibold text-[#0068C4]">
                            Total Biaya Saat Ini ({{ $gelombangAktif->nama }})
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-[#0068C4] text-base">
                            {{ $gelombangAktif->total_biaya_format }}
                        </td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
        @else
        <div class="bg-gray-50 border border-gray-100 rounded-sm p-4 text-center">
            <p class="text-sm text-gray-500">Informasi rincian biaya belum tersedia untuk tahun ajaran ini.</p>
        </div>
        @endif
    </div>

    {{-- Alur Pendaftaran --}}
    <div class="flex flex-col items-start mt-6 gap-4">
        <p class="text-xl px-2 text-gray-600 font-semibold mb-2">Alur Pendaftaran</p>
        <img src="{{ asset('images/gambar4.png') }}" alt="Alur Pendaftaran" class="w-full opacity-80 rounded-2xl shadow-sm">
    </div>
</div>

@endsection