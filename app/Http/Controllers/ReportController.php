<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\MasterfileModel;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\Store;
use App\Models\RcvHdr;
use App\Models\RcvDtl;
use App\Models\Products;

use App\Exports\ExportRcvDetailed;

use DataTables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel;

use PDF;

class ReportController extends Controller
{
    public function getStockLedgerIndex(Request $request)
    {
        $supplier_list = Supplier::all();
        $client_list = Client::where('is_enabled', '1')->get();
        
        return view('report/stock_ledger', [
            'request'=>$request,
            'supplier_list'=>$supplier_list,
            'client_list'=>$client_list,
        ]);
    }

    public function getStockLedger(Request $request)
    {
        $supplier_list = Supplier::all();
        $client_list = Client::where('is_enabled', '1')->get();
        
        $validator = Validator::make($request->all(), [
            'client'=>'required',
            'store'=>'required',
            'warehouse'=>'required',
            'product_id' => 'required',
            'item_type' => 'required',
        ], [
            'client'=>'Client  is required',
            'store'=>'Store is required',
            'warehouse'=>'Warehouse is required',
            'product_id'=>'Product is required',
            'item_type'=>'Item type is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $beg_balance = $this->getBegginingBalance($request);

        $result = $this->getStockLedgerResult($request);

        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'beg_balance'=> $beg_balance,
            'result'    => $result
        ]);
    }

    public function getReceivingDetailedIndex(Request $request)
    {
        $client_list = Client::where('is_enabled', '1')->get();
        return view('report/receiving_detailed', [
            'client_list'=>$client_list,
            'request'=>$request,
        ]);
    }

