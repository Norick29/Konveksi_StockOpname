<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class produk extends Model
{
    protected $primaryKey = 'id_produk';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_kategori',
        'color',
        'size',
        'sku',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }
}
