<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gelombang extends Model
{
     protected $table = 'gelombang';
 
    protected $fillable = [
        'tahun_ajaran_id',
        'nama',
        'tanggal_buka',
        'tanggal_tutup',
        'diskon_persen',
        'urutan',
    ];
 
    protected $casts = [
        'tanggal_buka'  => 'date',
        'tanggal_tutup' => 'date',
        'diskon_persen' => 'integer',
    ];
 
    // ── Helper ────────────────────────────────────────────────────────
    // Cek apakah gelombang ini sedang buka hari ini
    public function isBuka(): bool
    {
        $today = today();
        return $today->gte($this->tanggal_buka)
            && $today->lte($this->tanggal_tutup);
    }
 
    /// Total biaya setelah diskon — ini yang dibayar orang tua
    // Dihitung otomatis dari rincian biaya tahun ajaran
    public function totalBiaya(): int
    {
        $total = $this->tahunAjaran->rincianBiaya->sum('nominal');
        return (int) ($total * (1 - $this->diskon_persen / 100));
    }
 
    // ── Accessor ─────────────────────────────────────────────────────
    // Format total biaya ke Rupiah
    // Contoh: 2250000 → "Rp 2.250.000"
    public function getTotalBiayaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->totalBiaya(), 0, ',', '.');
    }
 
    // ── Relasi ────────────────────────────────────────────────────────
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
 
    
     public function pendaftaran()
     {
         return $this->hasMany(Pendaftaran::class, 'gelombang_id');
     }
}
