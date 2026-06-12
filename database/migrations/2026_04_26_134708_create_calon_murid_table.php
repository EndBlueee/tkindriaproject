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
        Schema::create('calon_murid', function (Blueprint $table) {
            $table->id();

             $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
 
            // Data biodata anak
            $table->string('nama_lengkap', 100);
            $table->string('nama_panggilan', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->enum('agama', [
                'Islam', 'Kristen', 'Katolik',
                'Hindu', 'Buddha', 'Konghucu'
            ]);
            $table->tinyInteger('anak_ke')->nullable();
            $table->tinyInteger('jumlah_saudara')->nullable();
 
            // NIS diisi admin setelah pendaftar berstatus aktif
            // Format bebas sesuai kebijakan sekolah
            $table->string('nis', 20)->nullable()->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calon_murid');
    }
};
