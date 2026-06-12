<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendaftar</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }

        /* Kop Surat */
        .kop { text-align: center; border-bottom: 2px solid #0068C4; padding-bottom: 10px; margin-bottom: 15px; }
        .kop h1 { font-size: 14px; font-weight: bold; color: #0068C4; }
        .kop p { font-size: 9px; color: #666; margin-top: 2px; }

        /* Judul Laporan */
        .judul { text-align: center; margin-bottom: 15px; }
        .judul h2 { font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .judul p { font-size: 9px; color: #666; margin-top: 3px; }

        /* Tabel */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        thead tr { background-color: #0068C4; color: white; }
        th { padding: 6px 8px; text-align: left; font-size: 9px; }
        td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #eee; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }

        /* Footer */
        .footer { margin-top: 30px; text-align: right; }
        .footer p { font-size: 9px; margin-bottom: 40px; }
        .footer .ttd { font-size: 9px; }
        .footer .nama { font-weight: bold; border-top: 1px solid #333; display: inline-block; padding-top: 3px; min-width: 150px; text-align: center; }

        /* Total */
        .total { text-align: right; font-size: 10px; margin-bottom: 10px; }
        .total span { font-weight: bold; }
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
        <h2>Laporan Data Pendaftar</h2>
        <p>Tahun Ajaran {{ $tahunAjaran?->nama ?? '-' }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y') }}</p>
    </div>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Daftar</th>
                <th>Nama Anak</th>
                <th>L/P</th>
                <th>Tgl Lahir</th>
                <th>Nama Orang Tua</th>
                <th>No. HP</th>
                <th>Gelombang</th>
                <th>Jalur</th>
                <th>Status</th>
                <th>Tgl Daftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pendaftarans as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->nomor_daftar }}</td>
                <td>{{ $p->calonMurid->nama_lengkap }}</td>
                <td>{{ $p->calonMurid->jenis_kelamin === 'laki-laki' ? 'L' : 'P' }}</td>
                <td>{{ $p->calonMurid->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $p->calonMurid->orangTua->nama_penanggung_jawab ?? '-' }}</td>
                <td>{{ $p->calonMurid->orangTua->nomor_hp ?? '-' }}</td>
                <td>{{ $p->gelombang?->nama ?? '-' }}</td>
                <td>{{ ucfirst($p->jalur) }}</td>
                <td>{{ $p->status_label }}</td>
                <td>{{ $p->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align: center; color: #999;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Total --}}
    <div class="total">
        Total Pendaftar: <span>{{ $pendaftarans->count() }} orang</span>
    </div>

    {{-- Footer Tanda Tangan --}}
    <div class="footer">
        <p>Bogor, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala Sekolah</p>
        <br><br><br>
        <p class="nama">................................</p>
    </div>

</body>
</html>