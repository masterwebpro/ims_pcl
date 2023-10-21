<?php

namespace App\Exports;

use App\Models\MasterdataModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportCurrentStocks implements FromCollection, WithHeadings
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
        $dateRangeParts = explode(" to ", $this->request->date);
        $startDate = isset($dateRangeParts[0]) ? $dateRangeParts[0] : "";
        $endDate = isset($dateRangeParts[1]) ? $dateRangeParts[1] : "";

        $master = MasterdataModel::select(
            's.store_name',
            'wh.warehouse_name',
            'products.product_code',
            'products.product_name',
            'masterdata.item_type',
            'masterdata.inv_qty',
            'ui.code as ui_code',
            'masterdata.whse_qty',
            'uw.code as uw_code',
            'masterdata.reserve_qty',
            'sl.location',
            'masterdata.lot_no',
            'masterdata.expiry_date',
            'masterdata.manufacture_date',
            'masterdata.received_date'
            )
            ->leftJoin('products', 'products.product_id', '=', 'masterdata.product_id')
            ->leftJoin('uom as uw','uw.uom_id','=','masterdata.whse_uom')
            ->leftJoin('uom as ui','ui.uom_id','=','masterdata.inv_uom')
            ->leftJoin('storage_locations as sl','sl.storage_location_id','=','masterdata.storage_location_id')
            ->leftJoin('store_list as s', 's.id', '=', 'masterdata.store_id')
            ->leftJoin('client_list as c', 'c.id', '=', 'masterdata.customer_id')
            ->leftJoin('client_list as com', 'com.id', '=', 'masterdata.company_id')
            ->leftJoin('warehouses as wh', 'wh.id', '=', 'masterdata.warehouse_id');
            if ($this->request->keyword) {
                $keyword = "%".$this->request->keyword."%";
                $master->where(function($query)use($keyword){
                    $query->where('products.product_code','like', $keyword);
                    $query->orWhere('products.product_name','like', $keyword);
                    $query->orWhere('masterdata.lot_no','like', $keyword);
                    $query->orWhere('sl.location','like', $keyword);
                    $query->orWhere('masterdata.expiry_date','like', $keyword);
                    $query->orWhere('masterdata.manufacture_date','like', $keyword);
                    $query->orWhere('masterdata.item_type','like', $keyword);
                });
            }

            if ($this->request->customer) {
                $master->where('masterdata.customer_id', $this->request->customer);
            }

            if ($this->request->company) {
                $master->where('masterdata.company_id', $this->request->company);
            }
            if($this->request->has('received_date')  && $this->request->received_date !='') {
                $date_split = explode(" to ",$this->request->received_date);
                $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";
                $to = date('Y-m-d',  strtotime($date_split[1]))." 023:59:59";

                $master->whereBetween('received_date', [$from, $to]);
            }


            return $master->get();
    }

    public function headings(): array
    {
        return [
        'SITE NAME',
        'WAREHOUSE',
        'PRODUCT CODE',
        'PRODUCT NAME',
        'ITEM TYPE',
        'INV QTY',
        'UNIT',
        'WHSE QTY',
		'UNIT',
        'RESERVE QTY',
        'LOCATION',
        'LOT NO',
        'EXPIRY DATE',
        'MFG. DATE',
        'RCV. DATE'
       ];
	}
}
