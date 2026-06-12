<?php

namespace App\Observers;

use App\Models\Notifikasi;
use App\Models\Pendaftaran;

class PendaftaranObserver
{
    /**
     * Handle the Pendaftaran "created" event.
     */

    // Saat pendaftaran baru dibuat (orang tua submit formulir)
    public function created(Pendaftaran $pendaftaran): void
    {
        $pendaftaran->load(['calonMurid', 'gelombang']);

        Notifikasi::create([
            'judul' => 'pendaftar Baru',
            'pesan' => $pendaftaran->calonMurid->nama_lengkap . ' telah mendaftar di ' . ($pendaftaran->gelombang->nama ?? '-'),
            'tipe' => 'pendaftar_baru',
            'url' => '/admin/pendaftaran/' . $pendaftaran->id,
        ]);
    }

    /**
     * Handle the Pendaftaran "updated" event.
     */

    // Saat status berubah ke perlu_revisi → orang tua upload ulang
    public function updated(Pendaftaran $pendaftaran): void
    {
        // Cek jika status berubah dari perlu_revisi ke menunggu_verifikasi
        // artinya orang tua sudah upload ulang dokumen
        if (
            $pendaftaran->isDirty('status') &&
             $pendaftaran->status === 'menunggu_verifikasi' &&
            $pendaftaran->getOriginal('status') === 'perlu_revisi'
        ) {
            $pendaftaran->load(['calonMurid']);

            Notifikasi::create([
                'judul' => 'Dokumen Direvisi',
                'pesan' => $pendaftaran->calonMurid->nama_lengkap .
                           ' telah mengupload ulang dokumen.',
                'tipe'  => 'dokumen_direvisi',
                'url'   => '/admin/pendaftaran/' . $pendaftaran->id,
            ]);
        }
    }

    /**
     * Handle the Pendaftaran "deleted" event.
     */
    public function deleted(Pendaftaran $pendaftaran): void
    {
        //
    }

    /**
     * Handle the Pendaftaran "restored" event.
     */
    public function restored(Pendaftaran $pendaftaran): void
    {
        //
    }

    /**
     * Handle the Pendaftaran "force deleted" event.
     */
    public function forceDeleted(Pendaftaran $pendaftaran): void
    {
        //
    }
}
