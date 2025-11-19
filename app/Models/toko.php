<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class toko extends Model
{
    protected $primaryKey = 'id_toko';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
    ];
}
