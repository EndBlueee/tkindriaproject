<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();

            // Nomor daftar generate saat submit final (bukan saat draft)
            // Format: PMB-2025-001
            $table->string('nomor_daftar', 20)->unique()->nullable();

            // Relasi utama
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('calon_murid_id')
                ->constrained('calon_murid')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();

            // Gelombang saat mendaftar
            $table->foreignId('gelombang_id')
                ->nullable()
                ->constrained('gelombang')
                ->nullOnDelete();

            // Jalur pendaftaran
            $table->enum('jalur', ['online', 'offline'])->default('online');

            // Status alur pendaftaran
            // Status pertama adalah menunggu_verifikasi — dibuat saat submit final
            $table->enum('status', [
                'menunggu_verifikasi',   // baru submit, menunggu admin cek dokumen
                'perlu_revisi',          // admin minta perbaikan dokumen
                'menunggu_pembayaran',   // dokumen OK, menunggu bayar
                'cicilan_berjalan',      // sudah bayar termin pertama (cicil)
                'menunggu_nis',          // sudah lunas, menunggu admin input NIS
                'aktif',                 // NIS sudah diterbitkan, murid resmi
            ])->default('menunggu_verifikasi');

            // Catatan dari admin (alasan revisi, dll)
            $table->text('catatan_admin')->nullable();

            // Timestamp proses
            $table->timestamp('tgl_submit')->nullable();       // saat submit final
            $table->timestamp('tgl_verifikasi')->nullable();   // saat admin verifikasi

            // Admin yang memproses
            $table->foreignId('diproses_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
