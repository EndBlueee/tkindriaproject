<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gelombang;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Auto-update status kedaluwarsa
        Pembayaran::where('status', 'menunggu')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->update(['status' => 'kedaluwarsa']);

        $stats = [
            'total_pendaftar'     => Pendaftaran::count(),
            'menunggu_verifikasi' => Pendaftaran::where('status', 'menunggu_verifikasi')->count(),
            'menunggu_nis'        => Pendaftaran::where('status', 'menunggu_nis')->count(),
            'murid_aktif'         => Pendaftaran::where('status', 'aktif')->count(),
        ];

        $ringkasanPembayaran = [
            'total_lunas'     => Pembayaran::where('status', 'lunas')->count(),
            'nominal_terkumpul' => Pembayaran::where('status', 'lunas')->sum('nominal'),
            'menunggu'        => Pembayaran::where('status', 'menunggu')->count(),
            'kedaluwarsa'     => Pembayaran::where('status', 'kedaluwarsa')->count(),
        ];

        $gelombangAktif = Gelombang::with('tahunAjaran')
            ->whereDate('tanggal_buka', '<=', today())
            ->whereDate('tanggal_tutup', '>=', today())
            ->get();

        return view('admin.dashboard.index', compact('stats', 'ringkasanPembayaran', 'gelombangAktif'));
    }
}
