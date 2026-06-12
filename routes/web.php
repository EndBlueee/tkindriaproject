<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminPersyaratanDokumenController;
use App\Http\Controllers\Admin\AdminRincianBiayaController;
use App\Http\Controllers\Admin\AdminTahunAjaranController;
use App\Http\Controllers\Admin\AdminPendaftaranController;
use App\Http\Controllers\Admin\AdminPembayaranController;
use App\Http\Controllers\Admin\AdminCalonMuridController;
use App\Http\Controllers\Admin\AdminGelombangController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminNotifikasiController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrangTua\OrangTuaDashboardController;
use App\Http\Controllers\OrangTua\OrangTuaPendaftaranController;
use App\Http\Controllers\OrangTua\OrangTuaPembayaranController;
use App\Http\Controllers\OrangTua\OrangTuaProfileController;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// ── Auth ──────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Portal Admin ──────────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard.index');

        // ── Manajemen User ────────────────────────────────────────────
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/tambah', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::post('/{user}/toggle-aktif', [AdminUserController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        // ── Tahun Ajaran ──────────────────────────────────────────────
        Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
            Route::get('/', [AdminTahunAjaranController::class, 'index'])->name('index');
            Route::get('/tambah', [AdminTahunAjaranController::class, 'create'])->name('create');
            Route::post('/', [AdminTahunAjaranController::class, 'store'])->name('store');
            Route::get('/{tahunAjaran}/edit', [AdminTahunAjaranController::class, 'edit'])->name('edit');
            Route::put('/{tahunAjaran}', [AdminTahunAjaranController::class, 'update'])->name('update');
            Route::post('/{tahunAjaran}/aktifkan', [AdminTahunAjaranController::class, 'aktifkan'])->name('aktifkan');
            Route::delete('/{tahunAjaran}', [AdminTahunAjaranController::class, 'destroy'])->name('destroy');
        });

        // ── Gelombang ─────────────────────────────────────────────────
        Route::prefix('gelombang')->name('gelombang.')->group(function () {
            Route::get('/', [AdminGelombangController::class, 'index'])->name('index');
            Route::get('/tambah', [AdminGelombangController::class, 'create'])->name('create');
            Route::post('/', [AdminGelombangController::class, 'store'])->name('store');
            Route::get('/{gelombang}/edit', [AdminGelombangController::class, 'edit'])->name('edit');
            Route::put('/{gelombang}', [AdminGelombangController::class, 'update'])->name('update');
            Route::delete('/{gelombang}', [AdminGelombangController::class, 'destroy'])->name('destroy');
        });

        // ── Rincian Biaya ─────────────────────────────────────────────
        Route::prefix('rincian-biaya')->name('rincian-biaya.')->group(function () {
            Route::get('/', [AdminRincianBiayaController::class, 'index'])->name('index');
            Route::get('/tambah', [AdminRincianBiayaController::class, 'create'])->name('create');
            Route::post('/', [AdminRincianBiayaController::class, 'store'])->name('store');
            Route::get('/{rincianBiaya}/edit', [AdminRincianBiayaController::class, 'edit'])->name('edit');
            Route::put('/{rincianBiaya}', [AdminRincianBiayaController::class, 'update'])->name('update');
            Route::delete('/{rincianBiaya}', [AdminRincianBiayaController::class, 'destroy'])->name('destroy');
        });

        // ── Persyaratan Dokumen ───────────────────────────────────────
        Route::prefix('persyaratan-dokumen')->name('persyaratan-dokumen.')->group(function () {
            Route::get('/', [AdminPersyaratanDokumenController::class, 'index'])->name('index');
            Route::get('/tambah', [AdminPersyaratanDokumenController::class, 'create'])->name('create');
            Route::post('/', [AdminPersyaratanDokumenController::class, 'store'])->name('store');
            Route::get('/{persyaratanDokumen}/edit', [AdminPersyaratanDokumenController::class, 'edit'])->name('edit');
            Route::put('/{persyaratanDokumen}', [AdminPersyaratanDokumenController::class, 'update'])->name('update');
            Route::delete('/{persyaratanDokumen}', [AdminPersyaratanDokumenController::class, 'destroy'])->name('destroy');
        });

        // ── Pendaftaran ───────────────────────────────────────────────
        Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
            Route::get('/', [AdminPendaftaranController::class, 'index'])->name('index');
            Route::get('/tambah', [AdminPendaftaranController::class, 'create'])->name('create');
            Route::post('/', [AdminPendaftaranController::class, 'store'])->name('store');
            Route::get('/{pendaftaran}/edit', [AdminPendaftaranController::class, 'edit'])->name('edit');
            Route::put('/{pendaftaran}', [AdminPendaftaranController::class, 'update'])->name('update');
            Route::get('/{pendaftaran}', [AdminPendaftaranController::class, 'show'])->name('show');
            Route::post('/{pendaftaran}/verifikasi', [AdminPendaftaranController::class, 'verifikasi'])->name('verifikasi');
            Route::post('/{pendaftaran}/revisi', [AdminPendaftaranController::class, 'revisi'])->name('revisi');
        });

        // ── Pembayaran Admin ──────────────────────────────────────────
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            // List semua pembayaran
            Route::get('/', [AdminPembayaranController::class, 'index'])
                ->name('index');
                 Route::get('/create', [AdminPembayaranController::class, 'create'])->name('create');
            Route::post('/', [AdminPembayaranController::class, 'store'])->name('store');
            // Detail pembayaran per pendaftaran
            Route::get('/{pendaftaran}', [AdminPembayaranController::class, 'show'])
                ->name('show');
            // Catat pembayaran offline
            Route::post('/{pembayaran}/catat', [AdminPembayaranController::class, 'catat'])
                ->name('catat');
        });

        // ── Calon Murid ───────────────────────────────────────────────
        Route::prefix('calon-murid')->name('calon-murid.')->group(function () {
            Route::get('/', [AdminCalonMuridController::class, 'index'])
                ->name('index');
            Route::post('/{calonMurid}/input-nis', [AdminCalonMuridController::class, 'inputNis'])
                ->name('input-nis');
        });

        // ── Laporan ───────────────────────────────────────────────────
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/pendaftar', [AdminLaporanController::class, 'pendaftar'])->name('pendaftar');
            Route::get('/pendaftar/pdf', [AdminLaporanController::class, 'pendaftarPdf'])->name('pendaftar.pdf');
            Route::get('/pembayaran', [AdminLaporanController::class, 'pembayaran'])->name('pembayaran');
            Route::get('/pembayaran/pdf', [AdminLaporanController::class, 'pembayaranPdf'])->name('pembayaran.pdf');
        });

        // Di dalam group admin
