<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianBiaya extends Model
{
    protected $table = 'rincian_biaya';
 
    protected $fillable = [
        'tahun_ajaran_id',
        'nama_komponen',
        'nominal',
        'keterangan',
        'urutan',
    ];
 
    protected $casts = [
        'nominal' => 'integer',
    ];
 
    // ── Accessor ──────────────────────────────────────────────────────
    // Format nominal ke Rupiah
    // Contoh: 1500000 → Rp 1.500.000
    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
 
    // ── Relasi ────────────────────────────────────────────────────────
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
