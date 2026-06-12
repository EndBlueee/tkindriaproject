<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class OrangTuaDashboardController extends Controller
{
    public function index()
    {
        $tahunAjaran    = TahunAjaran::aktif();
        $gelombangAktif = $tahunAjaran?->gelombangAktif();

        $pendaftaran = null;
        if ($tahunAjaran) {
            $pendaftaran = Pendaftaran::where('user_id', Auth::id())
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->with(['calonMurid', 'gelombang', 'pembayaran'])
                ->latest()
                ->first();
        }

        return view('orangtua.dashboard.index', compact(
            'tahunAjaran',
            'gelombangAktif',
            'pendaftaran'
        ));
    }
}