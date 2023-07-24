<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcvHdr extends Model
{
    use HasFactory;

    protected $table = 'rcv_hdr';

    public function items() 
    {
        return $this->hasMany(RcvDtl::class, 'rcv_no', 'rcv_no');
    }
    public function supplier() 
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function warehouse() 
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function user_create() 
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function truck() 
    {
        return $this->hasOne(TruckType::class, 'id', 'created_by');
    }

    public function client() 
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
    public function store() 
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
}
