<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }

        .kop { text-align: center; border-bottom: 2px solid #0068C4; padding-bottom: 10px; margin-bottom: 15px; }
        .kop h1 { font-size: 14px; font-weight: bold; color: #0068C4; }
        .kop p { font-size: 9px; color: #666; margin-top: 2px; }

        .judul { text-align: center; margin-bottom: 15px; }
        .judul h2 { font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .judul p { font-size: 9px; color: #666; margin-top: 3px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        thead tr { background-color: #0068C4; color: white; }
        th { padding: 6px 8px; text-align: left; font-size: 9px; }
        td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #eee; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }

        .ringkasan { margin-bottom: 15px; border: 1px solid #eee; padding: 8px 12px; }
        .ringkasan p { font-size: 10px; margin-bottom: 3px; }
        .ringkasan .total { font-weight: bold; color: #0068C4; font-size: 11px; }

        .footer { margin-top: 30px; text-align: right; }
        .footer p { font-size: 9px; margin-bottom: 40px; }
        .footer .nama { font-weight: bold; border-top: 1px solid #333; display: inline-block; padding-top: 3px; min-width: 150px; text-align: center; }
    </style>
</head>
<body>

    {{-- Kop Surat --}}
    <div class="kop">
        <h1>TK INDRIA BOGOR</h1>
        <p>Jl. Contoh No. 1, Bogor, Jawa Barat</p>
        <p>Telp: (0251) 123456</p>
    </div>

    {{-- Judul --}}
    <div class="judul">
        <h2>Laporan Data Pembayaran</h2>
        <p>Tahun Ajaran {{ $tahunAjaran?->nama ?? '-' }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y') }}</p>
    </div>

    {{-- Ringkasan --}}
    <div class="ringkasan">
        <p>Total Transaksi: <strong>{{ $pembayarans->count() }}</strong></p>
        <p class="total">Total Terkumpul: Rp {{ number_format($totalLunas, 0, ',', '.') }}</p>
    </div>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Daftar</th>
                <th>Nama Anak</th>
                <th>Gelombang</th>
                <th>Termin</th>
                <th>Metode</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Tgl Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembayarans as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->pendaftaran->nomor_daftar }}</td>
                <td>{{ $p->pendaftaran->calonMurid->nama_lengkap }}</td>
                <td>{{ $p->pendaftaran->gelombang?->nama ?? '-' }}</td>
                <td>{{ $p->termin_ke }}/{{ $p->total_termin }}</td>
                <td>{{ $p->metode_label }}</td>
                <td>{{ $p->nominal_format }}</td>
                <td>{{ $p->status_label }}</td>
                <td>{{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; color: #999;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Bogor, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala Sekolah</p>
        <br><br><br>
        <p class="nama">................................</p>
    </div>

</body>
</html>