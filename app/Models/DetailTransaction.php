<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_transaksi';
    
    protected $fillable = [
        'id_transaksi',
        'id_layanan',
        'harga',
        'flag_harga_khusus',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_transaksi');
    }

    public function service()
    {
        return $this->belongsTo(Layanan::class, 'id_layanan');
    }
}