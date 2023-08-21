<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterfileModel extends Model
{
    use HasFactory;

    protected $table = 'masterfiles';

    protected $fillable = [
        'ref_no',
        'product_id',
        'item_type',
        'inv_qty',
        'inv_uom',
        'whse_qty',
        'whse_uom',
        'client_id',
        'store_id',
        'rack',
        'level',
        'updated_at',
        'created_at'
    ];

    public function product() 
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

}
