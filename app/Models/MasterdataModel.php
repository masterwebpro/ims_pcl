<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use Illuminate\Support\Facades\DB;

class MasterdataModel extends Model
{
    use HasFactory, Compoships;
    public $timestamps = false;
    protected $table = 'masterdata';
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(WdDtl::class, ['master_id', 'product_id'],['id', 'product_id'])
                ->select(
                    'wd_dtl.master_id',
                    'wd_dtl.product_id',
                    'wd_dtl.wd_no',
                    'dispatch_dtl.dispatch_no',
                    DB::raw('SUM(wd_dtl.inv_qty) as wd_qty'),
                    DB::raw('SUM(wd_dtl.dispatch_qty) as remain_qty'),
                    DB::raw('SUM(dispatch_dtl.qty) as dispatch_qty')
                )
                ->leftJoin('wd_hdr as wh','wh.wd_no','wd_dtl.wd_no')
                ->leftJoin('dispatch_dtl','dispatch_dtl.wd_dtl_id','wd_dtl.id')
                ->leftJoin('dispatch_hdr','dispatch_hdr.dispatch_no','dispatch_dtl.dispatch_no')
                ->where('wh.status','posted')
                ->where('dispatch_hdr.status','posted')
                ->groupBy('wd_dtl.master_id','wd_dtl.product_id','wd_dtl.wd_no','dispatch_dtl.dispatch_no');
    }
}
