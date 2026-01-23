<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Lokasi extends Model
{
    use Uuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'lokasi';

    protected $fillable = [
        'nama',
        'alamat',
        'created_user',
        'updated_user',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
