<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoHdr extends Model
{
    use HasFactory;
    protected $table = 'po_hdr';

    protected $fillable = [
        'po_num',
        'store_id',
        'company_id',
        'customer_id',
        'supplier_id',
        'po_date',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];


    public function items() 
    {
        return $this->hasMany(PoDtl::class, 'po_num', 'po_num');
    }
    public function supplier() 
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    // public function client() 
    // {
    //     return $this->hasOne(Client::class, 'id', 'client_id');
    // }

    public function customer() 
    {
        return $this->hasOne(Client::class, 'id', 'customer_id');
    }

    public function company() 
    {
        return $this->hasOne(Client::class, 'id', 'company_id');
    }

    public function store() 
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
}