    public function getReceivingDetailed(Request $request)
    {
        $supplier_list = Supplier::all();
        $client_list = Client::where('is_enabled', '1')->get();

        $rcv = RcvHdr::select('rcv_hdr.*', 'p.product_code', 'p.product_name','rd.*', 'uw.code as uw_code', 'ui.code as ui_code', )
                ->leftJoin('rcv_dtl as rd', 'rd.rcv_no', '=', 'rcv_hdr.rcv_no')
                ->leftJoin('products as p', 'p.product_id', '=', 'rd.product_id')
                ->leftJoin('uom as uw', 'uw.uom_id', '=', 'rd.whse_uom')
                ->leftJoin('uom as ui', 'ui.uom_id', '=', 'rd.inv_uom');

        if($request->has('rcv_no') && $request->rcv_no !='')
            $rcv->where('rcv_hdr.rcv_no', $request->rcv_no);
        
        if($request->has('client')  && $request->client !='')
            $rcv->where('rcv_hdr.client_id', $request->client);
        
        if($request->has('store')  && $request->store !='')
            $rcv->where('rcv_hdr.store_id', $request->store);
        
        if($request->has('warehouse')  && $request->warehouse !='')
            $rcv->where('rcv_hdr.warehouse_id', $request->warehouse);

        if($request->has('product_code')  && $request->product_code !='')
            $rcv->where('p.product_code', $request->product_code);
        
        if($request->has('item_type')  && $request->item_type !='')
            $rcv->where('rd.item_type', $request->item_type);

        if($request->has('date_received')  && $request->date_received !='' ) {
            $date_split = explode(" to ",$request->date_received);
            $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";
            $to = date('Y-m-d',  strtotime($date_split[1]))." 023:59:59";
            $rcv->whereBetween('date_received', [$from, $to]);
        }

        if($request->has('product_name')  && $request->product_name !='')
            $rcv->where('p.product_name','LIKE','%'.$request->product_name.'%');

        $result = $rcv->get();

        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'data'    => $result,
        ]);
    }

    function exportReceivingDetailed(Request $request) {
        ob_start();
		$file_name = 'export-receiving-detailed'.date('Ymd-His').'.xls'; 
        return Excel::download(new ExportRcvDetailed($request), $file_name);
    }

    function printPdfReceivingDetailed(Request $request) {

        $rcv = RcvHdr::select('rcv_hdr.*', 'p.product_code', 'p.product_name','rd.*', 'uw.code as uw_code', 'ui.code as ui_code', )
                ->leftJoin('rcv_dtl as rd', 'rd.rcv_no', '=', 'rcv_hdr.rcv_no')
                ->leftJoin('products as p', 'p.product_id', '=', 'rd.product_id')
                ->leftJoin('uom as uw', 'uw.uom_id', '=', 'rd.whse_uom')
                ->leftJoin('uom as ui', 'ui.uom_id', '=', 'rd.inv_uom');

        if($request->has('rcv_no') && $request->rcv_no !='')
            $rcv->where('rcv_hdr.rcv_no', $request->rcv_no);
        
        if($request->has('client')  && $request->client !='')
            $rcv->where('rcv_hdr.client_id', $request->client);
        
        if($request->has('store')  && $request->store !='')
            $rcv->where('rcv_hdr.store_id', $request->store);
        
        if($request->has('warehouse')  && $request->warehouse !='')
            $rcv->where('rcv_hdr.warehouse_id', $request->warehouse);

        if($request->has('product_code')  && $request->product_code !='')
            $rcv->where('p.product_code', $request->product_code);
        
        if($request->has('item_type')  && $request->item_type !='')
            $rcv->where('rd.item_type', $request->item_type);

        if($request->has('date_received')  && $request->date_received !='' ) {
            $date_split = explode(" to ",$request->date_received);
            $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";
            $to = date('Y-m-d',  strtotime($date_split[1]))." 023:59:59";
            $rcv->whereBetween('date_received', [$from, $to]);
        }

        if($request->has('product_name')  && $request->product_name !='')
            $rcv->where('p.product_name','LIKE','%'.$request->product_name.'%');

        $result = $rcv->get();

        $data = [
            'title' =>'Receiving Detailed | Report Summary',
            'date' => date('m/d/Y'),
            'result' => $result
        ];

        $pdf = PDF::loadView('report/receiving_detailed_pdf', $data);
        $pdf->setPaper('A4','landscape');
        $pdf->setOption('margin', 0);
        return $pdf->stream();
    }

    function getBegginingBalance($request) {
        $rcv = MasterfileModel::select('masterfiles.product_id', DB::raw("SUM(inv_qty) as inv_qty"), DB::raw("SUM(whse_qty) as whse_qty"))
            ->leftJoin('products as p', 'p.product_id', '=', 'masterfiles.product_id')
            ->leftJoin('uom as uw', 'uw.uom_id', '=', 'masterfiles.whse_uom')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'masterfiles.inv_uom')
            ->orderBy("masterfiles.created_at")
            ->groupBy('masterfiles.product_id');
        
     
        if($request->has('client')  && $request->client !='')
            $rcv->where('masterfiles.client_id', $request->client);
        
        if($request->has('store')  && $request->store !='')
            $rcv->where('masterfiles.store_id', $request->store);
        
        if($request->has('warehouse')  && $request->warehouse !='')
            $rcv->where('masterfiles.warehouse_id', $request->warehouse);

        if($request->has('product_id')  && $request->product_id !='')
            $rcv->where('p.product_id', $request->product_id);
        
        if($request->has('item_type')  && $request->item_type !='')
            $rcv->where('masterfiles.item_type', $request->item_type);

        if($request->has('location')  && $request->location !='')
            $rcv->where('masterfiles.storage_location_id', $request->location);

        $rcv->where('masterfiles.created_at', '<', date('Y-01-01 00:00:00'));

        $data = $rcv->get();

        return $data;
    }

    function getStockLedgerResult($request) {
        $rcv = MasterfileModel::select('masterfiles.*','sl.location')
            ->whereBetween('masterfiles.created_at', [date('Y-01-01 00:00:00'), date('Y-m-d 23:59:59')])
            ->leftJoin('products as p', 'p.product_id', '=', 'masterfiles.product_id')
            ->leftJoin('uom as uw', 'uw.uom_id', '=', 'masterfiles.whse_uom')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'masterfiles.inv_uom')
            ->leftJoin('storage_locations as sl', 'sl.storage_location_id', '=', 'masterfiles.storage_location_id')
            ->orderBy("masterfiles.created_at");
        
        if($request->has('client')  && $request->client !='')
            $rcv->where('masterfiles.client_id', $request->client);
        
        if($request->has('store')  && $request->store !='')
            $rcv->where('masterfiles.store_id', $request->store);
        
        if($request->has('warehouse')  && $request->warehouse !='')
            $rcv->where('masterfiles.warehouse_id', $request->warehouse);

        if($request->has('product_id')  && $request->product_id !='')
            $rcv->where('p.product_id', $request->product_id);
        
        if($request->has('item_type')  && $request->item_type !='')
            $rcv->where('masterfiles.item_type', $request->item_type);

        if($request->has('location')  && $request->location !='')
            $rcv->where('masterfiles.storage_location_id', $request->location);
  
        $result = $rcv->get();

        return $result;
    }

    public function inventory(Request $request)
    {
        $client_list = Client::where('is_enabled', '1')->get();
        return view('report/inventory', [
            'client_list'=>$client_list,
            'request'=>$request,
        ]);
    }

    public function getInventoryReport(Request $request) {
        $rcv = MasterfileModel::select('client_name', 'store_name', 'w.warehouse_name', 'product_code', 'product_name', 'sl.location',  'masterfiles.whse_uom', 'masterfiles.inv_uom', 'masterfiles.item_type', 'masterfiles.status', 'uw.code as uw_code', 'ui.code as ui_code', DB::raw("SUM(inv_qty) as inv_qty"), DB::raw("SUM(whse_qty) as whse_qty"))
            ->leftJoin('products as p', 'p.product_id', '=', 'masterfiles.product_id')
            ->leftJoin('storage_locations as sl', 'sl.storage_location_id', '=', 'masterfiles.storage_location_id')
            ->leftJoin('client_list as cl', 'cl.id', '=', 'masterfiles.client_id')
            ->leftJoin('store_list as s', 's.id', '=', 'masterfiles.store_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'masterfiles.warehouse_id')
            ->leftJoin('uom as uw', 'uw.uom_id', '=', 'masterfiles.whse_uom')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'masterfiles.inv_uom')
            ->groupBy('client_name', 'store_name', 'w.warehouse_name', 'product_name', 'sl.location','masterfiles.item_type','masterfiles.status', 'masterfiles.whse_uom', 'masterfiles.inv_uom')
            ->having('inv_qty',  '>', 0)
            ->orderBy('product_name')
            ->orderBy('sl.location');
        
        if($request->has('client')  && $request->client !='')
            $rcv->where('masterfiles.client_id', $request->client);
        
        if($request->has('store')  && $request->store !='')
            $rcv->where('masterfiles.store_id', $request->store);
        
        if($request->has('warehouse')  && $request->warehouse !='')
            $rcv->where('masterfiles.warehouse_id', $request->warehouse);

        if($request->has('product_id')  && $request->product_id !='')
            $rcv->where('p.product_id', $request->product_id);
        
        if($request->has('item_type')  && $request->item_type !='')
            $rcv->where('masterfiles.item_type', $request->item_type);

        if($request->has('location')  && $request->location !='')
            $rcv->where('masterfiles.storage_location_id', $request->location);
  
        $result = $rcv->get();


        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'result'    => $result,
        ]);

    }

}
