<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalonMurid extends Model
{
    protected $table = 'calon_murid';
 
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nama_panggilan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'anak_ke',
        'jumlah_saudara',
        'nis',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // ── Accessor ──────────────────────────────────────────────────────
    // Jenis kelamin lengkap
    // Contoh: 'L' → 'Laki-laki', 'P' → 'Perempuan'
    public function getJenisKelaminLengkapAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
 
    // Tempat dan tanggal lahir digabung
    // Contoh: "Bogor, 10 Januari 2020"
    public function getTempatTanggalLahirAttribute(): string
    {
        return $this->tempat_lahir . ', ' . $this->tanggal_lahir->translatedFormat('d F Y');
    }
 
    // Usia anak dalam tahun
    // Contoh: 5 tahun
    public function getUsiaAttribute(): string
    {
        return $this->tanggal_lahir->age . ' tahun';
    }
 
    // ── Helper ────────────────────────────────────────────────────────
    // Cek apakah murid sudah punya NIS (sudah aktif)
    public function sudahAktif(): bool
    {
        return $this->nis !== null;
    }

    // ── Relasi ────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
 
    // Relasi 1-to-1 ke orang tua
    public function orangTua()
    {
        return $this->hasOne(OrangTua::class, 'calon_murid_id');
    }
 
    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'calon_murid_id');
    }
 
    // Ambil pendaftaran aktif (tahun ajaran yang sedang berjalan)
    public function pendaftaranAktif(): ?Pendaftaran
    {
        $tahunAjaran = TahunAjaran::aktif();
        if (! $tahunAjaran) return null;
 
        return $this->pendaftaran()
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->first();
    }
}
