<?php

namespace App\Exports;

use App\Models\RcvHdr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportRcvDetailed implements FromCollection, WithHeadings
{
    use Exportable;
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $rcv = RcvHdr::select('rcv_hdr.date_received','rcv_hdr.rcv_no', 'rcv_hdr.po_num', 'p.product_code', 'p.product_name','rd.item_type', 'rd.whse_qty','uw.code as uw_code', 'rd.inv_qty', 'ui.code as ui_code' ,'rd.lot_no', 'rd.expiry_date', 'rd.manufacture_date','rcv_hdr.plate_no', DB::raw('DATE_FORMAT(rcv_hdr.date_arrived, "%H:%i") as date_arrived'), DB::raw('DATE_FORMAT(rcv_hdr.start_unloading, "%H:%i") as start_unloading'), DB::raw('DATE_FORMAT(rcv_hdr.finish_unloading, "%H:%i") as finish_unloading'), DB::raw('DATE_FORMAT(rcv_hdr.date_departed, "%H:%i") as date_departed'), 'rcv_hdr.inspect_date', 'rcv_hdr.inspect_by')
            ->leftJoin('rcv_dtl as rd', 'rd.rcv_no', '=', 'rcv_hdr.rcv_no')
            ->leftJoin('products as p', 'p.product_id', '=', 'rd.product_id')
            ->leftJoin('uom as uw', 'uw.uom_id', '=', 'rd.whse_uom')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'rd.inv_uom')
            ->where('rcv_hdr.status','posted');

        if($this->request->rcv_no !='')
            $rcv->where('rcv_hdr.rcv_no', $this->request->rcv_no);

        if($this->request->has('client')  && $this->request->client !='')
            $rcv->where('rcv_hdr.customer_id', $this->request->client);

        if($this->request->has('store')  && $this->request->store !='')
            $rcv->where('rcv_hdr.store_id', $this->request->store);

        if($this->request->has('warehouse')  && $this->request->warehouse !='')
            $rcv->where('rcv_hdr.warehouse_id', $this->request->warehouse);

        if($this->request->has('product_code')  && $this->request->product_code !='')
            $rcv->where('p.product_code', $this->request->product_code);

        if($this->request->has('item_type')  && $this->request->item_type !='')
            $rcv->where('rd.item_type', $this->request->item_type);

        if($this->request->has('date_received')  && $this->request->date_received !='') {
            $date_split = explode(" to ",$this->request->date_received);
            $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";
            $to = date('Y-m-d',  strtotime($date_split[1]))." 023:59:59";

            $rcv->whereBetween('date_received', [$from, $to]);
        }

        if($this->request->has('product_name')  && $this->request->product_name !='')
            $rcv->where('p.product_name','LIKE','%'.$this->request->product_name.'%');


        return $rcv->get();
    }

    public function headings(): array
    {
        return [
        'Date Received',
        'Reference No',
        'Po Number',
        'Product Code',
		'Product Description',
        'Item Type',
        'WHSE Qty',
        'UOM',
        'Inv Qty',
        'UOM',
        'Lot No',
        'Exp. Date',
        'Mfg. Date',
        'Plate No',
        'Time Arrived',
        'Start Unloading',
        'Finish Unloading',
        'Time Departed',
        'Inspect Date',
        'Inspect By'
       ];
	}
}
