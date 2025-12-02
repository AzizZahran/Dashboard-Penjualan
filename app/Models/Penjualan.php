<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'nama_produk',
        'tanggal_penjualan',
        'jumlah',
        'harga',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
    ];
    
    protected $dates = ['sold_at'];

    public function getTotalAttribute()
    {
        return $this->jumlah * $this->harga;
    }
}