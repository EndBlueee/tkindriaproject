@extends('layouts.orangtua')

@section('title', 'Formulir Pendaftaran')

@section('main')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Formulir Pendaftaran</h1>
        <p class="text-sm text-gray-500 mt-1">TK Indria Bogor</p>
    </div>

    @if (! $tahunAjaran || ! $buka)

    {{-- ── Pendaftaran Tutup / Belum Buka ──────────────────────────── --}}
    <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6 md:p-12 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-700 mb-2">Pendaftaran Sedang Tutup</h2>
        <p class="text-sm text-gray-500 max-w-sm">
            Pendaftaran belum dibuka atau sudah ditutup.
            Silakan hubungi sekolah untuk informasi lebih lanjut.
        </p>
    </div>

    @else

    {{-- ── Pendaftaran Buka ─────────────────────────────────────────── --}}
    <p class="text-sm text-gray-500 mb-4">
        Tahun Ajaran {{ $tahunAjaran->nama }} —
        Pendaftaran ditutup {{ $tahunAjaran->gelombangAktif()?->tanggal_tutup->translatedFormat('d F Y') }}
    </p>

    <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 md:p-6 flex flex-col gap-4">

        {{-- Tahap 1 — Data Calon Murid --}}
        <a href="{{ route('orangtua.pendaftaran.murid') }}"
            class="group flex items-center justify-between p-4 md:p-5 rounded-sm border transition-all duration-200
            {{ $status['murid'] ? 'border-green-300 bg-green-50 hover:shadow-md' : 'border-gray-200 bg-gray-50 hover:bg-white hover:border-[#0068C4] hover:shadow-md' }}">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 flex items-center justify-center rounded-full font-bold text-lg border-2 transition-colors duration-200
                    {{ $status['murid'] ? 'bg-green-500 text-white border-green-500' : 'bg-white border-gray-200 text-gray-500 group-hover:bg-[#0068C4] group-hover:border-[#0068C4] group-hover:text-white' }}">
                    {{ $status['murid'] ? '✓' : '1' }}
                </div>
                <div>
                    <span class="font-bold text-gray-800 text-base block group-hover:text-[#0068C4] transition-colors">
                        Biodata Calon Murid
                    </span>
                    <p class="text-sm text-gray-500 mt-0.5">
                        @if ($status['murid'])
                        {{ $status['calon_murid']->nama_lengkap }} · {{ $status['calon_murid']->jenis_kelamin_lengkap }}
                        @else
                        Lengkapi biodata calon murid baru
                        @endif
                    </p>
                    <span class="inline-block mt-1 text-xs font-semibold px-2.5 py-0.5 rounded-full
                        {{ $status['murid'] ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-500' }}">
                        {{ $status['murid'] ? '✓ Sudah diisi' : 'Belum diisi' }}
                    </span>
                </div>
            </div>
            <div class="w-9 h-9 shrink-0 hidden md:flex items-center justify-center rounded-full border transition-colors duration-200
                {{ $status['murid'] ? 'bg-green-100 border-green-200 text-green-600' : 'bg-white border-gray-200 text-gray-400 group-hover:bg-blue-50 group-hover:border-blue-100 group-hover:text-[#0068C4]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </div>
        </a>

        {{-- Tahap 2 — Data Orang Tua --}}
        <a href="{{ route('orangtua.pendaftaran.ortu') }}"
            class="group flex items-center justify-between p-4 md:p-5 rounded-sm border transition-all duration-200
            {{ $status['ortu'] ? 'border-green-300 bg-green-50 hover:shadow-md' : 'border-gray-200 bg-gray-50 hover:bg-white hover:border-[#0068C4] hover:shadow-md' }}">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 flex items-center justify-center rounded-full font-bold text-lg border-2 transition-colors duration-200
                    {{ $status['ortu'] ? 'bg-green-500 text-white border-green-500' : 'bg-white border-gray-200 text-gray-500 group-hover:bg-[#0068C4] group-hover:border-[#0068C4] group-hover:text-white' }}">
                    {{ $status['ortu'] ? '✓' : '2' }}
                </div>
                <div>
                    <span class="font-bold text-gray-800 text-base block group-hover:text-[#0068C4] transition-colors">
                        Data Orang Tua / Wali
                    </span>
                    <p class="text-sm text-gray-500 mt-0.5">
                        @if ($status['ortu'])
                        {{ $status['orang_tua']->nama_penanggung_jawab }} · {{ $status['orang_tua']->nomor_hp }}
                        @else
                        Lengkapi data orang tua calon murid
                        @endif
                    </p>
                    <span class="inline-block mt-1 text-xs font-semibold px-2.5 py-0.5 rounded-full
                        {{ $status['ortu'] ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-500' }}">
                        {{ $status['ortu'] ? '✓ Sudah diisi' : 'Belum diisi' }}
                    </span>
                </div>
            </div>
            <div class="w-9 h-9 shrink-0 hidden md:flex items-center justify-center rounded-full border transition-colors duration-200
                {{ $status['ortu'] ? 'bg-green-100 border-green-200 text-green-600' : 'bg-white border-gray-200 text-gray-400 group-hover:bg-blue-50 group-hover:border-blue-100 group-hover:text-[#0068C4]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </div>
        </a>

        {{-- Tahap 3 — Upload Dokumen --}}
        <a href="{{ route('orangtua.pendaftaran.dokumen') }}"
            class="group flex items-center justify-between p-4 md:p-5 rounded-sm border transition-all duration-200
            {{ $status['dokumen'] ? 'border-green-300 bg-green-50 hover:shadow-md' : 'border-gray-200 bg-gray-50 hover:bg-white hover:border-[#0068C4] hover:shadow-md' }}">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 flex items-center justify-center rounded-full font-bold text-lg border-2 transition-colors duration-200
                    {{ $status['dokumen'] ? 'bg-green-500 text-white border-green-500' : 'bg-white border-gray-200 text-gray-500 group-hover:bg-[#0068C4] group-hover:border-[#0068C4] group-hover:text-white' }}">
                    {{ $status['dokumen'] ? '✓' : '3' }}
                </div>
                <div>
                    <span class="font-bold text-gray-800 text-base block group-hover:text-[#0068C4] transition-colors">
                        Upload Berkas Persyaratan
                    </span>
                    <p class="text-sm text-gray-500 mt-0.5">
                        @if ($status['dokumen'])
                        {{ $status['total_upload'] }} dokumen terupload
                        @else
                        Upload dokumen persyaratan pendaftaran
                        @endif
                    </p>
                    <span class="inline-block mt-1 text-xs font-semibold px-2.5 py-0.5 rounded-full
                        {{ $status['dokumen'] ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-500' }}">
                        {{ $status['dokumen'] ? '✓ Sudah diupload' : 'Belum diupload' }}
                    </span>
                </div>
            </div>
            <div class="w-9 h-9 shrink-0 hidden md:flex items-center justify-center rounded-full border transition-colors duration-200
                {{ $status['dokumen'] ? 'bg-green-100 border-green-200 text-green-600' : 'bg-white border-gray-200 text-gray-400 group-hover:bg-blue-50 group-hover:border-blue-100 group-hover:text-[#0068C4]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </div>
        </a>

        {{-- Tombol Submit --}}
        <div class="pt-2 border-t border-gray-100">
            @if ($status['murid'] && $status['ortu'] && $status['dokumen'])
            <form method="POST" action="{{ route('orangtua.pendaftaran.submit') }}"
                onsubmit="return confirm('Apakah Anda yakin? Data tidak dapat diubah setelah submit.')">
                @csrf

                {{-- Checkbox pernyataan --}}
                <div class="flex items-start gap-3 mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-sm">
                    <input type="checkbox" id="setuju" name="setuju" required
                        class="mt-0.5 w-4 h-4 accent-blue-600 cursor-pointer">
                    <label for="setuju" class="text-sm text-gray-600 cursor-pointer">
                        Saya menyatakan bahwa data yang saya isi adalah benar dan dapat dipertanggungjawabkan.
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-[#0068C4] hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-sm transition text-sm">
                    Submit Pendaftaran →
                </button>
            </form>
            <p class="text-xs text-gray-400 text-center mt-2">
                Setelah submit, data tidak dapat diubah kembali
            </p>
            @else
            <button disabled
                class="w-full bg-gray-200 text-gray-400 font-bold py-3 px-6 rounded-sm text-sm cursor-not-allowed">
                Submit Pendaftaran
            </button>
            <p class="text-xs text-gray-400 text-center mt-2">
                Lengkapi semua tahap di atas untuk dapat melakukan submit
            </p>
            @endif
        </div>

    </div>
</div>

@endif

@endsection