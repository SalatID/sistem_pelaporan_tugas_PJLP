<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Uuid, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'role',
        'password',
        'email_verified_at',
        'nama',
        'nip',
        'username',
        'jabatan_id',
        'lokasi_id',
        // 'status',      // opsional: APPROVED / PENDING
        'created_user',
        'updated_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
    /* =====================
     |  RELATIONSHIPS
     ===================== */

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'pengguna_id');
    }

    public function changeRequests()
    {
        return $this->hasMany(UserChangeRequest::class);
    }

    /* =====================
     |  ROLE HELPERS (RBAC)
     ===================== */

    public function isPengawas(): bool
    {
        return $this->jabatan?->nama === 'Pengawas';
    }

    public function isKordinator(): bool
    {
        return $this->jabatan?->nama === 'Kordinator';
    }

    public function isPetugas(): bool
    {
        return $this->jabatan?->nama === 'Perawat';
    }

    /* =====================
     |  ACCESSORS
     ===================== */

    public function getDisplayNameAttribute(): string
    {
        return $this->nama ?? $this->fullname ?? $this->username ?? $this->email;
    }
}
