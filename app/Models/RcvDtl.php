<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcvDtl extends Model
{
    use HasFactory;

    protected $table = 'rcv_dtl';

    protected $fillable = [
        'rcv_no',
        'product_id',
        'item_type',
        'inv_qty',
        'inv_uom',
        'whse_qty',
        'whse_uom',
        'lot_no',
        'expiry_date',
        'remarks',
        'updated_at',
        'created_at'
    ];

    public function header()
    {
        return $this->belongsTo(RcvHdr::class, 'rcv_no', 'rcv_no');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function whse()
    {
        return $this->hasOne(UOM::class, 'uom_id', 'whse_uom');
    }
    public function inv()
    {
        return $this->hasOne(UOM::class, 'uom_id', 'whse_uom');
    }
}
