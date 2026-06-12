<?php

namespace App\Observers;

use App\Models\CalonMurid;
use App\Models\Notifikasi;

class CalonMuridObserver
{
    /**
     * Handle the CalonMurid "created" event.
     */
    public function created(CalonMurid $calonMurid): void
    {
        //
    }

    /**
     * Handle the CalonMurid "updated" event.
     */

    // Saat NIS diinput (nis berubah dari null ke ada isinya)
    public function updated(CalonMurid $calonMurid): void
    {
        if (
            $calonMurid->isDirty('nis') &&
            $calonMurid->nis !== null &&
            $calonMurid->getOriginal('nis') === null
        ) {
            Notifikasi::create([
                'judul' => 'NIS Terbit',
                'pesan' => $calonMurid->nama_lengkap .
                           ' telah terdaftar sebagai murid aktif. NIS: ' .
                           $calonMurid->nis,
                'tipe'  => 'nis_terbit',
                'url'   => '/admin/calon-murid',
            ]);
        }
    }

    /**
     * Handle the CalonMurid "deleted" event.
     */
    public function deleted(CalonMurid $calonMurid): void
    {
        //
    }

    /**
     * Handle the CalonMurid "restored" event.
     */
    public function restored(CalonMurid $calonMurid): void
    {
        //
    }

    /**
     * Handle the CalonMurid "force deleted" event.
     */
    public function forceDeleted(CalonMurid $calonMurid): void
    {
        //
    }
}
