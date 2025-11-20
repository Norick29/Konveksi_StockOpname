<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stok_bulan extends Model
{
    protected $table = 'stok_bulans';
    protected $primaryKey = 'id_stok_bulan';

    protected $fillable = [
        'id_produk',
        'id_toko',
        'month',
        'quantity',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }
}
