<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class AdminNotifikasiController extends Controller
{
    // Tandai semua notifikasi sudah dibaca
    public function bacaSemua()
    {
        Notifikasi::belumDibaca()->update(['is_read' => true]);

        return back();
    }

    // Tandai satu notifikasi dibaca, lalu redirect ke URL tujuan
    public function bacaSatu(Notifikasi $notifikasi)
    {
        $notifikasi->update(['is_read' => true]);

        return redirect($notifikasi->url);
    }
}
