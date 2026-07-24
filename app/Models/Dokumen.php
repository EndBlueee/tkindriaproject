<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Dokumen extends Model
{
    protected $table = 'dokumen';
 
    protected $fillable = [
        'pendaftaran_id',
        'persyaratan_id',
        'nama_file',
        'path_file',
        'tipe_file',
        'ukuran_file',
        'status_verifikasi',
        'uploaded_at',
    ];
 
    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

        // ── Accessor ──────────────────────────────────────────────────────
 
    // URL publik file untuk ditampilkan / didownload di view
    // Contoh: storage/dokumen/PMB-2025-001/akta_lahir.pdf
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path_file);
    }
 
    // Format ukuran file
    // Contoh: 1024 → "1 MB", 500 → "500 KB"
    public function getUkuranFormatAttribute(): string
    {
        if ($this->ukuran_file >= 1024) {
            return round($this->ukuran_file / 1024, 1) . ' MB';
        }
        return $this->ukuran_file . ' KB';
    }
 
    // Label status verifikasi
    // Contoh: 'menunggu' → 'Menunggu'
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'menunggu' => 'Menunggu',
            'diterima' => 'Diterima',
            'ditolak'  => 'Ditolak',
            default    => ucfirst($this->status_verifikasi),
        };
    }
 
    // Warna badge status verifikasi untuk Tailwind CSS
    public function getStatusColorAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'menunggu' => 'yellow',
            'diterima' => 'green',
            'ditolak'  => 'red',
            default    => 'gray',
        };
    }
 
    // ── Relasi ────────────────────────────────────────────────────────
 
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }
 
    public function persyaratan()
    {
        return $this->belongsTo(PersyaratanDokumen::class, 'persyaratan_id');
    }

}
