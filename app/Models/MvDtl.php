<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MvDtl extends Model
{
    use HasFactory;
    protected $table = 'mv_dtl';

    protected $fillable = ['ref_no','product_id',
        'old_storage_location_id', 'old_item_type', 'old_inv_qty','old_inv_uom',
        'new_storage_location_id', 'new_item_type', 'new_inv_qty', 'new_inv_uom', 'remarks'
    ];

    public function item()
    {
        return $this->hasOne(Products::class, 'product_id', 'product_id');
    }

    public function old_location()
    {
        return $this->hasOne(StorageLocationModel::class, 'storage_location_id', 'old_storage_location_id');
    }

    public function new_location()
    {
        return $this->hasOne(StorageLocationModel::class, 'storage_location_id', 'new_storage_location_id');
    }

    public function old_uom()
    {
        return $this->hasOne(UOM::class, 'uom_id', 'old_inv_uom');
    }

    public function new_uom()
    {
        return $this->hasOne(UOM::class, 'uom_id', 'new_inv_uom');
    }

}
