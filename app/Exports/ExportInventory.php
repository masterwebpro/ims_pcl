<?php

namespace App\Exports;

use App\Models\MasterfileModel;
use App\Models\MasterdataModel;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;


class ExportInventory implements FromCollection, WithHeadings
{
    use Exportable;
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {

        // $rcv = MasterfileModel::select('cl.client_name', 's.store_name', 'w.warehouse_name', 'product_code', 'product_name',  DB::raw("IFNULL(sl.location, 'RA')  as location"),  'masterfiles.item_type', DB::raw("SUM(inv_qty) as inv_qty"),  'ui.code as ui_code',  DB::raw("SUM(whse_qty) as whse_qty"),  'uw.code as uw_code')
        //     ->leftJoin('products as p', 'p.product_id', '=', 'masterfiles.product_id')
        //     ->leftJoin('storage_locations as sl', 'sl.storage_location_id', '=', 'masterfiles.storage_location_id')
        //     ->leftJoin('client_list as cl', 'cl.id', '=', 'masterfiles.company_id')
        //     ->leftJoin('store_list as s', 's.id', '=', 'masterfiles.store_id')
        //     ->leftJoin('warehouses as w', 'w.id', '=', 'masterfiles.warehouse_id')
        //     ->leftJoin('uom as uw', 'uw.uom_id', '=', 'masterfiles.whse_uom')
        //     ->leftJoin('uom as ui', 'ui.uom_id', '=', 'masterfiles.inv_uom')
        //     ->groupBy('cl.client_name', 's.store_name', 'w.warehouse_name', 'product_name', 'sl.location','masterfiles.item_type','masterfiles.status', 'masterfiles.whse_uom', 'masterfiles.inv_uom')
        //     ->having('inv_qty',  '>', 0)
        //     ->orderBy('product_name')
        //     ->orderBy('sl.location');

        $rcv = MasterdataModel::select('cl.client_name', 's.store_name', 'w.warehouse_name', 'sap_code',   'product_code', 'product_name',  'rd.lot_no', 'rd.manufacture_date', 'rd.expiry_date',  'masterdata.item_type', DB::raw("IFNULL(sl.location, 'RA')  as location"),  DB::raw("SUM(masterdata.inv_qty) as inv_qty"),  'ui.code as ui_code',  DB::raw("SUM(masterdata.reserve_qty) as reserve_qty"),DB::raw("SUM(masterdata.inv_qty - masterdata.reserve_qty) as balance_qty"), 'masterdata.remarks')
            ->leftJoin('products as p', 'p.product_id', '=', 'masterdata.product_id')
            ->leftJoin('storage_locations as sl', 'sl.storage_location_id', '=', 'masterdata.storage_location_id')
            ->leftJoin('client_list as cl', 'cl.id', '=', 'masterdata.company_id')
            ->leftJoin('store_list as s', 's.id', '=', 'masterdata.store_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'masterdata.warehouse_id')
            ->leftJoin('uom as uw', 'uw.uom_id', '=', 'masterdata.whse_uom')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'masterdata.inv_uom')
            ->leftJoin('rcv_dtl as rd', 'rd.id', '=', 'masterdata.rcv_dtl_id')
            ->groupBy('client_name', 'store_name', 'w.warehouse_name', 'product_name', 'sl.location','masterdata.item_type', 'masterdata.whse_uom', 'masterdata.inv_uom', 'rd.lot_no', 'rd.manufacture_date', 'rd.expiry_date','masterdata.remarks')
            ->having('inv_qty',  '>', 0)
            ->orderBy('product_name')
            ->orderBy('sl.location');

        if($this->request->has('company')  && $this->request->company !='')
            $rcv->where('masterdata.company_id', $this->request->company);

        if($this->request->has('store')  && $this->request->store !='')
            $rcv->where('masterdata.store_id', $this->request->store);

        if($this->request->has('warehouse')  && $this->request->warehouse !='')
            $rcv->where('masterdata.warehouse_id', $this->request->warehouse);

        if($this->request->has('product_id')  && $this->request->product_id !='')
            $rcv->where('p.product_id', $this->request->product_id);

        if($this->request->has('item_type')  && $this->request->item_type !='')
            $rcv->where('masterdata.item_type', $this->request->item_type);

        if($this->request->has('location')  && $this->request->location !='')
            $rcv->where('masterdata.storage_location_id', $this->request->location);

        $result = $rcv->get();

        // if($this->request->has('date_received')  && $this->request->date_received !='') {
        //     $date_split = explode(" to ",$this->request->date_received);
        //     $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";
        //     $to = date('Y-m-d',  strtotime($date_split[1]))." 023:59:59";

        //     $rcv->whereBetween('date_received', [$from, $to]);
        // }


        return $result;
    }

    public function headings(): array
    {
        return [
        'Company Name',
        'Site Location',
        'Warehouse Name',
        'SAP Code',
        'Product Code',
		'Product Description',
        'Batch/Lot No',
        'MFG Date',
        'Expiry Date',
        'Item Type',
        'Location',
        'Inv Qty',
        'UOM',
        'Reserve Qty',
        'Balance Qty',
        'Remarks'
       ];
	}
}
