<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gelombang;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminLaporanController extends Controller
{
    // ── Laporan Pendaftar ─────────────────────────────────────────────

    public function pendaftar(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();
        $gelombangs  = Gelombang::where('tahun_ajaran_id', $tahunAjaran?->id)->get();

        $query = Pendaftaran::with(['calonMurid.orangTua', 'gelombang'])
            ->where('tahun_ajaran_id', $tahunAjaran?->id);

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $request->gelombang_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pendaftarans = $query->orderBy('created_at')->get();

        return view('admin.laporan.pendaftar', compact(
            'pendaftarans',
            'tahunAjaran',
            'gelombangs'
        ));
    }

    // ── Export PDF Pendaftar ──────────────────────────────────────────

    public function pendaftarPdf(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        $query = Pendaftaran::with(['calonMurid.orangTua', 'gelombang'])
            ->where('tahun_ajaran_id', $tahunAjaran?->id);

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $request->gelombang_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pendaftarans = $query->orderBy('created_at')->get();

        $pdf = Pdf::loadView('admin.laporan.pendaftar-pdf', compact(
            'pendaftarans',
            'tahunAjaran'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pendaftar-' . now()->format('d-m-Y') . '.pdf');
    }

    // ── Laporan Pembayaran ────────────────────────────────────────────

    public function pembayaran(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();
        $gelombangs  = Gelombang::where('tahun_ajaran_id', $tahunAjaran?->id)->get();

        $query = Pembayaran::with(['pendaftaran.calonMurid', 'pendaftaran.gelombang'])
            ->whereHas('pendaftaran', function ($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran_id', $tahunAjaran?->id);
            });

        if ($request->filled('gelombang_id')) {
            $query->whereHas('pendaftaran', function ($q) use ($request) {
                $q->where('gelombang_id', $request->gelombang_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayarans = $query->orderBy('created_at')->get();
        $totalLunas  = $pembayarans->where('status', 'lunas')->sum('nominal');

        return view('admin.laporan.pembayaran', compact(
            'pembayarans',
            'tahunAjaran',
            'gelombangs',
            'totalLunas'
        ));
    }

    // ── Export PDF Pembayaran ─────────────────────────────────────────

    public function pembayaranPdf(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        $query = Pembayaran::with(['pendaftaran.calonMurid', 'pendaftaran.gelombang'])
            ->whereHas('pendaftaran', function ($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran_id', $tahunAjaran?->id);
            });

        if ($request->filled('gelombang_id')) {
            $query->whereHas('pendaftaran', function ($q) use ($request) {
                $q->where('gelombang_id', $request->gelombang_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayarans = $query->orderBy('created_at')->get();
        $totalLunas  = $pembayarans->where('status', 'lunas')->sum('nominal');

        $pdf = Pdf::loadView('admin.laporan.pembayaran-pdf', compact(
            'pembayarans',
            'tahunAjaran',
            'totalLunas'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pembayaran-' . now()->format('d-m-Y') . '.pdf');
    }
}