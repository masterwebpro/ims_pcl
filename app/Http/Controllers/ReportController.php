<?php

namespace App\Http\Controllers;

use App\Exports\ExportCurrentStocks;
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
use App\Exports\ExportWdDetailed;
use App\Exports\ExportInventory;
use App\Exports\ExportOutboundMonitoring;
use App\Exports\ExportInboundMonitoring;
use App\Exports\ExportAging;
use App\Models\DispatchDtl;
use App\Models\DispatchHdr;
use App\Models\MasterdataModel;
use App\Models\OrderType;
use App\Models\WdHdr;
use DataTables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function getStockLedgerIndex(Request $request)
    {
        $supplier_list = Supplier::all();
        // $client_list = Client::where('is_enabled', '1')->get();
        $client_list = Client::where('client_type','O')->where('is_enabled', '1')->get();

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
            'product_id' => 'required',
            'item_type' => 'required',
            'date_range' => 'required',
        ], ['*'=>'This field is required' ]);

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
            $rcv->where('rcv_hdr.customer_id', $request->client);

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
            $rcv->where('masterfiles.company_id', $request->client);

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

        
        $date_split = explode(" to ",$request->date_range);

        $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";

        if(isset($date_split[1])) {
            $to = date('Y-m-d',  strtotime($date_split[1]))." 23:59:59";
        } else {
            $to = date('Y-m-d',  strtotime($date_split[0]))." 23:59:59";
        }

        
        $rcv->where('masterfiles.created_at', '<', $from);

        $data = $rcv->get();

        return $data;
    }

    function getStockLedgerResult($request) {

        $date_split = explode(" to ",$request->date_range);

        $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";

        if(isset($date_split[1])) {
            $to = date('Y-m-d',  strtotime($date_split[1]))." 23:59:59";
        } else {
            $to = date('Y-m-d',  strtotime($date_split[0]))." 23:59:59";
        }


        $rcv = MasterfileModel::select('masterfiles.*','sl.location')
            ->whereBetween('masterfiles.created_at', [$from, $to])
            ->leftJoin('products as p', 'p.product_id', '=', 'masterfiles.product_id')
            ->leftJoin('uom as uw', 'uw.uom_id', '=', 'masterfiles.whse_uom')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'masterfiles.inv_uom')
            ->leftJoin('storage_locations as sl', 'sl.storage_location_id', '=', 'masterfiles.storage_location_id')
            ->orderBy("masterfiles.created_at");

        if($request->has('client')  && $request->client !='')
            $rcv->where('masterfiles.company_id', $request->client);

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
        $rcv = MasterdataModel::select('client_name', 'store_name', 'w.warehouse_name',  'sap_code',  'product_code', 'product_name',  'sl.location',  'masterdata.whse_uom', 'masterdata.inv_uom', 'masterdata.item_type',  'rd.lot_no', 'rd.manufacture_date', 'rd.expiry_date', 'uw.code as uw_code', 'ui.code as ui_code', DB::raw("SUM(masterdata.inv_qty) as inv_qty"), DB::raw("SUM(masterdata.whse_qty) as whse_qty"))
            ->leftJoin('products as p', 'p.product_id', '=', 'masterdata.product_id')
            ->leftJoin('storage_locations as sl', 'sl.storage_location_id', '=', 'masterdata.storage_location_id')
            ->leftJoin('client_list as cl', 'cl.id', '=', 'masterdata.company_id')
            ->leftJoin('store_list as s', 's.id', '=', 'masterdata.store_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'masterdata.warehouse_id')
            ->leftJoin('uom as uw', 'uw.uom_id', '=', 'masterdata.whse_uom')
            ->leftJoin('uom as ui', 'ui.uom_id', '=', 'masterdata.inv_uom')
            ->leftJoin('rcv_dtl as rd', 'rd.id', '=', 'masterdata.rcv_dtl_id')
            ->groupBy('client_name', 'store_name', 'w.warehouse_name', 'product_name', 'sl.location','masterdata.item_type', 'masterdata.whse_uom', 'masterdata.inv_uom', 'rd.lot_no', 'rd.manufacture_date', 'rd.expiry_date')
            ->having('inv_qty',  '>', 0)
            ->orderBy('product_name')
            ->orderBy('sl.location');

        if($request->has('client')  && $request->client !='')
            $rcv->where('masterdata.customer_id', $request->client);

        if($request->has('store')  && $request->store !='')
            $rcv->where('masterdata.store_id', $request->store);

        if($request->has('warehouse')  && $request->warehouse !='')
            $rcv->where('masterdata.warehouse_id', $request->warehouse);

        if($request->has('product_id')  && $request->product_id !='')
            $rcv->where('p.product_id', $request->product_id);

        if($request->has('item_type')  && $request->item_type !='')
            $rcv->where('masterdata.item_type', $request->item_type);

        if($request->has('location')  && $request->location !='')
            $rcv->where('masterdata.storage_location_id', $request->location);

        $result = $rcv->get();


        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'result'    => $result,
        ]);

    }

    public function getWithdrawalDetailedIndex(Request $request)
    {
        $client_list = Client::where('is_enabled', '1')->get();
        $order_type = OrderType::all();
        return view('report/withdrawal_detailed', [
            'client_list'=>$client_list,
            'order_type'=>$order_type,
            'request'=>$request,
        ]);
    }

    public function getWithdrawalDetailed(Request $request)
    {
        $wd = WdHdr::select('wd_hdr.*', 'p.product_code', 'p.product_name','wd.*', 'ui.code as ui_code', )
                ->leftJoin('wd_dtl as wd', 'wd.wd_no', '=', 'wd_hdr.wd_no')
                ->leftJoin('products as p', 'p.product_id', '=', 'wd.product_id')
                ->leftJoin('uom as ui', 'ui.uom_id', '=', 'wd.inv_uom')
                ->where('wd_hdr.status','posted');

        if($request->has('wd_no') && $request->wd_no !='')
            $wd->where('wd_hdr.wd_no', $request->wd_no);

        if($request->has('client')  && $request->client !='')
            $wd->where('wd_hdr.customer_id', $request->client);

        if($request->has('store')  && $request->store !='')
            $wd->where('wd_hdr.store_id', $request->store);

        if($request->has('warehouse')  && $request->warehouse !='')
            $wd->where('wd_hdr.warehouse_id', $request->warehouse);

        if($request->has('product_code')  && $request->product_code !='')
            $wd->where('p.product_code', $request->product_code);

        if($request->has('order_type')  && $request->order_type !='')
            $wd->where('wd_hdr.order_type', $request->order_type);

        if($request->has('withdraw_date')  && $request->withdraw_date !='' ) {
            $date_split = explode(" to ",$request->withdraw_date);
            $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";
            $to = date('Y-m-d',  strtotime($date_split[1]))." 023:59:59";
            $wd->whereBetween('withdraw_date', [$from, $to]);
        }

        if($request->has('product_name')  && $request->product_name !='')
            $wd->where('p.product_name','LIKE','%'.$request->product_name.'%');

        $result = $wd->get();

        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'data'    => $result,
        ]);
    }

    function exportWithdrawalDetailed(Request $request) {
        ob_start();
		$file_name = 'export-withdrawal-detailed'.date('Ymd-His').'.xls';
        return Excel::download(new ExportWdDetailed($request), $file_name);
    }

    function printPdfWithdrawalDetailed(Request $request) {
        ob_start();
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        $wd = wdHdr::select('wd_hdr.*', 'p.product_code', 'p.product_name','wd.*','ui.code as ui_code', )
                ->leftJoin('wd_dtl as wd', 'wd.wd_no', '=', 'wd_hdr.wd_no')
                ->leftJoin('products as p', 'p.product_id', '=', 'wd.product_id')
                ->leftJoin('uom as ui', 'ui.uom_id', '=', 'wd.inv_uom')
                ->where('wd_hdr.status','posted');

        if($request->has('wd_no') && $request->wd_no !='')
            $wd->where('wd_hdr.wd_no', $request->wd_no);

        if($request->has('client')  && $request->client !='')
            $wd->where('wd_hdr.client_id', $request->client);

        if($request->has('store')  && $request->store !='')
            $wd->where('wd_hdr.store_id', $request->store);

        if($request->has('warehouse')  && $request->warehouse !='')
            $wd->where('wd_hdr.warehouse_id', $request->warehouse);

        if($request->has('product_code')  && $request->product_code !='')
            $wd->where('p.product_code', $request->product_code);

        if($request->has('order_type')  && $request->order_type !='')
            $wd->where('wd_hdr.order_type', $request->order_type);

        if($request->has('withdraw_date')  && $request->withdraw_date !='' ) {
            $date_split = explode(" to ",$request->withdraw_date);
            $from = date('Y-m-d', strtotime($date_split[0]))." 00:00:00";
            $to = date('Y-m-d',  strtotime($date_split[1]))." 023:59:59";
            $wd->whereBetween('withdraw_date', [$from, $to]);
        }

        if($request->has('product_name')  && $request->product_name !='')
            $wd->where('p.product_name','LIKE','%'.$request->product_name.'%');

        $result = $wd->get();

        $data = [
            'title' =>'Withdrawal Detailed | Report Summary',
            'date' => date('m/d/Y'),
            'result' => $result
        ];

        $pdf = PDF::loadView('report/withdrawal_detailed_pdf', $data);
        $pdf->setPaper('A4','landscape');
        $pdf->setOption('margin', 0);
        return $pdf->stream();
    }

    function exportInventory(Request $request) {
        ob_start();
		$file_name = 'inventory_summary'.date('Ymd-His').'.xls';
        return Excel::download(new ExportInventory($request), $file_name);
    }

    public function getOutboundMonitoringIndex(Request $request)
    {
        $dateRangeParts = explode(" to ", $request->date);
        $startDate = isset($dateRangeParts[0]) ? $dateRangeParts[0] : "";
        $endDate = isset($dateRangeParts[1]) ? $dateRangeParts[1] : "";

        $client_list = Client::where('is_enabled', '1')->get();
        $data_list = DispatchDtl::select('dispatch_dtl.*',
                        DB::raw('WEEK(dh.dispatch_date) as week_no'),
                        'dh.dispatch_date',
                        'dh.dispatch_by',
                        'dh.trucker_name',
                        'dh.truck_type',
                        'dh.plate_no',
                        'dh.driver',
                        'dh.contact_no',
                        'dh.helper',
                        'dh.seal_no',
                        'dh.start_datetime',
                        'dh.finish_datetime',
                        'dh.depart_datetime',
                        'dh.start_picking_datetime',
                        'dh.finish_picking_datetime',
                        'dh.arrival_datetime',
                        'u.name',
                        'p.product_code',
                        'p.product_name',
                        'ui.code as unit',
                        'rd.lot_no',
                        'rd.expiry_date',
                        'rd.manufacture_date',
                        'm.item_type',
                        'wh.dr_no',
                        'wh.po_num',
                        'wh.order_no',
                        'wh.sales_invoice',
                        's.supplier_name',
                        'cat.category_name',
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
        if ($s = $request->status) {
            if($s != 'all')
                $data_list->orWhere('dispatch_hdr.status', $s);
        }

        if ($request->q) {
            $data_list->where(function($q)use($request){
                $q->where('dh.dispatch_no', $request->q)
                ->orWhere('dh.dispatch_by', $request->q)
                ->orWhere('dh.trucker_name', $request->q)
                ->orWhere('dh.truck_type', $request->q)
                ->orWhere('dh.plate_no', $request->q)
                ->orWhere('dh.driver', $request->q)
                ->orWhere('dh.driver', $request->q)
                ->orWhere('dh.contact_no', $request->q)
                ->orWhere('dh.helper', $request->q)
                ->orWhere('dh.seal_no', $request->q);
            });
        }

        if ($request->filter_date && $request->date) {
            if($request->filter_date == 'dispatch_date') {
                $data_list->whereBetween('dh.dispatch_date', [$request->date." 00:00:00", $request->date." 23:59:00"]);
            }
            if($request->filter_date == 'created_at') {
                $data_list->whereBetween('dh.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
            }

            if($request->filter_date == 'dispatch_date' && $startDate && $endDate)
            {
                $data_list->whereBetween('dh.dispatch_date',[$startDate." 00:00:00",$endDate." 23:59:00"]);
            }

            if($request->filter_date == 'created_at' && $startDate && $endDate)
            {
                $data_list->whereBetween('dh.created_at',[$startDate." 00:00:00",$endDate." 23:59:00"]);
            }

        }

        if($request->customer ){
            $data_list->where('wh.customer_id', $request->customer);
        }
        if($request->company){
            $data_list->where('wh.company_id', $request->company);
        }

        $data = $data_list->paginate(20);
        return view('report/outbound_monitoring', [
            'client_list'=>$client_list,
            'data_list'=>$data,
            'request'=>$request,
        ]);
    }

    function exportCurrentStocks(Request $request) {
        ob_start();
		$file_name = 'export-current-stocks'.date('Ymd-His').'.xls';
        return Excel::download(new ExportCurrentStocks($request), $file_name);
    }

    function exportOutboundMonitoring(Request $request) {
        ob_start();
            $data_list = DispatchDtl::select(
                DB::raw('WEEK(dh.dispatch_date) as week_no'),
                'dh.dispatch_date',
                'dh.truck_type',
                'dh.trucker_name',
                'wh.dr_no',
                'dh.plate_no',
                'dh.seal_no',
                'dh.driver',
                'wh.po_num',
                'wh.sales_invoice',
                'wh.order_no',
                's.supplier_name',
                'cat.category_name',
                'p.product_code',
                'p.product_name',
                'rd.lot_no',
                'dispatch_dtl.qty',
                'ui.code as unit',
                'rd.manufacture_date',
                'rd.expiry_date',
                'dispatch_dtl.dispatch_no',
                'm.item_type',
                'dh.start_picking_datetime',
                'dh.finish_picking_datetime',
                'dh.start_datetime',
                'dh.finish_datetime',
                'dh.depart_datetime',
                'dh.start_picking_datetime',
                'dh.finish_picking_datetime',
                'dh.arrival_datetime',
                'u.name',
                'dh.dispatch_by',
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
        if ($request->q) {
            $data_list->where('dispatch_hdr.dispatch_no', $request->q)
                ->orWhere('dispatch_hdr.dispatch_by', $request->q)
                ->orWhere('dispatch_hdr.trucker_name', $request->q)
                ->orWhere('dispatch_hdr.truck_type', $request->q)
                ->orWhere('dispatch_hdr.plate_no', $request->q)
                ->orWhere('dispatch_hdr.driver', $request->q)
                ->orWhere('dispatch_hdr.driver', $request->q)
                ->orWhere('dispatch_hdr.contact_no', $request->q)
                ->orWhere('dispatch_hdr.helper', $request->q)
                ->orWhere('dispatch_hdr.seal_no', $request->q);
        }

        if ($request->filter_date && $request->date) {
            if($request->filter_date == 'dispatch_date') {
                $data_list->whereBetween('dispatch_hdr.dispatch_date', [$request->date." 00:00:00", $request->date." 23:59:00"]);
            }
            if($request->filter_date == 'created_at') {
                $data_list->whereBetween('dispatch_hdr.created_at', [$request->created_at." 00:00:00", $request->created_at." 23:59:00"]);
            }

        }
        if($request->customer ){
            $data_list->where('wh.customer_id', $request->customer);
        }
        if($request->company){
            $data_list->where('wh.company_id', $request->company);
        }
        $result = $data_list->get();
        if($result)
        {
            $data = [];
            foreach($result as $res){
                $data[] = array(
                    $res->week_no,
                    $res->dispatch_date,
                    $res->truck_type,
                    $res->trucker_name,
                    $res->dr_no,
                    $res->seal_no,
                    $res->plate_no,
                    $res->driver,
                    $res->po_num,
                    $res->order_no,
                    $res->sales_invoice,
                    $res->supplier_name,
                    $res->category_name,
                    $res->product_code,
                    $res->product_name,
                    $res->lot_no,
                    'N/A',
                    'N/A',
                    $res->qty,
                    $res->unit,
                    $res->manufacture_date,
                    $res->expiry_date,
                    $res->dispatch_no,
                    strtoupper($res->item_type),
                    "OUTBOUND",
                    "N/A",
                    date('H:i A', strtotime($res->start_picking_datetime)),
                    date('H:i A', strtotime($res->finish_picking_datetime)),
                    date('H:i A', strtotime($res->arrival_datetime)),
                    date('H:i A', strtotime($res->start_datetime)),
                    date('H:i A', strtotime($res->finish_datetime)),
                    date('H:i A', strtotime($res->depart_datetime)),
                    timeInterval($res->start_datetime, $res->finish_datetime),
                    timeInterval($res->arrival_datetime, $res->depart_datetime),
                    "N/A",
                    $res->name,
                    $res->dispatch_by
                );
            }

        }
		$file_name = 'export-outbound-monitoring'.date('Ymd-His').'.xls';
        return Excel::download(new ExportOutboundMonitoring($data), $file_name);
    }

    public function getAgingIndex(Request $request)
    {
        $client_list = Client::where('is_enabled', '1')->get();
        $result = MasterdataModel::select(
                'p.product_code',
                'p.product_name',
                'masterdata.inv_qty',
                'rh.date_received',
                DB::raw('DATEDIFF(now(),rh.date_received) as diff_days')
                )
                ->leftJoin('rcv_dtl as rd', 'rd.id', '=', 'masterdata.rcv_dtl_id')
                ->leftJoin('rcv_hdr as rh', 'rh.rcv_no', '=', 'rd.rcv_no')
                ->leftJoin('products as p', 'p.product_id', '=', 'masterdata.product_id')
                ->groupBy(['masterdata.product_id','rh.date_received']);
                if ($request->q) {
                    $result->where(function($q)use($request){
                        $q->where('p.product_code', $request->q)
                        ->orWhere('p.product_name', $request->q);
                    });
                }
                if($request->filter_date == 'filter_date' && $request->date) {
                    $result->whereBetween('rh.date_received', [$request->date." 00:00:00", $request->date." 23:59:59"]);
                }
                if($request->customer ){
                    $result->where('masterdata.customer_id', $request->customer);
                }
                if($request->company){
                    $result->where('masterdata.company_id', $request->company);
                }
        $data  = $result->get();
        $xdata = array();
        foreach($data as $res){
            $product_code = $res->product_code;
            if(!isset($xdata[$product_code]))
            {
                $xdata[$product_code]['days30'] = ($res->diff_days <= 30) ? $res->inv_qty : 0;
                $xdata[$product_code]['days60'] = ($res->diff_days > 30 && $res->diff_days <= 60) ? $res->inv_qty : 0;
                $xdata[$product_code]['days90'] = ($res->diff_days > 60 && $res->diff_days <= 90) ? $res->inv_qty : 0;
                $xdata[$product_code]['days120'] = ($res->diff_days > 90 && $res->diff_days <= 120) ? $res->inv_qty : 0;
                $xdata[$product_code]['days150'] = ($res->diff_days > 120 && $res->diff_days <= 150) ? $res->inv_qty : 0;
                $xdata[$product_code]['over150days'] = ($res->diff_days > 150) ? $res->inv_qty : 0;
                $xdata[$product_code] = $res;
            }
            else{
                $xdata[$product_code]['inv_qty'] += $res->inv_qty;
                $xdata[$product_code]['days30'] += ($res->diff_days <= 30) ? $res->inv_qty : 0;;
                $xdata[$product_code]['days60'] += ($res->diff_days > 30 && $res->diff_days <= 60) ? $res->inv_qty : 0;
                $xdata[$product_code]['days90'] += ($res->diff_days > 60 && $res->diff_days <= 90) ? $res->inv_qty : 0;
                $xdata[$product_code]['days120'] += ($res->diff_days > 90 && $res->diff_days <= 120) ? $res->inv_qty : 0;
                $xdata[$product_code]['days150'] += ($res->diff_days > 120 && $res->diff_days <= 150) ? $res->inv_qty : 0;
                $xdata[$product_code]['over150days'] += ($res->diff_days > 150) ? $res->inv_qty : 0;
            }
        }

        $result = paginate($xdata,20);
        return view('report/aging', [
            'client_list'=>$client_list,
            'request'=>$request,
            'data_list'=> $result,
        ]);
    }

    function exportAging(Request $request) {
        ob_start();
        $file_name = 'export-aging'.date('Ymd-His').'.xls';
        $result = MasterdataModel::select(
            'p.product_code',
            'p.product_name',
            'masterdata.inv_qty',
            'rh.date_received',
            DB::raw('DATEDIFF(now(),rh.date_received) as diff_days')
            )
            ->leftJoin('rcv_dtl as rd', 'rd.id', '=', 'masterdata.rcv_dtl_id')
            ->leftJoin('rcv_hdr as rh', 'rh.rcv_no', '=', 'rd.rcv_no')
            ->leftJoin('products as p', 'p.product_id', '=', 'masterdata.product_id')
            ->groupBy(['masterdata.product_id','rh.date_received']);
            if ($request->q) {
                $result->where(function($q)use($request){
                    $q->where('p.product_code', $request->q)
                    ->orWhere('p.product_name', $request->q);
                });
            }
            if($request->date) {
                $result->whereBetween('rh.date_received', [$request->date." 00:00:00", $request->date." 23:59:59"]);
            }

            if($request->customer ){
                $result->where('masterdata.customer_id', $request->customer);
            }
            if($request->company){
                $result->where('masterdata.company_id', $request->company);
            }

        $data  = $result->get();
        $xdata = array();
        foreach($data as $res){
            $product_code = $res->product_code;
            if(!isset($xdata[$product_code]))
            {
                $xdata[$product_code]['days30'] = ($res->diff_days <= 30) ? $res->inv_qty : 0;
                $xdata[$product_code]['days60'] = ($res->diff_days > 30 && $res->diff_days <= 60) ? $res->inv_qty : 0;
                $xdata[$product_code]['days90'] = ($res->diff_days > 60 && $res->diff_days <= 90) ? $res->inv_qty : 0;
                $xdata[$product_code]['days120'] = ($res->diff_days > 90 && $res->diff_days <= 120) ? $res->inv_qty : 0;
                $xdata[$product_code]['days150'] = ($res->diff_days > 120 && $res->diff_days <= 150) ? $res->inv_qty : 0;
                $xdata[$product_code]['over150days'] = ($res->diff_days > 150) ? $res->inv_qty : 0;
                $xdata[$product_code] = $res;
            }
            else{
                $xdata[$product_code]['inv_qty'] += $res->inv_qty;
                $xdata[$product_code]['days30'] += ($res->diff_days <= 30) ? $res->inv_qty : 0;;
                $xdata[$product_code]['days60'] += ($res->diff_days > 30 && $res->diff_days <= 60) ? $res->inv_qty : 0;
                $xdata[$product_code]['days90'] += ($res->diff_days > 60 && $res->diff_days <= 90) ? $res->inv_qty : 0;
                $xdata[$product_code]['days120'] += ($res->diff_days > 90 && $res->diff_days <= 120) ? $res->inv_qty : 0;
                $xdata[$product_code]['days150'] += ($res->diff_days > 120 && $res->diff_days <= 150) ? $res->inv_qty : 0;
                $xdata[$product_code]['over150days'] += ($res->diff_days > 150) ? $res->inv_qty : 0;
            }
        }
        $data = array();
        foreach($xdata as $res){
            $data[] = array(
                $res['product_code'],
                $res['product_name'],
                date('Y/m/d',strtotime($res['date_received'])),
                number_format($res['inv_qty'],2,'.',''),
                number_format($res['days30'],2,'.',''),
                number_format($res['days60'],2,'.',''),
                number_format($res['days90'],2,'.',''),
                number_format($res['days120'],2,'.',''),
                number_format($res['days150'],2,'.',''),
                number_format($res['over150days'],2,'.','')
            );
        }

        return Excel::download(new ExportAging($data), $file_name);
    }

    public function getInboundMonitoringIndex(Request $request)
    {
        $dateRangeParts = explode(" to ", $request->date);
        $startDate = isset($dateRangeParts[0]) ? $dateRangeParts[0] : "";
        $endDate = isset($dateRangeParts[1]) ? $dateRangeParts[1] : "";

        $client_list = Client::where('is_enabled', '1')->get();
        $data_list = RcvDtl::select('rcv_dtl.*',
                        DB::raw('WEEK(rh.date_received) as week_no'),
                        'rh.date_received',
                        'rh.received_by',
                        // 'rh.trucker_name',
                        'tt.vehicle_code',
                        'tt.vehicle_desc',
                        'rh.plate_no',
                        // 'rh.driver',
                        // 'rh.contact_no',
                        // 'rh.helper',
                        // 'rh.seal_no',
                        // 'rh.start_datetime',
                        // 'rh.finish_datetime',
                        'rh.date_departed',
                        // 'rh.start_unloading',
                        // 'rh.finish_unloading',
                        'rh.date_arrived',
                        'u.name',
                        'p.product_code',
                        'p.product_name',
                        'ui.code as unit',
                        'rh.po_num',
                        'rh.sales_invoice',
                        's.supplier_name',
                        'cat.category_name',
                         )
        ->leftJoin('rcv_hdr as rh', 'rh.rcv_no', '=', 'rcv_dtl.rcv_no')
        ->leftJoin('products as p', 'p.product_id', '=', 'rcv_dtl.product_id')
        ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id')
        ->leftJoin('category_brands as cb', 'cb.category_brand_id', '=', 'p.category_brand_id')
        ->leftJoin('categories as cat', 'cat.category_id', '=', 'cb.category_id')
        ->leftJoin('uom as ui', 'ui.uom_id', '=', 'rcv_dtl.inv_uom')
        ->leftJoin('users as u', 'u.id', '=', 'rh.created_by')
        ->leftJoin('truck_type as tt', 'tt.id', '=', 'rh.truck_type')
        ->groupBy('rcv_dtl.id')
        ->orderBy('rh.date_received','ASC')
        ->where('rh.status','posted');

        if ($request->q) {
            $data_list->where(function($q)use($request){
                $q->where('rh.rcv_no', $request->q)
                ->orWhere('rh.received_by', $request->q)
                ->orWhere('rh.vehicle_code', $request->q)
                ->orWhere('rh.vehicle_desc', $request->q)
                ->orWhere('rcv_dtl.product_code', $request->q)
                ->orWhere('rcv_dtl.product_name', $request->q)
                ->orWhere('rh.plate_no', $request->q);
            });
        }

        if($request->filter_date == 'received_date' && $request->date) {
            $data_list->whereBetween('rh.date_received', [$request->date." 00:00:00", $request->date." 23:59:00"]);
        }
        if($request->filter_date == 'created_at' && $request->date) {
            $data_list->whereBetween('rd.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
        }
        if($request->date){
            if($request->filter_date == 'received_date' && $startDate && $endDate)
            {
                $data_list->whereBetween('rh.date_received',[$startDate,$endDate]);
            }

            if($request->filter_date == 'created_at' && $startDate && $endDate)
            {
                $data_list->whereBetween('rd.created_at',[$startDate,$endDate]);
            }
        }

         if($request->customer ){
            $data_list->where('rh.customer_id', $request->customer);
        }
        if($request->company){
            $data_list->where('rh.company_id', $request->company);
        }

        $data = $data_list->paginate(20);
        return view('report/inbound_monitoring', [
            'client_list'=>$client_list,
            'data_list'=>$data,
            'request'=>$request,
        ]);
    }

    public function exportInboundMonitoring(Request $request)
    {
        ob_start();
        $dateRangeParts = explode(" to ", $request->date);
        $startDate = isset($dateRangeParts[0]) ? $dateRangeParts[0] : "";
        $endDate = isset($dateRangeParts[1]) ? $dateRangeParts[1] : "";

        $data_list = RcvDtl::select('rcv_dtl.*',
                        DB::raw('WEEK(rh.date_received) as week_no'),
                        'rh.date_received',
                        'rh.received_by',
                        // 'rh.trucker_name',
                        'tt.vehicle_code',
                        'tt.vehicle_desc',
                        'rh.plate_no',
                        // 'rh.driver',
                        // 'rh.contact_no',
                        // 'rh.helper',
                        // 'rh.seal_no',
                        // 'rh.start_datetime',
                        // 'rh.finish_datetime',
                        'rh.date_departed',
                        'rh.remarks as remark',
                        // 'rh.start_unloading',
                        // 'rh.finish_unloading',
                        'rh.date_arrived',
                        'rh.remarks as remark',
                        'u.name',
                        'p.product_code',
                        'p.product_name',
                        'ui.code as unit',
                        'rh.po_num',
                        'rh.sales_invoice',
                        's.supplier_name',
                        'cat.category_name',
                         )
        ->leftJoin('rcv_hdr as rh', 'rh.rcv_no', '=', 'rcv_dtl.rcv_no')
        ->leftJoin('products as p', 'p.product_id', '=', 'rcv_dtl.product_id')
        ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id')
        ->leftJoin('category_brands as cb', 'cb.category_brand_id', '=', 'p.category_brand_id')
        ->leftJoin('categories as cat', 'cat.category_id', '=', 'cb.category_id')
        ->leftJoin('uom as ui', 'ui.uom_id', '=', 'rcv_dtl.inv_uom')
        ->leftJoin('users as u', 'u.id', '=', 'rh.created_by')
        ->leftJoin('truck_type as tt', 'tt.id', '=', 'rh.truck_type')
        ->groupBy('rcv_dtl.id')
        ->orderBy('rh.date_received','ASC')
        ->where('rh.status','posted');

        if ($request->q) {
            $data_list->where(function($q)use($request){
                $q->where('rh.rcv_no', $request->q)
                ->orWhere('rh.received_by', $request->q)
                ->orWhere('rh.vehicle_code', $request->q)
                ->orWhere('rh.vehicle_desc', $request->q)
                ->orWhere('rcv_dtl.product_code', $request->q)
                ->orWhere('rcv_dtl.product_name', $request->q)
                ->orWhere('rh.plate_no', $request->q);
            });
        }

        if($request->filter_date == 'received_date' && $request->date) {
            $data_list->whereBetween('rh.date_received', [$request->date." 00:00:00", $request->date." 23:59:00"]);
        }
        if($request->filter_date == 'created_at' && $request->date) {
            $data_list->whereBetween('rd.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
        }
        if($request->date){
            if($request->filter_date == 'received_date' && $startDate && $endDate)
            {
                $data_list->whereBetween('rh.date_received',[$startDate,$endDate]);
            }

            if($request->filter_date == 'created_at' && $startDate && $endDate)
            {
                $data_list->whereBetween('rd.created_at',[$startDate,$endDate]);
            }
        }

        if($request->customer ){
            $data_list->where('rh.customer_id', $request->customer);
        }
        if($request->company){
            $data_list->where('rh.company_id', $request->company);
        }

        $result = $data_list->get();
        if($result)
        {
            $data = [];
            foreach($result as $res){
                $data[] = array(
                    $res->week_no,
                    $res->date_received,
                    $res->vehicle_code,
                    '-',
                    '-',
                    $res->plate_no,
                    $res->driver,
                    $res->po_num,
                    $res->sales_invoice,
                    $res->supplier_name,
                    $res->category_name,
                    $res->product_code,
                    $res->product_name,
                    $res->lot_no,
                    'N/A',
                    'N/A',
                    $res->inv_qty,
                    $res->unit,
                    $res->manufacture_date,
                    $res->expiry_date,
                    $res->dispatch_no,
                    strtoupper($res->item_type),
                    $res->remark,
                    "N/A",
                    "-",
                    date('H:i A', strtotime($res->date_arrived)),
                    '-',
                    '-',
                    date('H:i A', strtotime($res->date_departed)),
                    '-',
                    timeInterval($res->date_arrived, $res->date_departed),
                    "N/A",
                    $res->name,
                    $res->received_by
                );
            }

        }
		$file_name = 'export-inbound-monitoring'.date('Ymd-His').'.xls';
        return Excel::download(new ExportInboundMonitoring($data), $file_name);
    }
}
