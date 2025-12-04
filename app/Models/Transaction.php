<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi';

    protected $fillable = [
        'id_bengkel',
        'id_customers',
        'id_service_advisors',
        'total',
        'status',
        'kilometer',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customers');
    }
    public function serviceAdvisor()
    {
        return $this->belongsTo(ServiceAdvisor::class, 'id_service_advisors');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaction::class, 'id_transaksi');
    }
    public function mekanik()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
