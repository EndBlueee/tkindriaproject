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
        Schema::create('persyaratan_dokumen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();
 
            // Contoh: Akta Lahir, Kartu Keluarga, Foto 3x4
            $table->string('nama_dokumen', 100);
 
            // Format file yang diterima, pisah koma: pdf,jpg,png
            $table->string('format_file', 50)->default('pdf,jpg,png');
 
            // Maksimal ukuran file dalam KB
            $table->unsignedInteger('maks_ukuran_kb')->default(2048); // default 2MB
 
            // Dokumen wajib atau opsional
            $table->boolean('is_wajib')->default(true);
 
            // Keterangan untuk orang tua
            $table->string('keterangan', 200)->nullable();
 
            // Urutan tampil di form upload
            $table->tinyInteger('urutan')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persyaratan_dokumen');
    }
};
