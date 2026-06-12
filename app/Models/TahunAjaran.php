<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
 
    protected $fillable = [
        'nama',
        'status',
        'batas_akhir_pembayaran',
        'maks_termin',
    ];
 
    protected $casts = [
        'batas_akhir_pembayaran' => 'date',
    ];

    // ── Scope ─────────────────────────────────────────────────────────
    // Ambil tahun ajaran yang sedang aktif
    // Penggunaan: TahunAjaran::aktif()
    public static function aktif(): ?self
    {
        return static::where('status', 'aktif')->first();
    }
 
    // ── Helper ────────────────────────────────────────────────────────
    // Cek apakah ada gelombang yang sedang buka hari ini
    public function isPendaftaranBuka(): bool
    {
        return $this->gelombang()
            ->where('tanggal_buka', '<=', today())
            ->where('tanggal_tutup', '>=', today())
            ->exists();
    }
 
    // Ambil gelombang yang sedang buka hari ini
    public function gelombangAktif(): ?Gelombang
    {
        return $this->gelombang()
            ->where('tanggal_buka', '<=', today())
            ->where('tanggal_tutup', '>=', today())
            ->first();
    }
 
    // ── Relasi ────────────────────────────────────────────────────────
    public function gelombang()
    {
        return $this->hasMany(Gelombang::class, 'tahun_ajaran_id')
            ->orderBy('urutan');
    }
 
    public function rincianBiaya()
    {
        return $this->hasMany(RincianBiaya::class, 'tahun_ajaran_id')
            ->orderBy('urutan');
    }
 
     public function persyaratanDokumen()
     {
         return $this->hasMany(PersyaratanDokumen::class, 'tahun_ajaran_id')
          ->orderBy('urutan');
     }
 
     public function pendaftaran()
     {
         return $this->hasMany(Pendaftaran::class, 'tahun_ajaran_id');
     }
}
