<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ekspedisi extends Model
{
     protected $primaryKey = 'id_ekspedisi';

    protected $fillable = [
        'id_toko',
        'id_user',
        'date',
        'courier',
        'quantity', 
        'press_admin',
        'resi_admin',
        'packing_admin',
        'buang_admin',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
