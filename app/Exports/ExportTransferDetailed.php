<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\TransferDtl;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportTransferDetailed implements FromCollection, WithHeadings
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
        $data_list = TransferDtl::select(
            DB::raw('DATE_FORMAT(th.trans_date, "%d %b %Y") as trans_date'),
            'transfer_dtl.ref_no',
            'cl.client_name',
            's.store_name',
            'th.dr_no',
            DB::raw('DATE_FORMAT(th.start_encoding, "%H:%i") as start_encoding'),
            DB::raw('DATE_FORMAT(th.end_encoding, "%H:%i") as end_encoding'),
            'p.product_code',
            'p.product_name',
            'transfer_dtl.source_item_type',
            'old_sl.location as source_location_code',
            'transfer_dtl.source_inv_qty',
            'old_ui.code as source_unit',
            'transfer_dtl.dest_item_type',
            'new_sl.location as dest_location_code',
            'transfer_dtl.dest_inv_qty',
            'new_ui.code as dest_unit',
            'transfer_dtl.remarks as detail_remarks',
            'th.requested_by',
            'u.name',
        )
        ->leftJoin('transfer_hdr as th', 'th.ref_no', '=', 'transfer_dtl.ref_no')
        ->leftJoin('products as p', 'p.product_id', '=', 'transfer_dtl.product_id')
        ->leftJoin('storage_locations as old_sl', 'old_sl.storage_location_id', '=', 'transfer_dtl.source_storage_location_id')
        ->leftJoin('storage_locations as new_sl', 'new_sl.storage_location_id', '=', 'transfer_dtl.dest_storage_location_id')
        ->leftJoin('uom as old_ui', 'old_ui.uom_id', '=', 'transfer_dtl.source_inv_uom')
        ->leftJoin('uom as new_ui', 'new_ui.uom_id', '=', 'transfer_dtl.dest_inv_uom')
        ->leftJoin('client_list as cl', 'cl.id', '=', 'th.source_company_id')
        ->leftJoin('store_list as s', 's.id', '=', 'th.source_store_id')
        ->leftJoin('users as u', 'u.id', '=', 'th.created_by')
        ->groupBy('transfer_dtl.id')
        ->orderBy('th.trans_date','ASC')
        ->where('th.status','posted');
         if($this->request->has('transfer_no') && $this->request->transfer_no !='') {
             $data_list->where('th.ref_no', $this->request->transfer_no);
         }
         if($this->request->has('transfer_date') && $this->request->transfer_date !=''){
           $data_list->when($this->request->has('transfer_date'), function ($q) {
                $date_split = explode(" to ", $this->request->transfer_date);
                $from = date('Y-m-d', strtotime($date_split[0])) . " 00:00:00";
                $to = date('Y-m-d', strtotime($date_split[1])) . " 23:59:59";
                $q->whereBetween('th.trans_date', [$from, $to]);
           });
        }
        $data_list->groupBy(
                'transfer_dtl.id',
                'transfer_dtl.product_id',
                'cl.client_name',
                's.store_name',
                'th.created_at',
                'u.name',
            );
        $result = $data_list->get();
        return $result;
    }

    public function headings(): array
    {
        return [
        'Transfer Date',
        'Reference No',
        'Company Name',
        'Site Name',
        'Dr No',
        'Start Encoding',
        'Finish Encoding',
        'Product Code',
		'Product Description',
        'Source Item Type',
        'Source Location',
        'Source Inv Qty',
        'Source Unit',
        'Destination Item Type',
        'Destination Location',
        'Destination Inv Qty',
        'Destination Unit',
        'Remarks',
        'Requested By',
        'Transfer By',
       ];
	}
}
