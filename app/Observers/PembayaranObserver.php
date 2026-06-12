<?php

namespace App\Observers;

use App\Models\Notifikasi;
use App\Models\Pembayaran;

class PembayaranObserver
{
    /**
     * Handle the Pembayaran "created" event.
     */
    public function created(Pembayaran $pembayaran): void
    {
        //
    }

    /**
     * Handle the Pembayaran "updated" event.
     */

    // Saat status pembayaran berubah jadi lunas
    public function updated(Pembayaran $pembayaran): void
    {
        if (
            $pembayaran->isDirty('status') &&
            $pembayaran->status === 'lunas'
        ) {
            $pembayaran->load(['pendaftaran.calonMurid']);

            $jenis = $pembayaran->total_termin === 1
                ? 'Lunas'
                : 'Cicilan ' . $pembayaran->termin_ke . '/' . $pembayaran->total_termin;

            Notifikasi::create([
                'judul' => 'Pembayaran Masuk',
                'pesan' => $pembayaran->pendaftaran->calonMurid->nama_lengkap .
                           ' — ' . $pembayaran->nominal_format .
                           ' (' . $jenis . ')',
                'tipe'  => 'pembayaran_masuk',
                'url'   => '/admin/pembayaran/' . $pembayaran->pendaftaran_id,
            ]);
        }
    }

    /**
     * Handle the Pembayaran "deleted" event.
     */
    public function deleted(Pembayaran $pembayaran): void
    {
        //
    }

    /**
     * Handle the Pembayaran "restored" event.
     */
    public function restored(Pembayaran $pembayaran): void
    {
        //
    }

    /**
     * Handle the Pembayaran "force deleted" event.
     */
    public function forceDeleted(Pembayaran $pembayaran): void
    {
        //
    }
}
