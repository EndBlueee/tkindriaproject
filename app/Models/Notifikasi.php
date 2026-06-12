<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'judul',
        'pesan',
        'tipe',
        'url',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ── Scope ─────────────────────────────────────────────────────

    // Ambil yang belum dibaca
    public function scopeBelumDibaca($query)
    {
        return $query->where('is_read', false);
    }

    // ── Accessor ──────────────────────────────────────────────────

    // Icon per tipe
    public function getIconAttribute(): string
    {
        return match ($this->tipe) {
            'pendaftar_baru'   => 'heroicon-o-clipboard-document-list',
            'pembayaran_masuk' => 'heroicon-o-credit-card',
            'dokumen_direvisi' => 'heroicon-o-exclamation-triangle',
            'nis_terbit'       => 'heroicon-o-academic-cap',
            default            => 'heroicon-o-bell',
        };
    }

    // Warna per tipe
    public function getWarnaAttribute(): string
    {
        return match ($this->tipe) {
            'pendaftar_baru'   => 'text-blue-500',
            'pembayaran_masuk' => 'text-green-500',
            'dokumen_direvisi' => 'text-red-500',
            'nis_terbit'       => 'text-purple-500',
            default            => 'text-gray-400',
        };
    }

    // Waktu relatif
    public function getWaktuAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
