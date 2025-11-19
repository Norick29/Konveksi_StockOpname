<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kategori extends Model
{
    protected $primaryKey = 'id_kategori';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
    ];
}
