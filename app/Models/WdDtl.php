<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WdDtl extends Model
{
    use HasFactory;

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
                ->select('masterdata.*','wh.warehouse_name','sl.location')
                ->leftJoin('warehouses as wh','wh.id','masterdata.warehouse_id')
                ->leftJoin('storage_locations as sl','sl.storage_location_id','masterdata.storage_location_id');
    }

    public function itemize()
    {
        return $this->hasMany(WdDtlItemize::class, 'wd_dtl_id', 'id');
    }
}
