@extends('layouts.orangtua')

@section('title', 'Detail Pendaftaran')

@section('main')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Pendaftaran</h1>
            <p class="text-sm text-gray-500 mt-1">No. Pendaftaran: <span class="font-semibold text-gray-700">{{ $pendaftaran->nomor_daftar }}</span></p>
        </div>


    </div>
    <div class="flex items-center justify-between mb-6 p-5  bg-[#0068C4] rounded-2xl text-white">
        <div class="flex items-center gap-3">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                <x-heroicon-s-user class="w-8 h-8 text-white/80" />
            </div>
            <div class="flex flex-col">
                <p class="text-xs text-blue-200 font-semibold mb-0.5">CALON MURID</p>
                <p class="text-xl md:text-2xl font-bold capitalize">{{ $pendaftaran->calonMurid->nama_panggilan ?? $pendaftaran->calonMurid->nama_lengkap }}</p>
                <p class="text-sm text-blue-100 font-mono">{{ $pendaftaran->nomor_daftar }}</p>
            </div>
        </div>
        <div class="flex flex-col items-end gap-1">
            {{-- Badge Status --}}
            <span class="inline-block px-3 py-1 rounded-lg text-xs font-semibold
        {{ $pendaftaran->status === 'menunggu_verifikasi' ? 'bg-yellow-100 text-yellow-700' : '' }}
        {{ $pendaftaran->status === 'perlu_revisi' ? 'bg-red-100 text-red-700' : '' }}
        {{ $pendaftaran->status === 'menunggu_pembayaran' ? 'bg-blue-100 text-blue-700' : '' }}
        {{ $pendaftaran->status === 'cicilan_berjalan' ? 'bg-orange-100 text-orange-700' : '' }}
        {{ $pendaftaran->status === 'menunggu_nis' ? 'bg-purple-100 text-purple-700' : '' }}
        {{ $pendaftaran->status === 'aktif' ? 'bg-green-100 text-green-700' : '' }}">
                {{ $pendaftaran->status_label }}
            </span>
            <span class="text-xs text-white bg-gray-50/30 px-3 py-1 rounded-lg mt-1">
                Tersubmit {{ $pendaftaran->tgl_submit ? $pendaftaran->tgl_submit->translatedFormat('d F Y, H:i') : '-' }}
            </span>
        </div>
    </div>

    {{-- Notifikasi Perlu Revisi --}}
    @if ($pendaftaran->status === 'perlu_revisi')
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-sm">
        <p class="text-sm font-semibold text-red-700 mb-1">Dokumen perlu diperbaiki</p>
        <p class="text-sm text-red-600">{{ $pendaftaran->catatan_admin }}</p>
        <a href="{{ route('orangtua.pendaftaran.revisi') }}"
            class="inline-block mt-3 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-sm transition">
            Upload Ulang Dokumen →
        </a>
    </div>
    @endif

    {{-- Notifikasi Menunggu Pembayaran --}}
    @if ($pendaftaran->status === 'menunggu_pembayaran')
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-sm">
        <p class="text-sm font-semibold text-blue-700 mb-1">Dokumen telah diverifikasi!</p>
        <p class="text-sm text-blue-600">Silakan lakukan pembayaran untuk melanjutkan proses pendaftaran.</p>
        <a href="{{ route('orangtua.pembayaran.index') }}"
            class="inline-block mt-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-sm transition">
            Lakukan Pembayaran →
        </a>
    </div>
    @endif

    {{-- Notifikasi Aktif --}}
    @if ($pendaftaran->status === 'aktif')
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-sm">
        <p class="text-sm font-semibold text-green-700 mb-1">🎉 Selamat! Pendaftaran berhasil!</p>
        <p class="text-sm text-green-600">
            {{ $pendaftaran->calonMurid->nama_lengkap }} resmi diterima di TK Indria.
            NIS: <span class="font-bold">{{ $pendaftaran->calonMurid->nis }}</span>
        </p>
    </div>
    @endif

    <div class="space-y-4">

        {{-- Data Calon Murid --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4 ">
                <div class="bg-blue-50 p-1.5 rounded-xl shadow-sm">
                    <x-heroicon-c-user class="w-6 h-6 text-[#0068C4]" />
                </div>
                <h2 class="text-lg font-bold text-gray-700">Data Calon Murid</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Nama Lengkap</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->nama_lengkap }}</p>
                </div>
                @if ($pendaftaran->calonMurid->nama_panggilan)
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Nama Panggilan</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->nama_panggilan }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Jenis Kelamin</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->jenis_kelamin_lengkap }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Tempat & Tanggal Lahir</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->tempat_tanggal_lahir }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Agama</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->agama }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Usia</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->usia }}</p>
                </div>
                @if ($pendaftaran->calonMurid->anak_ke)
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Anak ke</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->anak_ke }}</p>
                </div>
                @endif
                @if ($pendaftaran->calonMurid->jumlah_saudara !== null)
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Jumlah Saudara</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->jumlah_saudara }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Data Orang Tua --}}
        @if ($pendaftaran->calonMurid->orangTua)
        @php $ortu = $pendaftaran->calonMurid->orangTua; @endphp
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4 ">
                <div class="bg-purple-50 p-1.5 rounded-xl shadow-sm">
                    <x-heroicon-c-user class="w-6 h-6 text-[#a352ff]" />
                </div>
                <h2 class="text-lg font-bold text-gray-700">Data Orang Tua / Wali</h2>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                @if ($ortu->nama_ayah)
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Nama Ayah</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->nama_ayah }}</p>
                </div>
                @endif
                @if ($ortu->pekerjaan_ayah)
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Pekerjaan Ayah</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->pekerjaan_ayah }}</p>
                </div>
                @endif
                @if ($ortu->nama_ibu)
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Nama Ibu</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->nama_ibu }}</p>
                </div>
                @endif
                @if ($ortu->pekerjaan_ibu)
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Pekerjaan Ibu</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->pekerjaan_ibu }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Nomor HP</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->nomor_hp }}</p>
                </div>
                @if ($ortu->nomor_saudara)
                <div>
                    <p class="text-gray-400 text-sm mb-0.5">Nomor Saudara</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->nomor_saudara }}</p>
                </div>
                @endif
                <div class="md:col-span-2">
                    <p class="text-gray-400 text-sm mb-0.5">Alamat</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->alamat }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Dokumen --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4 ">
                <div class="bg-yellow-50 p-1.5 rounded-xl shadow-sm">
                    <x-heroicon-c-document-duplicate class="w-6 h-6 text-[#c47f00]" />
                </div>
                <h2 class="text-lg font-bold text-gray-700">Berkas Persyaratan</h2>
            </div>
            <div class="space-y-3">
                @foreach ($pendaftaran->dokumen as $dok)
                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-sm">
                    <div class="flex items-center gap-3">
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold
                        {{ $dok->status_verifikasi === 'diterima' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $dok->status_verifikasi === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $dok->status_verifikasi === 'menunggu' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                            {{ $dok->status_label }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800 capitalize">{{ $dok->persyaratan->nama_dokumen }}</p>
                            <p class="text-xs text-gray-400">{{ $dok->ukuran_format }}</p>
                        </div>
                    </div>
                    <a href="{{ $dok->url }}" target="_blank"
                        class="text-xs text-blue-600 hover:underline font-medium">
                        Lihat
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Info Gelombang --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4 ">
                <div class="bg-purple-50 p-1.5 rounded-xl shadow-sm">
                    <x-heroicon-s-information-circle class="w-6 h-6 text-[#a352ff]" />
                </div>
                <h2 class="text-lg font-bold text-gray-700">Info Pendaftaran</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Gelombang</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->gelombang?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Tanggal Submit</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->tgl_submit?->translatedFormat('d F Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Total Biaya</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->gelombang?->total_biaya_format ?? '-' }}</p>
                </div>
                @if ($pendaftaran->calonMurid->nis)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">NIS</p>
                    <p class="font-bold text-green-700">{{ $pendaftaran->calonMurid->nis }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection