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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pendaftaran_id')
                ->constrained('pendaftaran')
                ->cascadeOnDelete();

            $table->foreignId('persyaratan_id')
                ->constrained('persyaratan_dokumen')
                ->cascadeOnDelete();

            // Info file
            $table->string('nama_file', 255);      // nama asli file
            $table->string('path_file', 255);      // path di storage
            $table->string('tipe_file', 10);       // ekstensi: pdf, jpg, png
            $table->unsignedInteger('ukuran_file'); // ukuran dalam KB

            // Status verifikasi dokumen oleh admin
            $table->enum('status_verifikasi', [
                'menunggu',  // belum dicek admin
                'diterima',  // dokumen valid
                'ditolak',   // dokumen tidak valid, perlu upload ulang
            ])->default('menunggu');

            $table->timestamp('uploaded_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
