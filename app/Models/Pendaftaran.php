<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
     protected $table = 'pendaftaran';
 
    protected $fillable = [
        'nomor_daftar',
        'user_id',
        'calon_murid_id',
        'tahun_ajaran_id',
        'gelombang_id',
        'jalur',
        'status',
        'catatan_admin',
        'tgl_submit',
        'tgl_verifikasi',
        'tgl_aktif',
        'diproses_oleh',
    ];

     protected $casts = [
        'tgl_submit'     => 'datetime',
        'tgl_verifikasi' => 'datetime',
        'tgl_aktif'      => 'datetime',
    ];
 
    // ── Generate Nomor Daftar ─────────────────────────────────────────
 
    // Generate nomor daftar unik saat submit final
    // Format: PMB-2025-001
    public static function generateNomorDaftar(): string
    {
        $tahun = date('Y');
        $last  = static::whereYear('tgl_submit', $tahun)
            ->whereNotNull('nomor_daftar')
            ->count();
 
        return 'PMB-' . $tahun . '-' . str_pad($last + 1, 3, '0', STR_PAD_LEFT);
    }

    // ── Accessor ──────────────────────────────────────────────────────
 
    // Label status untuk ditampilkan di UI
    // Contoh: 'menunggu_verifikasi' → 'Menunggu Verifikasi'
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'perlu_revisi'        => 'Perlu Revisi',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'cicilan_berjalan'    => 'Cicilan Berjalan',
            'menunggu_nis'        => 'Menunggu NIS',
            'aktif'               => 'Aktif',
            default               => ucfirst($this->status),
        };
    }
 
    // Warna badge status untuk Tailwind CSS
    // Pemakaian di view: bg-{{ $pendaftaran->status_color }}-100
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'yellow',
            'perlu_revisi'        => 'red',
            'menunggu_pembayaran' => 'blue',
            'cicilan_berjalan'    => 'orange',
            'menunggu_nis'        => 'purple',
            'aktif'               => 'green',
            default               => 'gray',
        };
    }
 
    // ── Helper Pembayaran ─────────────────────────────────────────────
    // ⏳ Dibuka saat model Pembayaran sudah dibuat
 
    // public function totalTerbayar(): int
    // {
    //     return $this->pembayaran()->where('status', 'lunas')->sum('nominal');
    // }
 
    // public function sudahLunas(): bool
    // {
    //     $total = $this->pembayaran()->count();
    //     $lunas = $this->pembayaran()->where('status', 'lunas')->count();
    //     return $total > 0 && $total === $lunas;
    // }
 
    // ── Relasi ────────────────────────────────────────────────────────
 
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
 
    public function calonMurid()
    {
        return $this->belongsTo(CalonMurid::class, 'calon_murid_id');
    }
 
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
 
    public function gelombang()
    {
        return $this->belongsTo(Gelombang::class, 'gelombang_id');
    }
 
    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
 
    
     public function dokumen()
     {
         return $this->hasMany(Dokumen::class, 'pendaftaran_id');
     }
 
     public function pembayaran()
     {
         return $this->hasMany(Pembayaran::class, 'pendaftaran_id')
             ->orderBy('termin_ke');
     }
}
