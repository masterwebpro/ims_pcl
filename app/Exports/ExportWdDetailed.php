<?php

namespace App\Exports;
use App\Models\WdHdr;
use Illuminate\Support\Facades\DB;
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
        $wd = WdHdr::select(
                'wd_hdr.withdraw_date',
                'wd_hdr.wd_no',
                'dd.dispatch_no',
                'wd_hdr.order_no',
                'wd_hdr.order_type',
                'wd_hdr.dr_no',
                'wd_hdr.sales_invoice',
                'wd_hdr.po_num',
                'p.product_code',
                'p.product_name',
                DB::raw('SUM(wd.inv_qty) as inv_qty'),
                'ui.code as ui_code',
                DB::raw('SUM(dd.qty) as qty'),
                'rd.lot_no',
                'rd.expiry_date',
                'rd.manufacture_date',
                'md.remarks'
            )
            ->leftJoin('wd_dtl as wd', 'wd.wd_no', '=', 'wd_hdr.wd_no')
            ->leftJoin('rcv_dtl as rd', 'rd.id', '=', 'wd.rcv_dtl_id')
            ->leftJoin('products as p', 'p.product_id', '=', 'wd.product_id')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'wd.inv_uom')
            ->leftJoin('masterdata as md', 'md.id', '=', 'wd.master_id')
            ->leftJoin('dispatch_dtl as dd', 'dd.wd_dtl_id', '=', 'wd.id')
            ->where('wd_hdr.status', 'posted')
            ->when(!empty($this->request->wd_no), function ($q) {
                $q->where('wd_hdr.wd_no', $this->request->wd_no);
            })
            ->when($this->request->filled('client'), function ($q) {
                $q->where('wd_hdr.customer_id', $this->request->client);
            })
            ->when($this->request->filled('store'), function ($q) {
                $q->where('wd_hdr.store_id', $this->request->store);
            })
            ->when($this->request->filled('product_code'), function ($q) {
                $q->where('p.product_code', $this->request->product_code);
            })
            ->when($this->request->filled('order_type'), function ($q) {
                $q->where('wd_hdr.order_type', $this->request->order_type);
            })
            ->when($this->request->filled('withdraw_date'), function ($q) {
                $date_split = explode(" to ", $this->request->withdraw_date);
                $from = date('Y-m-d', strtotime($date_split[0])) . " 00:00:00";
                $to = date('Y-m-d', strtotime($date_split[1])) . " 23:59:59";
                $q->whereBetween('wd_hdr.withdraw_date', [$from, $to]);
            })
            ->when($this->request->filled('product_name'), function ($q) {
                $q->where('p.product_name', 'LIKE', '%' . $this->request->product_name . '%');
            })
            ->groupBy(
                'wd_hdr.wd_no',
                'wd_hdr.withdraw_date',
                'wd_hdr.customer_id',
                'wd_hdr.store_id',
                'wd_hdr.order_type',
                'p.product_code',
                'p.product_name',
                'wd.product_id',
                'wd.master_id',
                'wd.id',
                'ui.code',
                'rd.lot_no',
                'rd.manufacture_date',
                'rd.expiry_date',
                'dd.dispatch_no',
                'md.remarks'
            );

        return $wd->get();
    }

    public function headings(): array
    {
        return [
        'Date Withdraw',
        'Reference No',
        'Dispatch No',
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
        'Remarks'
       ];
	}
}
