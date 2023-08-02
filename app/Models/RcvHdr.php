<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcvHdr extends Model
{
    use HasFactory;

    protected $table = 'rcv_hdr';

    protected $fillable = [
        'rcv_no',
        'po_num',
        'store_id',
        'client_id',
        'supplier_id',
        'date_received',
        'received_by',
        'po_date',
        'inspect_by',
        'inspect_date',
        'date_arrived',
        'date_departed',
        'plate_no',
        'sales_invoice',
        'truck_type',
        'warehouse_id',
        'remarks',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

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
