<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stok_harian extends Model
{
    protected $table = 'stok_harians';
    protected $primaryKey = 'id_stok_harian';

    protected $fillable = [
        'id_produk',
        'id_toko',
        'id_user',
        'type',
        'quantity',
        'note',
        'transaction_date',
    ];

    public function produk()
    {
        return $this->belongsTo(produk::class, 'id_produk', 'id_produk');
    }

    public function toko()
    {
        return $this->belongsTo(toko::class, 'id_toko', 'id_toko');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
