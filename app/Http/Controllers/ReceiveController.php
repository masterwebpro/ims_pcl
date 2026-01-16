<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\RcvHdr;
use App\Models\RcvDtl;
use App\Models\MvHdr;
use App\Models\MvDtl;
use App\Models\PoHdr;
use App\Models\PoDtl;
use App\Models\SeriesModel;
use App\Models\Client;
use App\Models\Store;
use App\Models\MasterfileModel;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\UOM;
use App\Models\TruckType;
use App\Models\AuditTrail;
use App\Models\ItemType;

use DataTables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ReceiveController extends Controller
{
    public function index(Request $request)
    {
        $receive_list = RcvHdr::select('rcv_hdr.*', 'sp.supplier_name', 's.store_name','cx.client_name as customer_name', 'cm.client_name as company_name', 'u.name')
        ->leftJoin('suppliers as sp', 'sp.id', '=', 'rcv_hdr.supplier_id')
        ->leftJoin('store_list as s', 's.id', '=', 'rcv_hdr.store_id')
        ->leftJoin('client_list as cx', 'cx.id', '=', 'rcv_hdr.customer_id')
        ->leftJoin('client_list as cm', 'cm.id', '=', 'rcv_hdr.company_id')
        ->leftJoin('users as u', 'u.id', '=', 'rcv_hdr.created_by')
        ->orderByDesc('rcv_hdr.created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('rcv_hdr.status', $s);
                }

                if ($request->q) {
                    $query->where('rcv_hdr.po_num', $request->q);
                    $query->orWhere('rcv_hdr.rcv_no', $request->q);
                }

                if ($request->filter_date && $request->po_date) {
                    if($request->filter_date == 'po_date') {
                        $query->whereBetween('rcv_hdr.po_date', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('rcv_hdr.created_at', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                    }
                }
                if ($request->supplier) {
                    $query->where('rcv_hdr.supplier_id', $request->supplier);
                }

                if ($request->customer) {
                    $query->where('rcv_hdr.customer_id', $request->customer);
                }

                if ($request->company) {
                    $query->where('rcv_hdr.company_id', $request->company);
                }

                $query->get();
            }]
        ])->paginate(20);
        $supplier_list = Supplier::all();
        $client_list = Client::where('is_enabled', '1')->get();

        return view('receive/index', ['receive_list'=>$receive_list, 'supplier_list'=>$supplier_list, 'client_list'=>$client_list,  'request'=>$request]);
    }

    public function create()
    {
        $truck_type_list = TruckType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::where('is_enabled', '1')->get();
        $warehouse_list = Warehouse::all();

        $uom = UOM::all();
        $item_type = ItemType::all();

        return view('receive/create', [
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'truck_type_list'=>$truck_type_list,
            'warehouse_list'=>$warehouse_list,
            'uom'=>$uom,
            'item_type' => $item_type
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'supplier'=>'required',
            'customer'=>'required',
            'company'=>'required',
            'store'=>'required',
            'sales_invoice'=>'required',
            'received_by'=>'required',
            'date_received'=>'required',
            'inspect_by'=>'required',
            'inspect_date'=>'required',
            'date_arrived'=>'required',
            'time_arrived'=>'required',
            'date_departed'=>'required',
            'time_departed'=>'required',
            'truck_type'=>'required',
            'plate_no'=>'required',
            'warehouse'=>'required',
            'whse_qty.*' => 'required',
            'whse_uom.*' => 'required',
            'inv_qty.*' => 'required',
            'inv_uom.*' => 'required',
            'item_type.*' => 'required',
        ], [
            'supplier'=>'Supplier is required',
            'customer'=>'Customer is required',
            'company'=>'Company is required',
            'store'=>'Site is required',
            'sales_invoice'=>'Sales invoice is required',
            'received_by'=>'Received by is required',
            'date_received'=>'Date received is required',
            'inspect_by'=>'Inspect by is required',
            'plate_no'=>'Plate no is required',
            'inspect_date'=>'Inspect date is required',
            'date_arrived'=>'Date arrived  is required',
            'time_arrived'=>'Time arrived  is required',
            'date_departed'=>'Date Departed  is required',
            'time_departed'=>'Time Departed  is required',
            'truck_type'=>'Truck Type  is required',
            'warehouse'=>'Warehose  is required',
            'whse_qty.*' => 'Qty  is required',
            'whse_uom.*' => 'UOM  is required',
            'inv_qty.*' => 'Qty  is required',
            'inv_uom.*' => 'UOM  is required',
            'item_type.*' => 'This is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::connection()->beginTransaction();

        try
        {
            $rcv_no = $request->rcv_no;

            if($rcv_no=='') {
                $rcv_no = $this->generateRcvNo();

                $series[] = [
                    'series' => $rcv_no,
                    'trans_type' => 'RCV',
                    'created_at' => $this->current_datetime,
                    'updated_at' => $this->current_datetime,
                    'user_id' => Auth::user()->id,
                ];

                SeriesModel::insert($series);
            }

            //check if from PO
            $_hasPo = _hasPo($request->po_num);

            $date_arrived = date("Y-m-d", strtotime($request->date_arrived))." ".date("H:i:s", strtotime($request->time_arrived));
            $date_departed = date("Y-m-d", strtotime($request->date_departed))." ".date("H:i:s", strtotime($request->time_departed));

            $rcv = RcvHdr::updateOrCreate(['rcv_no' => $rcv_no], [
                'po_num'=>$request->po_num,
                'store_id'=>$request->store,
                'company_id'=>$request->company,
                'customer_id'=>$request->customer,
                'supplier_id'=>$request->supplier,
                'sales_invoice'=>$request->sales_invoice,
                'received_by'=>$request->received_by,
                'po_date'=>date("Y-m-d", strtotime($request->po_date)),
                'date_received'=>date("Y-m-d", strtotime($request->date_received)),
                'inspect_by'=>$request->inspect_by,
                'inspect_date'=>date("Y-m-d H:i:s", strtotime($request->inspect_date)),
                'date_arrived'=>date("Y-m-d H:i:s", strtotime($date_arrived)),
                'date_departed'=>date("Y-m-d H:i:s", strtotime($date_departed)),
                'plate_no'=>$request->plate_no,
                'truck_type'=>$request->truck_type,
                'warehouse_id'=>$request->warehouse,
                'status'=>$request->status,
                'remarks'=>$request->remarks,
                'created_by' =>Auth::user()->id,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

            //save on dtl
            $dtl = array();
            $masterfile = [];
            $masterdata = [];

            $result= RcvDtl::where('rcv_no',$rcv_no)->delete();

            $has_error = [];

            for($x=0; $x < count($request->product_id); $x++ ) {

                if($_hasPo) {
                    if($request->inv_qty[$x] > $request->available_qty[$x]) {
                        return response()->json([
                            'success'  => false,
                            'message' => 'Insufficient QTY for Product Code : ' .$request->product_code[$x],
                        ]);
                        exit;
                    }
                }

                $item = array(
                    'rcv_no'=>$rcv_no,
                    'product_id'=>$request->product_id[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>clean($request->inv_qty[$x]),
                    'inv_uom'=>$request->inv_uom[$x],
                    'whse_qty'=>clean($request->whse_qty[$x]),
                    'whse_uom'=>$request->whse_uom[$x],
                    'manufacture_date'=>$request->manufacture_date[$x],
                    'lot_no'=>$request->lot_no[$x],
                    'po_dtl_id'=>isset($request->po_dtl_id[$x]) ? $request->po_dtl_id[$x] : 0,
                    'expiry_date'=>$request->expiry_date[$x],
                    'remarks'=>$request->item_remarks[$x],
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime,
                );

                $rcv_dtl = RcvDtl::create($item);

                //add on the masterfile
                $masterfile[] = array(
                    'ref_no'=>$rcv_no,
                    'status'=>'X',
                    'trans_type'=>'RV',
                    'date_received'=>date("Y-m-d H:i:s", strtotime($request->date_received)),
                    'product_id'=>$request->product_id[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>clean($request->inv_qty[$x]),
                    'inv_uom'=>$request->inv_uom[$x],
                    'whse_qty'=>clean($request->whse_qty[$x]),
                    'whse_uom'=>$request->whse_uom[$x],
                    'store_id'=>$request->store,
                    'customer_id'=>$request->customer,
                    'company_id'=>$request->company,
                    'warehouse_id'=>$request->warehouse,
                    'storage_location_id'=>null,
                    'ref1_no'=>$rcv_no,
                    'ref1_type'=>'rcv',
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime,
                );

                $masterdata[] = array(
                    'customer_id'=>$request->customer,
                    'company_id'=>$request->company,
                    'store_id'=>$request->store,
                    'warehouse_id'=>$request->warehouse,
                    'product_id'=>$request->product_id[$x],
                    'storage_location_id'=>null,
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>clean($request->inv_qty[$x]),
                    'inv_uom'=>$request->inv_uom[$x],
                    'whse_qty'=>clean($request->whse_qty[$x]),
                    'whse_uom'=>$request->whse_uom[$x],
                    // 'expiry_date'=>$request->expiry_date[$x],
                    // 'manufacture_date'=>$request->manufacture_date[$x],
                    // 'lot_no'=>$request->lot_no[$x],
                    'rcv_dtl_id'=>$rcv_dtl->id,
                    // 'received_date'=>date("Y-m-d H:i:s", strtotime($request->date_received)),
                );
            }

            $audit_trail[] = [
                'control_no' => $rcv_no,
                'type' => 'RCV',
                'status' => $request->status,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
                'user_id' => Auth::user()->id,
                'data' => null
            ];


            if($request->status == 'posted') {
                //add on the masterfile
                MasterfileModel::insert($masterfile);

                _stockInMasterData($masterdata);

                //update available qty
                if($_hasPo) {
                    for($i=0; $i < count($request->product_id); $i++ ) {
                        PoDtl::where('po_num', '=', $request->po_num)
                            ->where('product_id', '=', $request->product_id[$i])
                            ->update(['available_qty'=> DB::raw('available_qty - '.clean($request->inv_qty[$i]))] );
                    }

                    //check if po_dtl is complete
                    $qty= PoDtl::where('po_num','=',$request->po_num)->sum('available_qty');
                    if($qty == 0) {
                        //update PO to closed
                        PoHdr::where('po_num', '=', $request->po_num)->update(['status'=>'closed']);
                    }
                }

                $audit_trail[] = [
                    'control_no' => $rcv_no,
                    'type' => 'masterfile',
                    'status' => $request->status,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                    'user_id' => Auth::user()->id,
                    'data' => json_encode(array('comment' => 'Location: floor'))
                ];

            }

            AuditTrail::insert($audit_trail);

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $rcv,
                'id'=> _encode($rcv->id)
            ]);
        }
        catch(\Throwable $e)
        {
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $rcv = RcvHdr::select('rcv_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'rcv_hdr.created_by')
        ->where('rcv_hdr.id', _decode($id))->first();

        $uom_list = UOM::all();
        $truck_type_list = TruckType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::where('is_enabled', '1')->get();
        $warehouse_list = Warehouse::all();
        $item_type = ItemType::all();

        return view('receive/view', [
            'rcv'=>$rcv,
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'truck_type_list'=>$truck_type_list,
            'warehouse_list'=>$warehouse_list,
            'item_type' => $item_type,
            'uom_list'=>$uom_list]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rcv = RcvHdr::select('rcv_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'rcv_hdr.created_by')
        ->where('rcv_hdr.id', _decode($id))->first();

        $uom_list = UOM::all();
        $truck_type_list = TruckType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::where('is_enabled', '1')->get();
        $warehouse_list = Warehouse::all();
        $item_type = ItemType::all();
        return view('receive/edit', [
            'rcv'=>$rcv,
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'truck_type_list'=>$truck_type_list,
            'warehouse_list'=>$warehouse_list,
            'item_type' => $item_type,
            'uom_list'=>$uom_list]);
    }

    public function receivePo($po_id)
    {
        $po_id = _decode($po_id);

        $po = PoHdr::where('id', $po_id)->first();

        if($po) {
            $uom_list = UOM::all();
            $truck_type_list = TruckType::all();
            $store_list = Store::all();
            $supplier_list = Supplier::all();
            $client_list = Client::where('is_enabled', '1')->get();
            $warehouse_list = Warehouse::all();

            return view('receive/po', [
                'po'=>$po,
                'client_list'=>$client_list,
                'supplier_list'=>$supplier_list,
                'truck_type_list'=>$truck_type_list,
                'uom_list'=>$uom_list
            ]);
        } else {
            return view('error/no-found');
        }

    }

    public function generateRcvNo()
    {
        $data = SeriesModel::where('trans_type', '=', 'RCV')->where('created_at', '>=', date('Y-m-01 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'));
        $count = $data->count();
        $count = $count + 1;
        $date = date('ym');

        $num = str_pad((int)$count, 5, "0", STR_PAD_LEFT);

        $series = "R-" . $date . "-" . $num;

        return $series;
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::connection()->beginTransaction();

        try
        {
            $rcv_no = $request->rcv_no;
            if($rcv_no) {
                $rcv_hdr = RcvHdr::where('rcv_no', $rcv_no)->first();
                $rcv = RcvHdr::where('rcv_no', $rcv_no)->delete();
                $rcv_dtl = RcvDtl::where('rcv_no', $rcv_no)->delete();
                $po = PoHdr::where('po_num', $rcv_hdr->po_num)->update(['status'=>'posted']);


                $audit_trail[] = [
                    'control_no' => $rcv_no,
                    'type' => 'RCV',
                    'status' => 'deleted',
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                    'user_id' => Auth::user()->id,
                    'data' => 'deleted'
                ];

                AuditTrail::insert($audit_trail);

                DB::connection()->commit();

                return response()->json([
                    'success'  => true,
                    'message' => 'deleted successfully!',
                    'data'    => $rcv
                ]);

            } else {
                return response()->json([
                    'success'  => false,
                    'message' => 'Unable to process request. Please try again.',
                    'data'    => $e->getMessage()
                ]);
            }

        }
        catch(\Throwable $e)
        {
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        }
    }

    public function unpost(Request $request)
    {
        DB::connection()->beginTransaction();
        try
        {
            $rcv_no = $request->rcv_no;
            if($rcv_no) {

                //get Rcv Dtl
                $rcv_dtl = RcvDtl::select('rcv_hdr.store_id','rcv_hdr.company_id','rcv_hdr.customer_id', 'rcv_hdr.date_received', 'rcv_hdr.warehouse_id','rcv_dtl.*')
                    ->leftJoin('rcv_hdr', 'rcv_hdr.rcv_no', '=', 'rcv_dtl.rcv_no')
                    ->where('rcv_dtl.rcv_no', $rcv_no)->get();
                $masterdata = [];

                foreach($rcv_dtl as $dtl) {
                    $masterdata[] = array(
                        'customer_id'=>$dtl->customer_id,
                        'company_id'=>$dtl->company_id,
                        'store_id'=>$dtl->store_id,
                        'warehouse_id'=>$dtl->warehouse_id,
                        'product_id'=>$dtl->product_id,
                        'storage_location_id'=>null,
                        'item_type'=>$dtl->item_type,
                        'inv_qty'=>$dtl->inv_qty,
                        'inv_uom'=>$dtl->inv_uom,
                        'whse_qty'=>$dtl->whse_qty,
                        'whse_uom'=>$dtl->whse_uom,
                        'expiry_date'=>$dtl->expiry_date,
                        'lot_no'=>$dtl->lot_no,
                        'received_date'=>date("Y-m-d", strtotime($dtl->date_received)),
                    );
                }
                //check if has movement
                if(hasMovement($rcv_no, 'rcv')) {
                    return response()->json([
                        'success'  => false,
                        'message' => 'Unable to unpost the transaction! Please remove it in PUTAWAY first.',
                        'data'    => $rcv_no
                    ]);

                } else {

                    if(hasPendingMovement($rcv_no, 'rcv')) {
                        return response()->json([
                            'success'  => false,
                            'message' => 'Unable to unpost the transaction! Still have active PUTAWAY.',
                            'data'    => $rcv_no
                        ]);

                    } else {
                        $rcv = RcvHdr::where('rcv_no', $rcv_no)->update(['status'=>'open']);
                        //remove on MW HDR and DTL
                        $mster_hdr = MasterfileModel::where('ref_no', $rcv_no)->delete();
                        $mv_hdr = MvHdr::where('ref_no', $rcv_no)->delete();
                        $mv_dtl = MvDtl::where('ref_no', $rcv_no)->delete();

                        //deduct on masterdata
                        _stockOutMasterData($masterdata);

                        $audit_trail[] = [
                            'control_no' => $rcv_no,
                            'type' => 'RCV',
                            'status' => 'open',
                            'created_at' => date('y-m-d h:i:s'),
                            'updated_at' => date('y-m-d h:i:s'),
                            'user_id' => Auth::user()->id,
                            'data' => 'unpost'
                        ];

                        AuditTrail::insert($audit_trail);

                        DB::connection()->commit();
                        return response()->json([
                            'success'  => true,
                            'message' => 'Unpost successfully!',
                            'data'    => $rcv
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'success'  => false,
                    'message' => 'Unable to process request. Please try again.',
                    'data'    => $e->getMessage()
                ]);
            }

        }
        catch(\Throwable $e)
        {
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        }
    }

    public function updateRow(Request $request)
    {
        if(!$request->id) {
            return response()->json([
                'success'  => false,
                'message' => 'Row ID is required.',
                'data'    => null
            ]);
        }
        try {
            $rcv_dtl = RcvDtl::findOrFail($request->id);
            $rcv_dtl->update($request->only('manufacture_date', 'lot_no', 'expiry_date','remarks'));

            return response()->json([
                'success'  => true,
                'message' => 'Row updated successfully!',
                'data'    => $rcv_dtl
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success'  => false,
                'message' => 'Row not found.',
                'data'    => null
            ]);
        }

    }
}
