<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'nama',
        'no_telp',
        'email',
        'alamat',
        'plat_nomor',
        'vin',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });
        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
        static::deleting(function ($model) {
            $model->deleted_by = Auth::id();
            $model->save();
        });
    }
    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class, 'id_bengkel');
    }
}
