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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

             $table->foreignId('pendaftaran_id')
                ->constrained('pendaftaran')
                ->cascadeOnDelete();
 
            // Metode pembayaran
            $table->string('metode', 50);
 
            // Info termin cicilan
            // Contoh: termin_ke=1, total_termin=3 → cicilan ke-1 dari 3
            // total_termin=1 berarti lunas sekaligus
            $table->tinyInteger('termin_ke')->default(1);
            $table->tinyInteger('total_termin')->default(1);
 
            // Nominal pembayaran termin ini
            $table->unsignedBigInteger('nominal');
 
            // Status pembayaran
            $table->enum('status', [
                'menunggu',    // belum dibayar
                'lunas',       // sudah dibayar
                'kedaluwarsa', // expired (khusus Midtrans)
            ])->default('menunggu');
 
            // Tanggal bayar — otomatis (Midtrans) atau input admin (offline)
            $table->timestamp('tanggal_bayar')->nullable();
 
            // Admin yang mencatat (khusus offline)
            $table->foreignId('dicatat_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
 
            // ── Khusus Online (Midtrans) ──────────────────────────────
            // Order ID unik yang kita buat untuk Midtrans
            $table->string('order_id', 100)->nullable()->unique();
 
            // Transaction ID dari Midtrans — setelah pembayaran berhasil
            $table->string('trx_id', 100)->nullable();
 
            // Waktu expired snap token
            $table->timestamp('expired_at')->nullable();

            // Kode bayar dari Midtrans
            // VA → nomor virtual account
            // Alfamart/Indomaret → kode bayar di kasir
            $table->string('kode_bayar', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
