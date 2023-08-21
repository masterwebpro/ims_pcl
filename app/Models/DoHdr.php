<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoHdr extends Model
{
    use HasFactory;
    protected $table = 'do_hdr';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(DoDtl::class, 'do_no', 'do_no');
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


    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
}
