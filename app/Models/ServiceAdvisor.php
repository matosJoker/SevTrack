<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ServiceAdvisor extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'service_advisors';
    protected $fillable = [
        'nama_service_advisor',
        'no_telp',
        'email',
        'alamat',
        'id_bengkel',
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
}
