@extends('layouts.orangtua')

@section('title',
$tahap === 'murid' ? 'Biodata Calon Murid' :
($tahap === 'ortu' ? 'Data Orang Tua' :
'Upload Dokumen')
)

@section('main')
<div class="max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('orangtua.pendaftaran.index') }}" class="hover:text-[#0068C4] transition">
            Formulir Pendaftaran
        </a>
        <span>/</span>
        <span class="text-gray-700 font-medium">
            @if ($tahap === 'murid') Biodata Calon Murid
            @elseif ($tahap === 'ortu') Data Orang Tua
            @else Upload Dokumen
            @endif
        </span>
    </div>

    <div class="mb-6 ">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">
            @if ($tahap === 'murid') Biodata Calon Murid
            @elseif ($tahap === 'ortu') Data Orang Tua / Wali
            @else Upload Berkas Persyaratan
            @endif
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            @if ($tahap === 'murid')
            Langkah 1 dari 3 — Isi data sesuai akta lahir
            @elseif ($tahap === 'ortu')
            Langkah 2 dari 3 — Data orang tua
            @if(isset($calonMurid))
            <span class="font-medium text-gray-700">{{ $calonMurid->nama_lengkap }}</span>
            @endif
            @else
            Langkah 3 dari 3 — Upload dokumen untuk
            @if(isset($calonMurid))
            <span class="font-medium text-gray-700">{{ $calonMurid->nama_lengkap }}</span>
            @endif
            @endif
        </p>
    </div>

    <div class="bg-white rounded-sm shadow-sm border border-gray-100 p-4 md:p-6">

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- TAHAP 1 — DATA CALON MURID                                    --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        @if ($tahap === 'murid')
        <form action="{{ route('orangtua.pendaftaran.simpan-murid') }}" method="POST">
            @csrf

            <div class="mb-5 flex flex-col md:flex-row md:items-center">
                <label for="nama_lengkap" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <input type="text" name="nama_lengkap" id="nama_lengkap"
                        value="{{ old('nama_lengkap', $calonMurid->nama_lengkap ?? '') }}"
                        placeholder="Sesuai akta lahir"
                        class="w-full border {{ $errors->has('nama_lengkap') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('nama_lengkap')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-5 flex flex-col md:flex-row md:items-center">
                <label for="nama_panggilan" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">
                    Nama Panggilan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <div class="w-full">
                    <input type="text" name="nama_panggilan" id="nama_panggilan"
                        value="{{ old('nama_panggilan', $calonMurid->nama_panggilan ?? '') }}"
                        placeholder="Nama sehari-hari"
                        class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="mb-5 flex flex-col md:flex-row md:items-center">
                <label class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">
                    Jenis Kelamin <span class="text-red-500">*</span>
                </label>
                <div class="w-full flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis_kelamin" value="L"
                            {{ old('jenis_kelamin', $calonMurid->jenis_kelamin ?? '') === 'L' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Laki-laki</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis_kelamin" value="P"
                            {{ old('jenis_kelamin', $calonMurid->jenis_kelamin ?? '') === 'P' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Perempuan</span>
                    </label>
                </div>
                @error('jenis_kelamin')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5 flex flex-col md:flex-row md:items-start">
                <label class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0 md:pt-2">
                    Tempat & Tanggal Lahir <span class="text-red-500">*</span>
                </label>
                <div class="w-full grid grid-cols-2 gap-4">
                    <div>
                        <input type="text" name="tempat_lahir"
                            value="{{ old('tempat_lahir', $calonMurid->tempat_lahir ?? '') }}"
                            placeholder="Kota lahir"
                            class="w-full border {{ $errors->has('tempat_lahir') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('tempat_lahir')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <input type="date" name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', isset($calonMurid) ? $calonMurid->tanggal_lahir->format('Y-m-d') : '') }}"
                            class="w-full border {{ $errors->has('tanggal_lahir') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('tanggal_lahir')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-5 flex flex-col md:flex-row md:items-center">
                <label for="agama" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">
                    Agama <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <select name="agama" id="agama"
                        class="w-full border {{ $errors->has('agama') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">-- Pilih Agama --</option>
                        @foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                        <option value="{{ $agama }}"
                            {{ old('agama', $calonMurid->agama ?? '') === $agama ? 'selected' : '' }}>
                            {{ $agama }}
                        </option>
                        @endforeach
                    </select>
                    @error('agama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-5 flex flex-col md:flex-row md:items-center">
                <label for="anak_ke" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">
                    Anak ke <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <div class="w-full">
                    <input type="number" name="anak_ke"
                        value="{{ old('anak_ke', $calonMurid->anak_ke ?? '') }}"
                        min="1" placeholder="Contoh: 1"
                        class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="mb-6 flex flex-col md:flex-row md:items-center">
                <label for="jumlah_saudara" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">
                    Jumlah Saudara <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <div class="w-full">
                    <input type="number" name="jumlah_saudara"
                        value="{{ old('jumlah_saudara', $calonMurid->jumlah_saudara ?? '') }}"
                        min="0" placeholder="Contoh: 2"
                        class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('orangtua.pendaftaran.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-sm hover:bg-gray-100 transition">
                    ← Kembali
                </a>
                <button type="submit"
                    class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-8 py-2.5 rounded-sm transition">
                    Simpan Data Murid
                </button>
            </div>
        </form>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- TAHAP 2 — DATA ORANG TUA                                      --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        @elseif ($tahap === 'ortu')
        <form action="{{ route('orangtua.pendaftaran.simpan-ortu') }}" method="POST">
            @csrf

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Data Ayah</p>

            <div class="flex flex-col md:flex-row md:items-center mb-4">
                <label for="nama_ayah" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">Nama Ayah</label>
                <div class="w-full">
                    <input type="text" name="nama_ayah"
                        value="{{ old('nama_ayah', $orangTua->nama_ayah ?? '') }}"
                        placeholder="Nama lengkap ayah"
                        class="w-full border {{ $errors->has('nama_ayah') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center mb-4">
                <label for="pekerjaan_ayah" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">Pekerjaan Ayah</label>
                <div class="w-full">
                    <input type="text" name="pekerjaan_ayah"
                        value="{{ old('pekerjaan_ayah', $orangTua->pekerjaan_ayah ?? '') }}"
                        placeholder="Pekerjaan ayah"
                        class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 pt-4 border-t border-gray-100">Data Ibu</p>

            <div class="flex flex-col md:flex-row md:items-center mb-4">
                <label for="nama_ibu" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">Nama Ibu</label>
                <div class="w-full">
                    <input type="text" name="nama_ibu"
                        value="{{ old('nama_ibu', $orangTua->nama_ibu ?? '') }}"
                        placeholder="Nama lengkap ibu"
                        class="w-full border {{ $errors->has('nama_ibu') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center mb-4">
                <label for="pekerjaan_ibu" class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">Pekerjaan Ibu</label>
                <div class="w-full">
                    <input type="text" name="pekerjaan_ibu"
                        value="{{ old('pekerjaan_ibu', $orangTua->pekerjaan_ibu ?? '') }}"
                        placeholder="Pekerjaan ibu"
                        class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            @error('nama_ayah')
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-sm text-sm text-red-600">
                {{ $message }}
            </div>
            @enderror

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 pt-4 border-t border-gray-100">Kontak</p>

            <div class="flex flex-col md:flex-row md:items-center mb-4">
                <label class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">
                    Nomor HP <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <input type="text" name="nomor_hp"
                        value="{{ old('nomor_hp', $orangTua->nomor_hp ?? '') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full border {{ $errors->has('nomor_hp') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('nomor_hp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center mb-4">
                <label class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0">
                    Nomor Saudara <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <div class="w-full">
                    <input type="text" name="nomor_saudara"
                        value="{{ old('nomor_saudara', $orangTua->nomor_saudara ?? '') }}"
                        placeholder="Nomor saudara yang dapat dihubungi"
                        class="w-full border border-gray-300 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 pt-4 border-t border-gray-100">Alamat</p>

            <div class="mb-6 flex flex-col md:flex-row md:items-start">
                <label class="w-full md:w-1/3 text-sm font-semibold text-gray-700 mb-1 md:mb-0 md:pt-2">
                    Alamat Lengkap <span class="text-red-500">*</span>
                </label>
                <div class="w-full">
                    <textarea name="alamat" rows="3"
                        placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten"
                        class="w-full border {{ $errors->has('alamat') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">{{ old('alamat', $orangTua->alamat ?? '') }}</textarea>
                    @error('alamat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('orangtua.pendaftaran.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-sm hover:bg-gray-100 transition">
                    ← Kembali
                </a>
                <button type="submit"
                    class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-8 py-2.5 rounded-sm transition">
                    Simpan Data Orang Tua
                </button>
            </div>
        </form>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- TAHAP 3 — UPLOAD DOKUMEN                                      --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        @elseif ($tahap === 'dokumen')
        <form action="{{ route('orangtua.pendaftaran.simpan-dokumen') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @foreach ($persyaratans as $p)
            @php $sudahAda = isset($dokumenTemp[$p->id]); @endphp

            <div class="mb-6 {{ ! $loop->last ? 'pb-6 border-b border-gray-100' : '' }}">

                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-2 gap-1">
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
                    <div class="text-left sm:text-right text-xs text-gray-400 sm:shrink-0 sm:ml-4">
                        <p>{{ $p->format_file_tampil }}</p>
                        <p>Maks. {{ $p->maks_ukuran_format }}</p>
                    </div>
                </div>

                @if ($sudahAda)
                <div class="mb-2 flex items-center gap-3 bg-green-50 border border-green-200 rounded-sm px-4 py-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="text-xs font-medium text-green-700">File sudah diupload</p>
                </div>
                <p class="text-xs text-gray-400 mb-2">Upload file baru untuk mengganti</p>
                @endif

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
                <input type="file" name="dokumen_{{ $p->id }}" id="dokumen_{{ $p->id }}"
                    accept="{{ $p->accept_format }}" class="hidden"
                    onchange="tampilNama(this, '{{ $p->id }}')">

                @error('dokumen_' . $p->id)
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>
            @endforeach

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('orangtua.pendaftaran.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-sm hover:bg-gray-100 transition">
                    ← Kembali
                </a>
                <button type="submit"
                    class="bg-[#0068C4] hover:bg-blue-700 text-white text-sm font-semibold px-8 py-2.5 rounded-sm transition">
                    Simpan Dokumen
                </button>
            </div>
        </form>

        @endif

    </div>
</div>

@if ($tahap === 'dokumen')
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
@endif

@endsection