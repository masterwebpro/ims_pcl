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
                    'dispatch_dtl.qty',
                    'cl.client_name',
                    'del.client_name as deliver_to',
                    'wd_hdr.order_no',
                    'wd_hdr.order_date',
                    'wd_hdr.po_num',
                    'wd_hdr.sales_invoice',
                    'wd_hdr.dr_no'
                    )
                    ->leftJoin('wd_hdr','wd_hdr.wd_no','dispatch_dtl.wd_no')
                    ->leftJoin('client_list as cl','cl.id','wd_hdr.customer_id')
                    ->leftJoin('client_list as del','del.id','wd_hdr.deliver_to_id');
    }

    public function truck()
    {
        return $this->hasMany(DispatchTruck::class, 'dispatch_no', 'dispatch_no');
    }
}
