<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tua';
 
    protected $fillable = [
        'calon_murid_id',
        'nama_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'nomor_hp',
        'nomor_saudara',
        'alamat',
    ];

      // ── Accessor ──────────────────────────────────────────────────────
 
    // Nama penanggung jawab utama
    // Prioritas: Ayah → Ibu → Wali
    // Contoh: jika ada ayah → tampil nama ayah
    public function getNamaPenanggungJawabAttribute(): string
    {
        return $this->nama_ayah
            ?? $this->nama_ibu
            ?? '-';
    }
 
    // ── Helper ────────────────────────────────────────────────────────
 
    // Cek apakah ada data ayah
    public function adaAyah(): bool
    {
        return ! empty($this->nama_ayah);
    }
 
    // Cek apakah ada data ibu
    public function adaIbu(): bool
    {
        return ! empty($this->nama_ibu);
    }
 
    // ── Relasi ────────────────────────────────────────────────────────
 
    public function calonMurid()
    {
        return $this->belongsTo(CalonMurid::class, 'calon_murid_id');
    }
}
