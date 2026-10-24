<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\CalonMurid;
use Illuminate\Support\Facades\Auth;

class OrangTuaAnakController extends Controller
{
    // ── Tambah Anak Baru ──────────────────────────────────────────────

    public function tambah()
    {
        /*
         * Tandai bahwa user sedang membuat anak baru.
         *
         * active_calon_murid_id dikosongkan agar sistem
         * tidak mengambil data anak yang sebelumnya aktif.
         */
        session([
            'adding_new_child' => true,
        ]);

        session()->forget('active_calon_murid_id');

        /*
         * Setelah klik "Tambah Anak",
         * user tetap berada di Dashboard.
         *
         * Dashboard nantinya akan membaca
         * adding_new_child = true
         * dan menampilkan kondisi kosong.
         */
        return redirect()
            ->route('orangtua.dashboard.index')
            ->with(
                'success',
                'Silakan pilih menu Formulir Pendaftaran untuk mengisi data anak baru.'
            );
    }


    // ── Ganti Anak Aktif ─────────────────────────────────────────────

    public function switch(CalonMurid $calonMurid)
    {
        /*
         * Pastikan anak yang dipilih memang milik
         * user yang sedang login.
         */
        if ($calonMurid->user_id !== Auth::id()) {
            abort(403);
        }

        /*
         * Anak yang dipilih menjadi anak aktif.
         */
        session([
            'active_calon_murid_id' => $calonMurid->id,
            'adding_new_child'      => false,
        ]);

        /*
         * Kembali ke halaman sebelumnya.
         *
         * Misalnya user sedang di Dashboard,
         * maka kembali ke Dashboard.
         *
         * Kalau sedang di Pembayaran,
         * maka kembali ke Pembayaran.
         */
        return redirect()->back();
    }
}