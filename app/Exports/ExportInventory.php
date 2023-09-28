<?php

namespace App\Exports;

use App\Models\MasterfileModel;

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
        
        $rcv = MasterfileModel::select('cl.client_name', 's.store_name', 'w.warehouse_name', 'product_code', 'product_name',  DB::raw("IFNULL(sl.location, 'RA')  as location"),  'masterfiles.item_type', DB::raw("SUM(inv_qty) as inv_qty"),  'ui.code as ui_code',  DB::raw("SUM(whse_qty) as whse_qty"),  'uw.code as uw_code')
            ->leftJoin('products as p', 'p.product_id', '=', 'masterfiles.product_id')
            ->leftJoin('storage_locations as sl', 'sl.storage_location_id', '=', 'masterfiles.storage_location_id')
            ->leftJoin('client_list as cl', 'cl.id', '=', 'masterfiles.company_id')
            ->leftJoin('store_list as s', 's.id', '=', 'masterfiles.store_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'masterfiles.warehouse_id')
            ->leftJoin('uom as uw', 'uw.uom_id', '=', 'masterfiles.whse_uom')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'masterfiles.inv_uom')
            ->groupBy('cl.client_name', 's.store_name', 'w.warehouse_name', 'product_name', 'sl.location','masterfiles.item_type','masterfiles.status', 'masterfiles.whse_uom', 'masterfiles.inv_uom')
            ->having('inv_qty',  '>', 0)
            ->orderBy('product_name')
            ->orderBy('sl.location');
        
        if($this->request->has('company')  && $this->request->company !='')
            $rcv->where('masterfiles.company_id', $this->request->company);
        
        if($this->request->has('store')  && $this->request->store !='')
            $rcv->where('masterfiles.store_id', $this->request->store);
        
        if($this->request->has('warehouse')  && $this->request->warehouse !='')
            $rcv->where('masterfiles.warehouse_id', $this->request->warehouse);

        if($this->request->has('product_id')  && $this->request->product_id !='')
            $rcv->where('p.product_id', $this->request->product_id);
        
        if($this->request->has('item_type')  && $this->request->item_type !='')
            $rcv->where('masterfiles.item_type', $this->request->item_type);

        if($this->request->has('location')  && $this->request->location !='')
            $rcv->where('masterfiles.storage_location_id', $this->request->location);
  
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
        'Product Code',
		'Product Description',
        'Location',
        'Item Type',
        'WHSE Qty',
        'UOM',
        'Inv Qty',
        'UOM'
       ];
	}
}
