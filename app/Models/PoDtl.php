<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoDtl extends Model
{
    use HasFactory;

    protected $table = 'po_dtl';

    public $timestamps = false;

    public function header()
    {
        return $this->belongsTo(PoHdr::class, 'po_num', 'po_num');
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
