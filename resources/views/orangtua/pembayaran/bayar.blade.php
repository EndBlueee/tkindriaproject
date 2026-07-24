@extends('layouts.orangtua')

@section('title', 'Kode Pembayaran')

@section('main')



<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Kode Pembayaran</h1>
    <p class="text-sm text-gray-500 mt-1">Selesaikan pembayaran sebelum batas waktu yang ditentukan</p>
</div>

{{-- Notifikasi --}}
@if (session('info'))
<div class="w-full mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-sm text-sm text-yellow-700">
    {{ session('info') }}
</div>
@endif
@if (session('error'))
<div class="w-full mb-4 p-3 bg-red-50 border border-red-200 rounded-sm text-sm text-red-700">
    {{ session('error') }}
</div>
@endif

<div class="w-full">
    <div class="space-y-3 overflow-hidden">

        {{-- Header --}}
        <div class="bg-[#0068C4] px-6 py-4">
            <div class="grid grid-cols-2 gap-4 text-white text-xs">
                <div>
                    <p class="opacity-70 mb-0.5">NO. INVOICE</p>
                    <p class="font-mono font-semibold">{{ $pembayaran->order_id }}</p>
                </div>
                <div class="text-right">
                    <p class="opacity-70 mb-0.5">METODE PEMBAYARAN</p>
                    <div class="inline-flex items-center gap-2 bg-white ml-auto p-2 rounded-sm">
                        <img src="{{ asset('images/bank/' . str_replace('_va', '', $pembayaran->metode) . '.png') }}"
                            alt="{{ $pembayaran->metode_label }}"
                            class="h-3 md:h-4 object-contain bg-white rounded px-1">
                        <span class="font-semibold text-[#0068C4]">{{ $pembayaran->metode_label }}</span>
                    </div>
                </div>
                <div>
                    <p class="opacity-70 mb-0.5">TGL. TRANSAKSI</p>
                    <p class="font-semibold">{{ $pembayaran->created_at->translatedFormat('d M Y H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="opacity-70 mb-0.5">BATAS BAYAR</p>
                    @if ($pembayaran->status === 'menunggu' && ! $pembayaran->isExpired())
                    <p class="font-bold text-yellow-300 text-lg tracking-wider" id="countdown-timer">02:00</p>
                    <p class="text-[10px] text-blue-200 mt-0.5">{{ $pembayaran->expired_at?->translatedFormat('d M Y H:i') }}</p>
                    @else
                    <p class="font-semibold {{ $pembayaran->isExpired() ? 'text-red-300' : 'text-yellow-300' }}">
                        {{ $pembayaran->expired_at?->translatedFormat('d M Y H:i') ?? '-' }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5 bg-white">

            {{-- Ditujukan Kepada --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Ditujukan Kepada</p>
                <p class="font-semibold text-gray-800">
                    {{ $pembayaran->pendaftaran->calonMurid->orangTua->nama_penanggung_jawab ?? '-' }}
                </p>
                <p class="text-sm text-gray-500">
                    No. HP: {{ $pembayaran->pendaftaran->calonMurid->orangTua->nomor_hp ?? '-' }}
                </p>
            </div>

            <hr class="border-gray-100">

            {{-- Kode Bayar --}}
            @if ($pembayaran->status === 'menunggu' && $pembayaran->kode_bayar && ! $pembayaran->isExpired())
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    @if ($pembayaran->isVirtualAccount()) Nomor Virtual Account
                    @else Kode Pembayaran
                    @endif
                </p>
                <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-sm px-4 py-3">
                    <span class="font-mono font-bold text-xl text-gray-800 tracking-widest" id="kode-bayar">
                        {{ $pembayaran->kode_bayar }}
                    </span>
                    <button onclick="salinKode()"
                        class="text-xs text-[#0068C4] hover:text-blue-700 font-semibold px-3 py-1.5 border border-[#0068C4] rounded-sm transition">
                        Salin
                    </button>
                </div>
                @if ($pembayaran->isVirtualAccount())
                <p class="text-xs text-gray-400 mt-1">
                    Transfer ke nomor VA di atas melalui ATM, Mobile Banking, atau Internet Banking {{ $pembayaran->nama_bank }}
                </p>
                @else
                <p class="text-xs text-gray-400 mt-1">
                    Tunjukkan kode ini kepada kasir {{ $pembayaran->metode_label }}
                </p>
                @endif
            </div>
            
            <hr class="border-gray-100">
            @endif
        </div>

            {{-- Rincian Tagihan --}}
            <div class="p-6 space-y-5 bg-white border-t border-gray-100">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Rincian Tagihan</p>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left pb-2 text-gray-500 font-medium">Keterangan</th>
                            <th class="text-right pb-2 text-gray-500 font-medium">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-800">
                                @if ($pembayaran->total_termin === 1)
                                    Pembayaran Lunas PMB
                                @else
                                    Cicilan {{ $pembayaran->termin_ke }}/{{ $pembayaran->total_termin }} PMB
                                @endif
                                <p class="text-xs text-gray-400">
                                    {{ $pembayaran->pendaftaran->calonMurid->nama_lengkap }}
                                </p>
                            </td>
                            <td class="py-3 text-right font-medium text-gray-800">
                                {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="pt-3 font-bold text-gray-800">Total Bayar</td>
                            <td class="pt-3 text-right font-bold text-lg text-[#0068C4]">
                                {{ $pembayaran->nominal_format }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

            {{-- Status Lunas --}}
            @if ($pembayaran->status === 'lunas')
            <div class="bg-green-50 border border-green-200 rounded-sm p-4 text-center">
                <p class="text-green-700 font-semibold text-sm">✅ Pembayaran Lunas</p>
                <p class="text-green-600 text-xs mt-1">
                    Dibayar pada {{ $pembayaran->tanggal_bayar?->translatedFormat('d F Y, H:i') }}
                </p>
            </div>

            {{-- Status Kedaluwarsa --}}
            @elseif ($pembayaran->isExpired())
            <div class="bg-red-50 border border-red-200 rounded-sm p-4 text-center">
                <p class="text-red-700 font-semibold text-sm">❌ Kode Pembayaran Kedaluwarsa</p>
                <p class="text-red-600 text-xs mt-1">Silakan pilih metode pembayaran ulang</p>
            </div>

            {{-- Status Menunggu --}}
            @else
            <div class="bg-blue-50 border border-blue-100 rounded-sm p-3">
                <p class="text-xs text-blue-700">
                    <span class="font-semibold">Catatan:</span>
                    Selesaikan pembayaran sebelum
                    <span class="font-semibold">
                        {{ $pembayaran->expired_at?->translatedFormat('d F Y, H:i') }}
                    </span>.
                </p>
            </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="flex flex-col gap-2">

                @if ($pembayaran->status !== 'lunas')

                {{-- Ganti Metode --}}
                <a href="{{ route('orangtua.pembayaran.index') }}?ganti=1"
                    class="w-full text-center bg-white border border-[#0068C4] text-[#0068C4] hover:bg-blue-50 font-semibold py-2.5 rounded-sm transition text-sm">
                    {{ $pembayaran->isExpired() ? 'Pilih Metode Pembayaran Ulang' : 'Ganti Metode Pembayaran' }}
                </a>

                @endif

                <a href="{{ route('orangtua.pendaftaran.detail') }}"
                    class="w-full text-center text-gray-500 hover:text-gray-700 font-medium py-2.5 rounded-sm hover:bg-gray-50 transition text-sm">
                    ← Kembali ke Detail Pendaftaran
                </a>

            </div>

        
    </div>
</div>

<script>
function salinKode() {
    const kode = document.getElementById('kode-bayar').textContent.trim();
    navigator.clipboard.writeText(kode).then(() => {
        alert('Kode berhasil disalin!');
    });
}

@if ($pembayaran->status === 'menunggu' && ! $pembayaran->isExpired())
(function () {
    const url = "{{ route('orangtua.pembayaran.status', $pembayaran) }}";
    const expiredAt = {!! $pembayaran->expired_at ? 'new Date("'.$pembayaran->expired_at->toIso8601String().'").getTime()' : 'null' !!};
    const timerEl = document.getElementById('countdown-timer');

    const interval = setInterval(async function () {

        // Cek lokal secara instan agar langsung reload saat waktu habis
        if (expiredAt && Date.now() > expiredAt) {
            clearInterval(interval);
            window.location.reload();
            return;
        }

        // Update UI Countdown Timer
        if (expiredAt && timerEl) {
            const distance = expiredAt - Date.now();
            if (distance > 0) {
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                timerEl.innerText = minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
            }
        }

        try {
            const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();

            if (data.status === 'lunas') {
                clearInterval(interval);
                window.location.href = data.selesai_url;
            } else if (data.status === 'kedaluwarsa') {
                clearInterval(interval);
                window.location.reload();
            }
        } catch (e) {
            // abaikan error jaringan sementara, coba lagi di interval berikutnya
        }
    }, 1000); // Ubah dari 5000 ke 1000 (1 detik) agar detik berjalan mulus
})();
@endif
</script>

@endsection