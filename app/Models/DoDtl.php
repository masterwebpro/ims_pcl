<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoDtl extends Model
{
    use HasFactory;
    protected $table = 'do_dtl';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function header()
    {
        return $this->belongsTo(DoHdr::class, 'do_no', 'do_no');
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
