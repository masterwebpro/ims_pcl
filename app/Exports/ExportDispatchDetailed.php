<?php

namespace App\Exports;

use App\Models\DispatchDtl;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExportDispatchDetailed implements FromCollection , WithHeadings
{
    use Exportable;
    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data_list = DispatchDtl::select(
                'dh.dispatch_date',
                'dh.dispatch_no',
                'wh.wd_no',
                'dh.plate_no',
                'dh.truck_type',
                'dh.trucker_name',
                'dh.seal_no',
                'dh.dispatch_by',
                'dh.driver',
                'dh.helper',
                DB::raw('DATE_FORMAT(dh.start_picking_datetime, "%h:%i %p") as start_picking_datetime'),
                DB::raw('DATE_FORMAT(dh.finish_picking_datetime, "%h:%i %p") as finish_picking_datetime'),
                DB::raw('DATE_FORMAT(dh.arrival_datetime, "%h:%i %p") as arrival_datetime'),
                DB::raw('DATE_FORMAT(dh.start_datetime, "%h:%i %p") as start_datetime'),
                DB::raw('DATE_FORMAT(dh.finish_datetime, "%h:%i %p") as finish_datetime'),
                DB::raw('DATE_FORMAT(dh.depart_datetime, "%h:%i %p") as depart_datetime'),
                'p.product_code',
                'p.product_name',
                'wd.inv_qty',
                'dispatch_dtl.qty',
                'ui.code as unit',
                'wh.order_no',
                'wh.order_date',
                'wh.dr_no',
                'u.name'
                )
        ->leftJoin('dispatch_hdr as dh', 'dh.dispatch_no', '=', 'dispatch_dtl.dispatch_no')
        ->leftJoin('wd_dtl as wd', 'wd.id', '=', 'dispatch_dtl.wd_dtl_id')
        ->leftJoin('wd_hdr as wh', 'wh.wd_no', '=', 'wd.wd_no')
        ->leftJoin('masterdata as m', 'm.id', '=', 'wd.master_id')
        ->leftJoin('rcv_dtl as rd', 'rd.id', '=', 'wd.rcv_dtl_id')
        ->leftJoin('products as p', 'p.product_id', '=', 'wd.product_id')
        ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id')
        ->leftJoin('category_brands as cb', 'cb.category_brand_id', '=', 'p.category_brand_id')
        ->leftJoin('categories as cat', 'cat.category_id', '=', 'cb.category_id')
        ->leftJoin('uom as ui', 'ui.uom_id', '=', 'wd.inv_uom')
        ->leftJoin('users as u', 'u.id', '=', 'dh.created_by')
        ->groupBy('dispatch_dtl.id')
        ->orderBy('dh.dispatch_date','ASC')
        ->where('dh.status','posted');
        if($this->request->has('dispatch_no') && $this->request->dispatch_no !='') {
            $data_list->where('dh.dispatch_no', $this->request->dispatch_no);
        }
        // if($this->request->has('client') && $this->request->client !='') {
        //     $data_list->where('wh.customer_id', $this->request->client);
        // }
        // if($this->request->has('store') && $this->request->store !='') {
        //     $data_list->where('wh.store_id', $this->request->store);
        // }
        // if($this->request->has('product_code') && $this->request->product_code !='') {
        //     $data_list->where('p.product_code','LIKE', '%' . $this->request->product_code . '%');
        // }

        if($this->request->has('dispatch_date') && $this->request->dispatch_date !=''){
           $data_list->when($this->request->has('dispatch_date'), function ($q) {
                $date_split = explode(" to ", $this->request->dispatch_date);
                $from = date('Y-m-d', strtotime($date_split[0])) . " 00:00:00";
                $to = date('Y-m-d', strtotime($date_split[1])) . " 23:59:59";
                $q->whereBetween('dh.dispatch_date', [$from, $to]);
           });
        }

        $result = $data_list->groupBy(
            'dh.dispatch_date',
            'dh.dispatch_no',
            'wh.customer_id',
            'wh.store_id',
            'wh.order_type',
            'p.product_code',
            'p.product_name',
            'wd.product_id',
            'wd.master_id',
            'dispatch_dtl.id',
            'ui.code',
            'rd.lot_no',
            'rd.manufacture_date',
            'rd.expiry_date',
            'm.remarks'
        )
        ->get();

        return  $result;
    }

     public function headings(): array
    {
        return [
        'Date Dispatch',
        'Reference No',
        'Withdrawal No',
        'Plate No',
        'Truck Type',
        'Trucker Name',
        'Seal No',
        'Dispatch By',
        'Driver',
        'Helper',
        'Start Picking',
        'Finish Picking',
        'Actual Truck Arrival',
        'Start Loading',
        'Finish Loading',
        'Depart Date/Time',
        'Product Code',
		'Product Description',
        'Withdraw Qty',
        'Dispatch Qty',
        'Unit',
        'Order No',
        'Order Date',
        'Dr No',
        'Prepared By',
       ];
	}
}
