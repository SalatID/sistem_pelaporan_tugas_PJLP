<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Kategori extends Model
{
    use Uuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'kategori';

    protected $fillable = [
        'nama',
        'created_user',
        'updated_user',
    ];

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }
}
