<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBathces extends Model
{
    protected $table = 'stok_bathces'; // pastikan nama tabel benar

    protected $primaryKey = 'id_batch';

    protected $fillable = [
        'id_produk',
        'id_toko',
        'sumber',
        'id_sumber',
        'qty_awal',
        'qty_sisa',
        'tanggal_masuk',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko', 'id_toko');
    }

    public function sumberOpening()
    {
        return $this->belongsTo(stok_bulan::class, 'id_sumber', 'id_stok_bulan');
    }

    public function sumberIn()
    {
        return $this->belongsTo(stok_harian::class, 'id_sumber', 'id_stok_harian');
    }

    public function scopeAvailable($query)
    {
        return $query->where('qty_sisa', '>', 0);
    }

    // FIFO = order by tanggal_masuk ASC
    public function scopeFIFO($query)
    {
        return $query->orderBy('tanggal_masuk', 'asc');
    }

    
}
