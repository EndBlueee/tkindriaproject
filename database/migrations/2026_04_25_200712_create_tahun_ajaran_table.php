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
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();

            // Contoh: 2024/2025
            $table->string('nama', 20)->unique();
 
            // Hanya 1 tahun ajaran yang boleh aktif
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
 
            // Batas akhir pembayaran untuk semua gelombang
            $table->date('batas_akhir_pembayaran')->nullable();
 
            // Jumlah maksimal termin cicilan
            $table->tinyInteger('maks_termin')->default(3);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
