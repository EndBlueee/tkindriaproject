<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersyaratanDokumen extends Model
{
    protected $table = 'persyaratan_dokumen';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_dokumen',
        'format_file',
        'maks_ukuran_kb',
        'is_wajib',
        'keterangan',
        'urutan',
    ];

    protected $casts = [
        'is_wajib'      => 'boolean',
        'maks_ukuran_kb' => 'integer',
    ];

    // ── Accessor ──────────────────────────────────────────────────────
    // Format file untuk ditampilkan ke orang tua
    // Contoh: "pdf,jpg,png" → "PDF, JPG, PNG"
    public function getFormatFileTampilAttribute(): string
    {
        return implode(', ', array_map(
            fn($f) => strtoupper(trim($f)),
            explode(',', $this->format_file)
        ));
    }

    // Format ukuran file untuk ditampilkan ke orang tua
    // Contoh: 2048 → "2 MB", 500 → "500 KB"
    public function getMaksUkuranFormatAttribute(): string
    {
        if ($this->maks_ukuran_kb >= 1024) {
            return round($this->maks_ukuran_kb / 1024, 1) . ' MB';
        }
        return $this->maks_ukuran_kb . ' KB';
    }

    // Format accept untuk input file HTML
    // Contoh: "pdf,jpg,png" → ".pdf,.jpg,.png"
    public function getAcceptFormatAttribute(): string
    {
        return implode(',', array_map(
            fn($f) => '.' . trim($f),
            explode(',', $this->format_file)
        ));
    }

    // ── Scope ─────────────────────────────────────────────────────────
    // Urutkan berdasarkan kolom urutan
    public function scopeTerurut($query)
    {
        return $query->orderBy('urutan');
    }

    // Hanya dokumen wajib
    public function scopeWajib($query)
    {
        return $query->where('is_wajib', true);
    }

    // ── Relasi ────────────────────────────────────────────────────────
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'persyaratan_id');
    }
}
