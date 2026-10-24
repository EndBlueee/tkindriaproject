<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\CalonMurid;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class OrangTuaDashboardController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::aktif();
        $gelombangAktif = $tahunAjaran?->gelombangAktif();

        /*
         * Cek apakah user sedang membuat anak baru.
         *
         * Jika true, dashboard sengaja dibuat kosong
         * sampai user mengisi biodata anak.
         */
        $sedangTambahAnak = session('adding_new_child', false);

        $calonMurid = null;
        $pendaftaran = null;

        if (! $sedangTambahAnak) {

            $calonMurid = $this->calonMuridAktif();

            // Ambil pendaftaran berdasarkan anak aktif
            if ($tahunAjaran && $calonMurid) {

                $pendaftaran = Pendaftaran::where('user_id', Auth::id())
                    ->where('calon_murid_id', $calonMurid->id)
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->with([
                        'calonMurid',
                        'gelombang',
                        'pembayaran'
                    ])
                    ->latest()
                    ->first();
            }
        }

        return view('orangtua.dashboard.index', compact(
            'tahunAjaran',
            'gelombangAktif',
            'pendaftaran',
            'calonMurid',
            'sedangTambahAnak'
        ));
    }

    // ── Helper: Ambil Calon Murid Aktif ───────────────────────────────

    private function calonMuridAktif(): ?CalonMurid
    {
        $userId = Auth::id();

        // Jika ada anak aktif di session
        $activeId = session('active_calon_murid_id');

        if ($activeId) {

            $calonMurid = CalonMurid::where('id', $activeId)
                ->where('user_id', $userId)
                ->first();

            if ($calonMurid) {
                return $calonMurid;
            }

            session()->forget('active_calon_murid_id');
        }

        // Jika belum ada anak aktif, ambil anak terakhir
        $calonMurid = CalonMurid::where('user_id', $userId)
            ->latest('id')
            ->first();

        if ($calonMurid) {
            session([
                'active_calon_murid_id' => $calonMurid->id
            ]);
        }

        return $calonMurid;
    }
}