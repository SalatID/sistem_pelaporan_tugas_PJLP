<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class UserChangeRequest extends Model
{
    use Uuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'user_change_requests';

    protected $fillable = [
        'user_id',
        'requested_by',
        'type',        // UPDATE_USER, CHANGE_ROLE, CHANGE_LOKASI
        'payload',     // JSON data perubahan
        'status',      // PENDING | APPROVED | REJECTED
        'processed_by',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    /* =====================
     |  RELATIONSHIPS
     ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
