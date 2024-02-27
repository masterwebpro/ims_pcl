<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
class RcvDtl extends Model
{
    use Compoships;
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
        'manufacture_date',
        'po_dtl_id',
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

    public function withdraw()
    {
        return $this->hasMany(WdDtl::class, ['product_id', 'rcv_dtl_id'],['product_id', 'id'])
        ->select('wd_dtl.*','wd_hdr.wd_no','wd_hdr.withdraw_date','wd_hdr.status')
        ->leftJoin('wd_hdr', 'wd_hdr.wd_no', '=', 'wd_dtl.wd_no')
        ->leftJoin('dispatch_dtl', 'dispatch_dtl.wd_dtl_id', '=', 'wd_dtl.id')
        ->leftJoin('dispatch_hdr', 'dispatch_hdr.dispatch_no', '=', 'dispatch_dtl.dispatch_no')
        ->where('wd_hdr.status','posted')
        ->where('dispatch_hdr.status','posted');
    }
}
