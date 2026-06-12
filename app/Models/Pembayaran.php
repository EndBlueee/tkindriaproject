<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'pendaftaran_id',
        'metode',
        'termin_ke',
        'total_termin',
        'nominal',
        'status',
        'tanggal_bayar',
        'dicatat_oleh',
        'order_id',
        'kode_bayar',
        'trx_id',
        'expired_at',
    ];

    protected $casts = [
        'nominal'       => 'integer',
        'tanggal_bayar' => 'datetime',
        'expired_at'    => 'datetime',
    ];

    // ── Accessor ──────────────────────────────────────────────────────

    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getMetodeLabelAttribute(): string
    {
        return match ($this->metode) {
            'bca_va'     => 'BCA Virtual Account',
            'bni_va'     => 'BNI Virtual Account',
            'bri_va'     => 'BRI Virtual Account',
            'mandiri_va' => 'Mandiri Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'alfamart'   => 'Alfamart',
            'indomaret'  => 'Indomaret',
            'tunai'      => 'Tunai',
            'transfer'   => 'Transfer Bank',
            default      => ucfirst($this->metode),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu'    => 'Belum Dibayar',
            'lunas'       => 'Lunas',
            'kedaluwarsa' => 'Kedaluwarsa',
            default       => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu'    => 'yellow',
            'lunas'       => 'green',
            'kedaluwarsa' => 'red',
            default       => 'gray',
        };
    }

    // Cek apakah metode ini Virtual Account
    public function isVirtualAccount(): bool
    {
        return in_array($this->metode, ['bca_va', 'bni_va', 'bri_va', 'mandiri_va', 'permata_va']);
    }

    // Cek apakah metode ini minimarket
    public function isMinimarket(): bool
    {
        return in_array($this->metode, ['alfamart', 'indomaret']);
    }

    // Nama bank dari metode VA
    // Contoh: bca_va → BCA
    public function getNamaBankAttribute(): string
    {
        return match ($this->metode) {
            'bca_va'     => 'BCA',
            'bni_va'     => 'BNI',
            'bri_va'     => 'BRI',
            'mandiri_va' => 'Mandiri',
            'permata_va' => 'Permata',
            default      => strtoupper($this->metode),
        };
    }

    // ── Helper ────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        if ($this->status === 'kedaluwarsa') {
            return true;
        }
        if (! $this->expired_at) return false;
        return now()->isAfter($this->expired_at);
    }

    public function sudahLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public static function generateOrderId(int $pendaftaranId, int $terminKe): string
    {
        return 'PMB-' . $pendaftaranId . '-T' . $terminKe . '-' . time();
    }

    // ── Relasi ────────────────────────────────────────────────────────

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function dicatatOleh()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}