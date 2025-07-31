<?php

namespace App\Exports;
use App\Models\WdHdr;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportWdDetailed implements FromCollection, WithHeadings
{
     use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $wd = WdHdr::select('wd_hdr.withdraw_date','wd_hdr.wd_no', 'dd.dispatch_no', 'wd_hdr.order_no','wd_hdr.order_type','wd_hdr.dr_no','wd_hdr.sales_invoice','wd_hdr.po_num', 'p.product_code', 'p.product_name', 'wd.inv_qty', 'ui.code as ui_code','wd.dispatch_qty',  'rd.lot_no', 'rd.expiry_date','rd.manufacture_date')
            ->leftJoin('wd_dtl as wd', 'wd.wd_no', '=', 'wd_hdr.wd_no')
            ->leftJoin('rcv_dtl as rd', 'rd.id', '=', 'wd.rcv_dtl_id')
            ->leftJoin('products as p', 'p.product_id', '=', 'wd.product_id')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'wd.inv_uom')
            ->leftJoin('dispatch_dtl as dd', 'dd.wd_dtl_id', '=', 'wd.id')
            ->where('wd_hdr.status','posted');

        if($this->request->wd_no !='')
            $wd->where('wd_hdr.wd_no', $this->request->wd_no);

        if($this->request->has('client')  && $this->request->client !='')
            $wd->where('wd_hdr.customer_id', $this->request->client);

        if($this->request->has('store')  && $this->request->store !='')
            $wd->where('wd_hdr.store_id', $this->request->store);

        // if($this->request->has('warehouse')  && $this->request->warehouse !='')
        //     $wd->where('wd_hdr.warehouse_id', $this->request->warehouse);

        if($this->request->has('product_code')  && $this->request->product_code !='')
            $wd->where('p.product_code', $this->request->product_code);

        if($this->request->has('order_type')  && $this->request->order_type !='')
            $wd->where('wd_hdr.order_type', $this->request->order_type);

        if($this->request->has('withdraw_date')  && $this->request->withdraw_date !='') {
            $date_split = explode(" to ",$this->request->withdraw_date);
            $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";
            $to = date('Y-m-d',  strtotime($date_split[1]))." 023:59:59";

            $wd->whereBetween('withdraw_date', [$from, $to]);
        }

        if($this->request->has('product_name')  && $this->request->product_name !='')
            $wd->where('p.product_name','LIKE','%'.$this->request->product_name.'%');


        return $wd->get();
    }

    public function headings(): array
    {
        return [
        'Date Withdraw',
        'Reference No',
        'Order No',
        'Order Type',
        'DR No',
        'Sales Invoice',
        'Po Number',
        'Product Code',
		'Product Description',
        'Inv Qty',
        'UOM',
        'Dispatch Qty',
        'Lot No',
        'Exp. Date',
        'Mfg. Date',
       ];
	}
}