Route::post('/notifikasi/baca-semua', [AdminNotifikasiController::class, 'bacaSemua'])
    ->name('notifikasi.baca-semua');
Route::get('/notifikasi/{notifikasi}/baca', [AdminNotifikasiController::class, 'bacaSatu'])
    ->name('notifikasi.baca-satu');
    });

// ── Portal Orang Tua ──────────────────────────────────────────────────
Route::prefix('pendaftar')
    ->name('orangtua.')
    ->middleware(['auth', 'role:orangtua'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [OrangTuaDashboardController::class, 'index'])
            ->name('dashboard.index');

        // ── Formulir Pendaftaran ──────────────────────────────────────
        Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
            Route::get('/', [OrangTuaPendaftaranController::class, 'index'])->name('index');
            Route::get('/murid', [OrangTuaPendaftaranController::class, 'murid'])->name('murid');
            Route::post('/murid', [OrangTuaPendaftaranController::class, 'simpanMurid'])->name('simpan-murid');
            Route::get('/ortu', [OrangTuaPendaftaranController::class, 'orangTua'])->name('ortu');
            Route::post('/ortu', [OrangTuaPendaftaranController::class, 'simpanOrangTua'])->name('simpan-ortu');
            Route::get('/dokumen', [OrangTuaPendaftaranController::class, 'dokumen'])->name('dokumen');
            Route::post('/dokumen', [OrangTuaPendaftaranController::class, 'simpanDokumen'])->name('simpan-dokumen');
            Route::post('/submit', [OrangTuaPendaftaranController::class, 'submit'])->name('submit');
            Route::get('/detail', [OrangTuaPendaftaranController::class, 'detail'])->name('detail');
            Route::get('/revisi', [OrangTuaPendaftaranController::class, 'revisi'])->name('revisi');
            Route::post('/revisi', [OrangTuaPendaftaranController::class, 'simpanRevisi'])->name('simpan-revisi');
        });

        // ── Pembayaran Orang Tua ──────────────────────────────────────
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/', [OrangTuaPembayaranController::class, 'index'])->name('index');
            Route::post('/pilih', [OrangTuaPembayaranController::class, 'pilih'])->name('pilih');
            Route::get('/{pembayaran}/bayar', [OrangTuaPembayaranController::class, 'bayar'])->name('bayar');
            Route::get('/{pembayaran}/status', [OrangTuaPembayaranController::class, 'statusJson'])->name('status');
            Route::post('/{pembayaran}/cek-status', [OrangTuaPembayaranController::class, 'cekStatus'])->name('cek-status');
            Route::get('/{pembayaran}/selesai', [OrangTuaPembayaranController::class, 'selesai'])->name('selesai');
        });

        // ── Profil ────────────────────────────────────────────────────
        Route::get('/profile', [OrangTuaProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [OrangTuaProfileController::class, 'update'])->name('profile.update');

    });

    // ── Webhook Midtrans ──────────────────────────────────────────────────
// Tidak pakai middleware auth — Midtrans kirim notifikasi dari server mereka
Route::post('/midtrans/notifikasi', [MidtransController::class, 'notifikasi'])
    ->name('midtrans.notifikasi');
