<?php

namespace App\Providers;

use App\Models\CalonMurid;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Observers\CalonMuridObserver;
use App\Observers\PembayaranObserver;
use App\Observers\PendaftaranObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');

        Pendaftaran::observe(PendaftaranObserver::class);
        Pembayaran::observe(PembayaranObserver::class);
        CalonMurid::observe(CalonMuridObserver::class);
    }
}
