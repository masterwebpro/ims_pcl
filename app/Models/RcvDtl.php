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

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'uom_id', 'uom_id');
    }
}
