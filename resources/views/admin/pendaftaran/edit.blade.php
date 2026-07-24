@extends('layouts.admin')

@section('title', 'Edit Pendaftaran')

@section('main')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.pendaftaran.index') }}" class="hover:text-[#0068C4] transition">Pendaftaran</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Edit</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Data Pendaftaran</h1>
    <p class="text-sm text-gray-500 mt-1">
        No. Daftar: <span class="font-semibold text-gray-700 font-mono">{{ $pendaftaran->nomor_daftar }}</span>
        
    </p>
</div>

<form id="form-edit-pendaftaran" method="POST" action="{{ route('admin.pendaftaran.update', $pendaftaran) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="space-y-6">

        {{-- ── Data Calon Murid ─────────────────────────────────── --}}
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Data Calon Murid</h2>

            <div class="">
                <div class="flex items-center">
                    <label for="nama_lengkap" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="w-full">
                        <input type="text" name="nama_lengkap" id="nama_lengkap"
                            value="{{ old('nama_lengkap', $pendaftaran->calonMurid->nama_lengkap) }}"
                            placeholder="Sesuai akta lahir"
                            class="w-full border {{ $errors->has('nama_lengkap') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('nama_lengkap')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center mt-4">
                    <label for="nama_panggilan" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Panggilan <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <div class="w-full">
                        <input type="text" name="nama_panggilan" id="nama_panggilan"
                            value="{{ old('nama_panggilan', $pendaftaran->calonMurid->nama_panggilan) }}"
                            class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>

                <div class="flex items-center mt-4">
                    <label class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <div class="w-full">
                        <div class="flex gap-6 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="L"
                                    {{ old('jenis_kelamin', $pendaftaran->calonMurid->jenis_kelamin) === 'L' ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">Laki-laki</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="P"
                                    {{ old('jenis_kelamin', $pendaftaran->calonMurid->jenis_kelamin) === 'P' ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">Perempuan</span>
                            </label>
                        </div>
                        @error('jenis_kelamin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center mt-4">
                    <label for="agama" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Agama <span class="text-red-500">*</span>
                    </label>
                    <div class="w-full">
                        <select name="agama" id="agama"
                            class="w-full border {{ $errors->has('agama') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">-- Pilih Agama --</option>
                            @foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                            <option value="{{ $agama }}"
                                {{ old('agama', $pendaftaran->calonMurid->agama) === $agama ? 'selected' : '' }}>
                                {{ $agama }}
                            </option>
                            @endforeach
                        </select>
                        @error('agama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center mt-4">
                    <label for="tempat_lahir" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Tempat Lahir <span class="text-red-500">*</span>
                    </label>
                    <div class="w-full">
                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                            value="{{ old('tempat_lahir', $pendaftaran->calonMurid->tempat_lahir) }}"
                            placeholder="Kota lahir"
                            class="w-full border {{ $errors->has('tempat_lahir') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('tempat_lahir')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center mt-4">
                    <label for="tanggal_lahir" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <div class="w-full">
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $pendaftaran->calonMurid->tanggal_lahir->format('Y-m-d')) }}"
                            class="w-full border {{ $errors->has('tanggal_lahir') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('tanggal_lahir')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center mt-4">
                    <label for="anak_ke" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Anak ke <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <div class="w-full">
                        <input type="number" name="anak_ke" min="1"
                            value="{{ old('anak_ke', $pendaftaran->calonMurid->anak_ke) }}"
                            class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>

                <div class="flex items-center mt-4">
                    <label for="jumlah_saudara" class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Jumlah Saudara <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <div class="w-full">
                        <input type="number" name="jumlah_saudara" min="0"
                            value="{{ old('jumlah_saudara', $pendaftaran->calonMurid->jumlah_saudara) }}"
                            class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Data Orang Tua ───────────────────────────────────── --}}
        @php $ortu = $pendaftaran->calonMurid->orangTua; @endphp
        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Data Orang Tua / Wali</h2>

            <div class="">
                <div class="flex items-center mt-4">
                    <label class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">Nama Ayah</label>
                    <div class="w-full">
                        <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $ortu->nama_ayah) }}"
                            placeholder="Nama lengkap ayah"
                            class="w-full border {{ $errors->has('nama_ayah') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('nama_ayah')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <label class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">Pekerjaan Ayah</label>
                    <div class="w-full">
                        <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $ortu->pekerjaan_ayah) }}"
                            class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <label class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">Nama Ibu</label>
                    <div class="w-full">
                        <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $ortu->nama_ibu) }}"
                            placeholder="Nama lengkap ibu"
                            class="w-full border {{ $errors->has('nama_ibu') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <label class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">Pekerjaan Ibu</label>
                    <div class="w-full">
                        <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $ortu->pekerjaan_ibu) }}"
                            class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <label class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <div class="w-full">
                        <input type="text" name="nomor_hp_ortu" value="{{ old('nomor_hp_ortu', $ortu->nomor_hp) }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full border {{ $errors->has('nomor_hp_ortu') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('nomor_hp_ortu')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <label class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Nomor Saudara <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <div class="w-full">
                        <input type="text" name="nomor_saudara" value="{{ old('nomor_saudara', $ortu->nomor_saudara) }}"
                            class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <label class="w-1/3 text-sm font-semibold text-gray-700 mb-1.5">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <div class="w-full">
                        <textarea name="alamat" rows="3"
                            placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota"
                            class="w-full border {{ $errors->has('alamat') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">{{ old('alamat', $ortu->alamat) }}</textarea>
                        @error('alamat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-1">Berkas Persyaratan</h2>
            <p class="text-xs text-gray-400 mb-4">Upload atau ganti dokumen persyaratan pendaftaran</p>

            <div class="space-y-4">
                @foreach ($pendaftaran->tahunAjaran->persyaratanDokumen as $p)
                @php $dok = $pendaftaran->dokumen->firstWhere('persyaratan_id', $p->id); @endphp

                <div class="pb-4 {{ ! $loop->last ? 'border-b border-gray-100' : '' }}">
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
                            <p class="text-xs text-gray-400">{{ $p->format_file_tampil }} · Maks. {{ $p->maks_ukuran_format }}</p>
                        </div>
                        @if ($dok)
                        <a href="{{ $dok->url }}" target="_blank"
                            class="text-xs text-blue-600 hover:underline font-medium shrink-0">
                            Lihat File
                        </a>
                        @endif
                    </div>

                    {{-- Status dokumen jika sudah ada --}}
                    @if ($dok)
                    <div class="mb-2 flex items-center gap-2 text-xs
                        {{ $dok->status_verifikasi === 'diterima' ? 'text-green-600' : 'text-yellow-600' }}">
                        <span>● {{ $dok->status_label }} — {{ $dok->nama_file }}</span>
                    </div>
                    @endif

                    <div class="flex gap-3 items-center">
                        <input type="file" name="dokumen_{{ $p->id }}" accept="{{ $p->accept_format }}"
                            class="flex-1 text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    @error('dokumen_'.$p->id)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach
            </div>
        </div>

    </div>
</form>

{{-- Tombol Kembali & Simpan di paling bawah --}}
<div class="mt-4 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
    <a href="{{ route('admin.pendaftaran.index', $pendaftaran) }}"
        class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-sm bg-gray-100 hover:bg-gray-200 transition">
        Kembali
    </a>
    <button form="form-edit-pendaftaran"
        class="bg-[#0068C4] hover:bg-[#005ca3] text-white text-sm font-semibold px-6 py-2.5 rounded-sm transition">
        Simpan Perubahan
    </button>
</div>

@endsection