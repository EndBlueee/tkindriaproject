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
        Schema::create('rincian_biaya', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();

            // Contoh: Uang Pangkal, Seragam, Kegiatan, SPP Bulan Pertama
            $table->string('nama_komponen', 100);

            $table->unsignedBigInteger('nominal')->default(0);

            // Keterangan tambahan (opsional)
            $table->string('keterangan', 200)->nullable();

            // Urutan tampil di invoice
            $table->tinyInteger('urutan')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_biaya');
    }
};
