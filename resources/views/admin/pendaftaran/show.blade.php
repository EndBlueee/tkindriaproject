@extends('layouts.admin')

@section('title', 'Detail Pendaftaran')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.pendaftaran.index') }}" class="hover:text-[#0068C4] transition">Pendaftaran</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">{{ $pendaftaran->nomor_daftar }}</span>
</div>

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Detail Pendaftaran</h1>
        <p class="text-sm text-gray-500 mt-1">
            No. Daftar: <span class="font-semibold text-gray-700 font-mono">{{ $pendaftaran->nomor_daftar }}</span>
            · Jalur: <span class="font-semibold text-gray-700">{{ ucfirst($pendaftaran->jalur) }}</span>
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pendaftaran.index') }}"
            class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-sm bg-gray-100 hover:bg-gray-200 transition">
            Kembali
        </a>
        <a href="{{ route('admin.pendaftaran.edit', $pendaftaran) }}"
            class="text-sm font-semibold px-4 py-2 rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
            Edit Data
        </a>
        <span class="inline-block px-3 py-1.5 rounded-full text-xs font-semibold
            {{ $pendaftaran->status === 'menunggu_verifikasi' ? 'bg-yellow-100 text-yellow-700' : '' }}
            {{ $pendaftaran->status === 'perlu_revisi' ? 'bg-red-100 text-red-700' : '' }}
            {{ $pendaftaran->status === 'menunggu_pembayaran' ? 'bg-blue-100 text-blue-700' : '' }}
            {{ $pendaftaran->status === 'cicilan_berjalan' ? 'bg-orange-100 text-orange-700' : '' }}
            {{ $pendaftaran->status === 'menunggu_nis' ? 'bg-purple-100 text-purple-700' : '' }}
            {{ $pendaftaran->status === 'aktif' ? 'bg-green-100 text-green-700' : '' }}">
            {{ $pendaftaran->status_label }}
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Kolom Kiri (2/3) --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Data Calon Murid --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Data Calon Murid</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Nama Lengkap</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->nama_lengkap }}</p>
                </div>
                @if ($pendaftaran->calonMurid->nama_panggilan)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Nama Panggilan</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->nama_panggilan }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Jenis Kelamin</p>
                    <p class="font-medium text-gray-800">{{ $pendaftaran->calonMurid->jenis_kelamin_lengkap }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Tempat & Tanggal Lahir</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $pendaftaran->calonMurid->tempat_tanggal_lahir }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Agama</p>
                    <p class="font-medium text-gray-800">{{ $pendaftaran->calonMurid->agama }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Usia</p>
                    <p class="font-medium text-gray-800">{{ $pendaftaran->calonMurid->usia }}</p>
                </div>
            </div>
        </div>

        {{-- Data Orang Tua --}}
        @if ($pendaftaran->calonMurid->orangTua)
        @php $ortu = $pendaftaran->calonMurid->orangTua; @endphp
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Data Orang Tua / Wali</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                @if ($ortu->nama_ayah)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Nama Ayah</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->nama_ayah }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Pekerjaan Ayah</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->pekerjaan_ayah ?? '-' }}</p>
                </div>
                @endif
                @if ($ortu->nama_ibu)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Nama Ibu</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->nama_ibu }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Pekerjaan Ibu</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->pekerjaan_ibu ?? '-' }}</p>
                </div>
                @endif
                @if ($ortu->nama_wali)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Nama Wali</p>
                    <p class="font-medium text-gray-800">{{ $ortu->nama_wali }}</p>
                    @if ($ortu->hubungan_wali)
                        <p class="text-xs text-gray-500">{{ $ortu->hubungan_wali }}</p>
                    @endif
                </div>
                @endif
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Nomor HP</p>
                    <p class="font-medium text-gray-800">{{ $ortu->nomor_hp }}</p>
                </div>
                @if ($ortu->nomor_saudara)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Nomor Saudara</p>
                    <p class="font-medium text-gray-800">{{ $ortu->nomor_saudara }}</p>
                </div>
                @endif
                <div class="col-span-2">
                    <p class="text-gray-400 text-xs mb-0.5">Alamat</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $ortu->alamat }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Dokumen --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Berkas Persyaratan</h2>

            @if ($pendaftaran->dokumen->count() > 0)
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
                            <p class="text-sm font-medium text-gray-800">{{ $dok->persyaratan->nama_dokumen }}</p>
                            <p class="text-xs text-gray-400">{{ $dok->ukuran_format }}</p>
                        </div>
                    </div>
                    <a href="{{ $dok->url }}" target="_blank"
                        class="text-xs text-blue-600 hover:underline font-medium shrink-0">
                        Lihat
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400">Belum ada dokumen diupload.</p>
            @endif
        </div>

    </div>

    {{-- Kolom Kanan (1/3) --}}
    <div class="space-y-4">

        {{-- Info Pendaftaran --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Info Pendaftaran</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Gelombang</p>
                    <p class="font-medium text-gray-800">{{ $pendaftaran->gelombang?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Total Biaya</p>
                    <p class="font-medium text-gray-800">{{ $pendaftaran->gelombang?->total_biaya_format ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Tanggal Submit</p>
                    <p class="font-medium text-gray-800">
                        {{ $pendaftaran->tgl_submit?->translatedFormat('d F Y, H:i') ?? '-' }}
                    </p>
                </div>
                @if ($pendaftaran->tgl_verifikasi)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Tanggal Verifikasi</p>
                    <p class="font-medium text-gray-800">
                        {{ $pendaftaran->tgl_verifikasi->translatedFormat('d F Y, H:i') }}
                    </p>
                </div>
                @endif
                @if ($pendaftaran->diprosesOleh)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Diproses Oleh</p>
                    <p class="font-medium text-gray-800">{{ $pendaftaran->diprosesOleh->nama }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Aksi Verifikasi — hanya untuk pendaftaran online --}}
        @if ($pendaftaran->jalur === 'online' && $pendaftaran->status === 'menunggu_verifikasi')
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Verifikasi Dokumen</h2>

            {{-- Approve --}}
            <form method="POST" action="{{ route('admin.pendaftaran.verifikasi', $pendaftaran) }}" class="mb-3">
                @csrf
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-sm transition">
                    ✓ Dokumen Lengkap & Valid
                </button>
            </form>

            {{-- Revisi --}}
            <form method="POST" action="{{ route('admin.pendaftaran.revisi', $pendaftaran) }}">
                @csrf
                <textarea name="catatan_admin" rows="3" required
                    placeholder="Tulis catatan untuk orang tua — dokumen apa yang perlu diperbaiki..."
                    class="w-full border {{ $errors->has('catatan_admin') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition mb-2"></textarea>
                @error('catatan_admin')
                    <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                @enderror
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2.5 rounded-sm transition">
                    ✗ Perlu Revisi Dokumen
                </button>
            </form>
        </div>
        @endif

        {{-- Catatan Admin (jika perlu revisi) --}}
        @if ($pendaftaran->status === 'perlu_revisi' && $pendaftaran->catatan_admin)
        <div class="bg-red-50 border border-red-200 rounded-sm p-4">
            <p class="text-xs font-semibold text-red-700 mb-1">Catatan Revisi:</p>
            <p class="text-sm text-red-600">{{ $pendaftaran->catatan_admin }}</p>
        </div>
        @endif

    </div>

</div>

@endsection