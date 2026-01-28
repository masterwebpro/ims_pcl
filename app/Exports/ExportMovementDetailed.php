<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\MvDtl;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportMovementDetailed implements FromCollection, WithHeadings
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
        $data_list = MvDtl::select(
            DB::raw('DATE_FORMAT(mh.created_at, "%d %b %Y") as movement_date'),
            'mv_dtl.ref_no',
            'cl.client_name',
            's.store_name',
            'w.warehouse_name',
            DB::raw('DATE_FORMAT(mh.start_encoding, "%H:%i") as start_encoding'),
            DB::raw('DATE_FORMAT(mh.end_encoding, "%H:%i") as end_encoding'),
            'p.product_code',
            'p.product_name',
            'mv_dtl.old_item_type',
            'old_sl.location as old_location_code',
            'mv_dtl.old_inv_qty',
            'old_ui.code as old_unit',
            'mv_dtl.new_item_type',
            'new_sl.location as new_location_code',
            'mv_dtl.new_inv_qty',
            'new_ui.code as new_unit',
            'mv_dtl.remarks as detail_remarks',
            'u.name',
        )
        ->leftJoin('mv_hdr as mh', 'mh.ref_no', '=', 'mv_dtl.ref_no')
        ->leftJoin('products as p', 'p.product_id', '=', 'mv_dtl.product_id')
        ->leftJoin('storage_locations as old_sl', 'old_sl.storage_location_id', '=', 'mv_dtl.old_storage_location_id')
        ->leftJoin('storage_locations as new_sl', 'new_sl.storage_location_id', '=', 'mv_dtl.new_storage_location_id')
        ->leftJoin('uom as old_ui', 'old_ui.uom_id', '=', 'mv_dtl.old_inv_uom')
        ->leftJoin('uom as new_ui', 'new_ui.uom_id', '=', 'mv_dtl.new_inv_uom')
        ->leftJoin('client_list as cl', 'cl.id', '=', 'mh.company_id')
        ->leftJoin('store_list as s', 's.id', '=', 'mh.store_id')
        ->leftJoin('warehouses as w', 'w.id', '=', 'mh.warehouse_id')
        ->leftJoin('users as u', 'u.id', '=', 'mh.created_by')
        ->groupBy('mv_dtl.id')
        ->orderBy('mh.created_at','ASC')
        ->where('mh.status','posted');
         if($this->request->has('movement_no') && $this->request->movement_no !='') {
             $data_list->where('mh.ref_no', $this->request->movement_no);
         }
         if($this->request->has('movement_date') && $this->request->movement_date !=''){
           $data_list->when($this->request->has('movement_date'), function ($q) {
                $date_split = explode(" to ", $this->request->movement_date);
                $from = date('Y-m-d', strtotime($date_split[0])) . " 00:00:00";
                $to = date('Y-m-d', strtotime($date_split[1])) . " 23:59:59";
                $q->whereBetween('mh.created_at', [$from, $to]);
           });
        }
        $data_list->groupBy(
                'mv_dtl.id',
                'mv_dtl.product_id',
                'cl.client_name',
                's.store_name',
                'w.warehouse_name',
                'mh.created_at',
                'u.name',
            );
        $result = $data_list->get();
        return $result;
    }

    public function headings(): array
    {
        return [
        'Date',
        'Reference No',
        'Company Name',
        'Site Name',
        'Warehouse Name',
        'Start Encoding',
        'Finish Encoding',
        'Product Code',
		'Product Description',
        'Old Item Type',
        'Old Location',
        'Old Inv Qty',
        'Old Unit',
        'New Item Type',
        'New Location',
        'New Inv Qty',
        'New Unit',
        'Remarks',
        'Moved By',
       ];
	}
}
