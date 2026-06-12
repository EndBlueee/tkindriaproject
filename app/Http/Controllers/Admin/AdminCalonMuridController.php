<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonMurid;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AdminCalonMuridController extends Controller
{
    // ── List Calon Murid ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        $query = CalonMurid::with([
            'orangTua',
            'pendaftaran' => function ($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran_id', $tahunAjaran?->id)
                  ->with('gelombang', 'pembayaran');
            }
        ])
        ->whereHas('pendaftaran', function ($q) use ($tahunAjaran) {
            $q->where('tahun_ajaran_id', $tahunAjaran?->id)
              ->whereIn('status', ['menunggu_nis', 'aktif']);
        });

        // Filter status
        if ($request->filled('status')) {
            $query->whereHas('pendaftaran', function ($q) use ($request, $tahunAjaran) {
                $q->where('tahun_ajaran_id', $tahunAjaran?->id)
                  ->where('status', $request->status);
            });
        }

        // Cari nama
        if ($request->filled('cari')) {
            $query->where('nama_lengkap', 'like', '%' . $request->cari . '%');
        }

        $calonMurids = $query->orderBy('nama_lengkap')->paginate(15);

        return view('admin.calon-murid.index', compact('calonMurids', 'tahunAjaran'));
    }

    // ── Input NIS ─────────────────────────────────────────────────────

    public function inputNis(Request $request, CalonMurid $calonMurid)
    {
        $request->validate([
            'nis' => 'required|string|max:20|unique:calon_murid,nis',
        ], [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique'   => 'NIS sudah digunakan.',
        ]);

        // Simpan NIS
        $calonMurid->update(['nis' => $request->nis]);

        // Update status pendaftaran jadi aktif
        $tahunAjaran = TahunAjaran::aktif();
        $pendaftaran = $calonMurid->pendaftaran()
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('status', 'menunggu_nis')
            ->first();

        if ($pendaftaran) {
            $pendaftaran->update(['status' => 'aktif']);
        }

        return back()->with('success', 'NIS berhasil disimpan untuk ' . $calonMurid->nama_lengkap . '.');
    }
}