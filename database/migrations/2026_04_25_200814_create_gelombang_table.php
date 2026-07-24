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
        Schema::create('gelombang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();
 
            // Contoh: Gelombang 1, Gelombang 2, Gelombang 3
            $table->string('nama', 50);
 
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');

            // Batas pembayaran untuk gelombang ini
            $table->date('batas_pembayaran')->nullable();
 
            // Diskon dalam persen untuk early bird
            // Contoh: 10 = diskon 10%, 0 = tidak ada diskon
            $table->tinyInteger('diskon_persen')->default(0);
 
            // Urutan tampil
            $table->tinyInteger('urutan')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gelombang');
    }
};
