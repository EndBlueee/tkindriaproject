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
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->id();

            // Relasi 1-to-1 dengan calon_murid
            $table->foreignId('calon_murid_id')
                ->unique()
                ->constrained('calon_murid')
                ->cascadeOnDelete();
 
            // Data Ayah
            $table->string('nama_ayah', 100)->nullable();
            $table->string('pekerjaan_ayah', 50)->nullable();
 
            // Data Ibu
            $table->string('nama_ibu', 100)->nullable();
            $table->string('pekerjaan_ibu', 50)->nullable();
 
            // Data Wali (jika bukan diasuh orang tua kandung)
            $table->string('nama_wali', 100)->nullable();
            $table->string('hubungan_wali', 50)->nullable();
 
            // Kontak — minimal 1 nomor HP wajib diisi
            $table->string('nomor_hp', 15);

            // Nomor saudara yang dapat dihubungi (opsional)
            $table->string('nomor_saudara', 15)->nullable();
 
            // Alamat
            $table->text('alamat');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orang_tua');
    }
};
