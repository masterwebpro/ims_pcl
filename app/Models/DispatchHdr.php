<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchHdr extends Model
{
    use HasFactory;
    protected $table = "dispatch_hdr";
    protected $primaryKey = 'id';
    protected $guarded = ['id','created_at','updated_at'];

    public function items()
    {
        return $this->hasMany(DispatchDtl::class, 'dispatch_no', 'dispatch_no')
                    ->select('dispatch_dtl.dispatch_no',
                    'dispatch_dtl.wd_no',
                    'dispatch_dtl.wd_dtl_id',
                    'dispatch_dtl.qty as dispatch_qty',
                    'wd_dtl.inv_qty as wd_qty',
                    'ui.code as unit',
                    'cl.client_name',
                    'del.client_name as deliver_to',
                    'wd_hdr.order_no',
                    'wd_hdr.order_date',
                    'wd_hdr.po_num',
                    'wd_hdr.sales_invoice',
                    'wd_hdr.dr_no',
                    'p.product_code',
                    'p.product_name',
                    )
                    ->leftJoin('wd_dtl','wd_dtl.id','dispatch_dtl.wd_dtl_id')
                    ->leftJoin('wd_hdr','wd_hdr.wd_no','dispatch_dtl.wd_no')
                    ->leftJoin('client_list as cl','cl.id','wd_hdr.customer_id')
                    ->leftJoin('client_list as del','del.id','wd_hdr.deliver_to_id')
                    ->leftJoin('products as p','p.product_id','=','wd_dtl.product_id')
                    ->leftJoin('uom as ui','ui.uom_id','=','wd_dtl.inv_uom');
    }

    public function truck()
    {
        return $this->hasMany(DispatchTruck::class, 'dispatch_no', 'dispatch_no');
    }
}
