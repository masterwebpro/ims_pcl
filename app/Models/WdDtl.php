<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use Illuminate\Support\Facades\DB;

class WdDtl extends Model
{

    use HasFactory, Compoships;

    protected $table = 'wd_dtl';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function header()
    {
        return $this->belongsTo(DoHdr::class, 'wd_no', 'wd_no');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'inv_uom', 'uom_id');
    }

    public function master()
    {
        return $this->belongsTo(MasterdataModel::class, 'master_id', 'id')
                ->select('masterdata.*','wh.warehouse_name','sl.location', 'uom.code')
                ->leftJoin('warehouses as wh','wh.id','masterdata.warehouse_id')
                ->leftJoin('storage_locations as sl','sl.storage_location_id','masterdata.storage_location_id')
                ->leftJoin('uom','uom.uom_id','masterdata.inv_uom');
    }

    public function itemize()
    {
        return $this->hasMany(WdDtlItemize::class, 'wd_dtl_id', 'id');
    }

    public function receiving()
    {
        return $this->belongsTo(RcvDtl::class, 'rcv_dtl_id', 'id')
                ->select('rcv_dtl.id','rcv_dtl.lot_no','rcv_dtl.expiry_date','rcv_dtl.manufacture_date','rh.date_received as received_date')
                ->leftJoin('rcv_hdr as rh','rh.rcv_no','rcv_dtl.rcv_no');
    }

    public function dispatch()
    {
        return $this->hasOne(DispatchDtl::class, 'wd_dtl_id', 'id')
                ->select('wd_dtl_id',DB::raw('GROUP_CONCAT(dispatch_no SEPARATOR ", ") as dispatch_no'));
    }
}
