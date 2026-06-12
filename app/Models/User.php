<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'nomor_hp',
        'password',
        'role',
        'is_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',  // otomatis hash, tidak perlu Hash::make() di controller
        'is_aktif' => 'boolean',
    ];

    // ── Helper Role ───────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrangTua(): bool
    {
        return $this->role === 'orangtua';
    }

    // ── Relasi ────────────────────────────────────────────────────────

    // Satu user (orangtua) punya satu calon murid/*

     public function calonMurid()
     {
         return $this->hasOne(CalonMurid::class, 'user_id');
     }

    // Satu user bisa punya banyak pendaftaran (meski umumnya 1 per tahun ajaran)
     public function pendaftaran()
     {
         return $this->hasMany(Pendaftaran::class, 'user_id');
     }
}