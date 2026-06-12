@extends('layouts.admin')

@section('title', 'Tambah Pembayaran')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.pembayaran.index') }}" class="hover:text-[#0068C4] transition">Pembayaran</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Tambah Pembayaran</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Pembayaran</h1>
    <p class="text-sm text-gray-500 mt-1">Catat pembayaran untuk pendaftar jalur offline</p>
</div>

<div class="">
    <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">

        <form method="POST" action="{{ route('admin.pembayaran.store') }}" id="form-pembayaran">
            @csrf

            {{-- Pilih Pendaftar --}}
            <div class="mb-4 flex items-center">
                <label for="pendaftaran_id" class="w-1/3 text-sm font-semibold text-gray-700">
                    Pendaftar <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <select name="pendaftaran_id" id="pendaftaran_id"
                    class="w-full border {{ $errors->has('pendaftaran_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    onchange="tampilkanRincian(this)">
                    <option value="">-- Pilih Pendaftar --</option>
                    @foreach ($pendaftarans as $p)
                    <option value="{{ $p->id }}"
                    data-nama="{{ $p->calonMurid->nama_lengkap }}"
                    data-gelombang="{{ $p->gelombang?->nama }}"
                    data-total="{{ $p->gelombang?->totalBiaya() }}"
                    data-total-format="{{ $p->gelombang?->total_biaya_format }}"
                    data-maks="{{ $tahunAjaran->maks_termin }}"
                        {{ old('pendaftaran_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nomor_daftar }} — {{ $p->calonMurid->nama_lengkap }}
                    </option>
                    @endforeach
                </select>
                @error('pendaftaran_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @if ($pendaftarans->isEmpty())
                <p class="text-xs text-gray-400 mt-1">
                    Tidak ada pendaftar offline yang menunggu pembayaran.
                </p>
                @endif
            </div>
            </div>

            {{-- Info Pendaftar (muncul setelah pilih) --}}
            <div id="info-pendaftar" class="mb-4 hidden">
                <div class="w-1/3"></div>
                <div class="w-full p-3 bg-blue-50 border border-blue-100 rounded-sm text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-blue-600">Total Biaya</span>
                        <span class="font-bold text-blue-700" id="info-total">-</span>
                    </div>
                </div>
            </div>

            {{-- Jenis Pembayaran --}}
            <div class="mb-4 flex items-center">
                <label for="jumlah_termin" class="w-1/3 text-sm font-semibold text-gray-700">
                    Jenis Pembayaran <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <select name="jumlah_termin" id="jumlah_termin"
                        class="w-full border {{ $errors->has('jumlah_termin') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">-- Pilih Jenis Pembayaran --</option>
                        <option value="1" {{ old('jumlah_termin') === '1' ? 'selected' : '' }}>Lunas</option>
                        @for ($i = 2; $i <= $tahunAjaran->maks_termin; $i++)
                        <option value="{{ $i }}" {{ old('jumlah_termin') === (string)$i ? 'selected' : '' }}>
                            Cicil {{ $i }}x
                        </option>
                        @endfor
                    </select>
                    @error('jumlah_termin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="mb-4 flex items-center">
                <label for="metode" class="w-1/3 text-sm font-semibold text-gray-700">
                    Metode Pembayaran <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <select name="metode" id="metode"
                        class="w-full border {{ $errors->has('metode') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">-- Pilih Metode --</option>
                        <option value="tunai" {{ old('metode') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ old('metode') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    </select>
                    @error('metode')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tanggal Bayar --}}
            <div class="mb-6 flex items-center">
                <label for="tanggal_bayar" class="w-1/3 text-sm font-semibold text-gray-700">
                    Tanggal Bayar <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar"
                        value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                        class="w-full border {{ $errors->has('tanggal_bayar') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('tanggal_bayar')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.pembayaran.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-sm bg-gray-200 hover:bg-gray-300 transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-[#0068C4] hover:bg-[#005ca3] text-white font-bold py-2.5 px-6 rounded-sm transition text-sm">
                    Simpan Pembayaran
                </button>
            </div>

        </form>
    </div>
</div>

<script>
function tampilkanRincian(select) {
    const option = select.options[select.selectedIndex];
    const info   = document.getElementById('info-pendaftar');
    const total  = document.getElementById('info-total');

    if (select.value) {
        total.textContent = option.dataset.totalFormat;
        info.classList.remove('hidden');
        info.classList.add('flex');
    } else {
        info.classList.add('hidden');
        info.classList.remove('flex');
    }
}

// Trigger saat page load jika ada old value
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('pendaftaran_id');
    if (select.value) tampilkanRincian(select);
});
</script>

@endsection