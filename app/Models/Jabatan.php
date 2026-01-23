<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Jabatan extends Model
{
    use Uuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'jabatan';

    protected $fillable = [
        'nama',
        'created_user',
        'updated_user',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
