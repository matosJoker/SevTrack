<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Bengkel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bengkel';
    protected $fillable = [
        'nama_bengkel',
        'alamat',
        'no_telp',
        'email',
        'website'
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = Auth::id(); // Set created_by to the authenticated user's ID
        });
        static::updating(function ($model) {
            $model->updated_by = Auth::id(); // Set updated_by to the authenticated user's ID
        });
        static::deleting(function ($model) {
            $model->deleted_by = Auth::id(); // Set deleted_by to the authenticated user's ID
            $model->save(); // Save the model to update the deleted_by field
        });
    }
}
