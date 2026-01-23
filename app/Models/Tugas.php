<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Tugas extends Model
{
    use Uuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'tugas';

    protected $fillable = [
        'nama',
        'kategori_id',
        'pengguna_id',
        'deskripsi',
        'foto',
        'status',        // DRAFT | PENDING | APPROVED | REJECTED
        'created_user',
        'updated_user',
    ];

    /* =====================
     |  RELATIONSHIPS
     ===================== */

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_user');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_user');
    }

    /* =====================
     |  STATUS HELPERS
     ===================== */

    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }
}
