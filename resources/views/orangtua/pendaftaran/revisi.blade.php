@extends('layouts.orangtua')

@section('title', 'Revisi Dokumen')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('orangtua.pendaftaran.detail') }}" class="hover:text-[#0068C4] transition">Detail Pendaftaran</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Revisi Dokumen</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Upload Ulang Dokumen</h1>
    <p class="text-sm text-gray-500 mt-1">No. Pendaftaran: <span class="font-semibold text-gray-700">{{ $pendaftaran->nomor_daftar }}</span></p>
</div>

{{-- Catatan Admin --}}
@if ($pendaftaran->catatan_admin)
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-sm">
    <p class="text-sm font-semibold text-red-700 mb-1">Catatan dari Admin:</p>
    <p class="text-sm text-red-600">{{ $pendaftaran->catatan_admin }}</p>
</div>
@endif

<div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
    <form action="{{ route('orangtua.pendaftaran.simpan-revisi') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @foreach ($persyaratans as $p)
        @php $dok = $dokumenExisting->get($p->id); @endphp

        <div class="mb-6 {{ ! $loop->last ? 'pb-6 border-b border-gray-100' : '' }}">

            {{-- Header --}}
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $p->nama_dokumen }}
                        @if ($p->is_wajib)
                            <span class="text-red-500">*</span>
                        @else
                            <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                        @endif
                    </p>
                    @if ($p->keterangan)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $p->keterangan }}</p>
                    @endif
                </div>
                <div class="text-right text-xs text-gray-400 shrink-0 ml-4">
                    <p>{{ $p->format_file_tampil }}</p>
                    <p>Maks. {{ $p->maks_ukuran_format }}</p>
                </div>
            </div>

            {{-- Dokumen yang sudah ada --}}
            @if ($dok)
            <div class="mb-2 flex items-center gap-3 px-4 py-2.5 rounded-sm border
                {{ $dok->status_verifikasi === 'ditolak' ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 {{ $dok->status_verifikasi === 'ditolak' ? 'text-red-500' : 'text-green-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    @if ($dok->status_verifikasi === 'ditolak')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    @endif
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium {{ $dok->status_verifikasi === 'ditolak' ? 'text-red-700' : 'text-green-700' }} truncate">
                        {{ $dok->nama_file }}
                    </p>
                    <p class="text-xs {{ $dok->status_verifikasi === 'ditolak' ? 'text-red-500' : 'text-green-500' }}">
                        {{ $dok->ukuran_format }} ·
                        {{ $dok->status_verifikasi === 'ditolak' ? 'Ditolak — upload ulang' : 'Sudah diverifikasi' }}
                    </p>
                </div>
                <a href="{{ $dok->url }}" target="_blank"
                    class="text-xs {{ $dok->status_verifikasi === 'ditolak' ? 'text-red-700' : 'text-green-700' }} hover:underline shrink-0">
                    Lihat
                </a>
            </div>
            @endif

            {{-- Area Upload — selalu tampil karena status perlu_revisi --}}
            <label for="dokumen_{{ $p->id }}"
                class="flex items-center justify-center w-full border-2 border-dashed rounded-sm px-4 py-6 cursor-pointer transition
                {{ $errors->has('dokumen_' . $p->id) ? 'border-red-400 bg-red-50' : 'border-gray-300 hover:border-[#0068C4] hover:bg-blue-50' }}">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                    <p class="text-sm text-gray-500">Klik untuk pilih file</p>
                    <p class="text-xs text-gray-400 mt-1" id="nama-file-{{ $p->id }}">Belum ada file dipilih</p>
                </div>
            </label>
            <input type="file"
                name="dokumen_{{ $p->id }}"
                id="dokumen_{{ $p->id }}"
                accept="{{ $p->accept_format }}"
                class="hidden"
                onchange="tampilNama(this, '{{ $p->id }}')">

            @error('dokumen_' . $p->id)
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

        </div>
        @endforeach

        {{-- Tombol --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('orangtua.pendaftaran.detail') }}"
                class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-sm hover:bg-gray-100 transition">
                ← Kembali
            </a>
            <button type="submit"
                class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-8 py-2.5 rounded-sm transition">
                Submit Ulang ke Admin →
            </button>
        </div>

    </form>
</div>

<script>
function tampilNama(input, id) {
    const el = document.getElementById('nama-file-' + id);
    if (input.files && input.files[0]) {
        el.textContent = input.files[0].name;
        el.classList.add('text-green-600', 'font-medium');
        el.classList.remove('text-gray-400');
    }
}
</script>

@endsection