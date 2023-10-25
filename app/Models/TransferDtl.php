<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferDtl extends Model
{
    use HasFactory;

    protected $table = "transfer_dtl";
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function item() 
    {
        return $this->hasOne(Products::class, 'product_id', 'product_id');
    }

    public function source_location() 
    {
        return $this->hasOne(StorageLocationModel::class, 'storage_location_id', 'source_storage_location_id');
    }

    public function source_warehouse() 
    {
        return $this->hasOne(Warehouse::class, 'id', 'source_warehouse_id');
    }

    public function dest_location() 
    {
        return $this->hasOne(StorageLocationModel::class, 'storage_location_id', 'dest_storage_location_id');
    }

    public function dest_warehouse() 
    {
        return $this->hasOne(Warehouse::class, 'id', 'dest_warehouse_id');
    }

    public function source_uom() 
    {
        return $this->hasOne(UOM::class, 'uom_id', 'source_inv_uom');
    }

    public function dest_uom() 
    {
        return $this->hasOne(UOM::class, 'uom_id', 'dest_inv_uom');
    }

}
