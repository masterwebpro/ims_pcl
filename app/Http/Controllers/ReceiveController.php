<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\RcvHdr;
use App\Models\RcvDtl;
use App\Models\PoHdr;
use App\Models\SeriesModel;
use App\Models\Client;
use App\Models\Store;
use App\Models\MasterfileModel;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\UOM;
use App\Models\TruckType;
use App\Models\AuditTrail;

use DataTables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ReceiveController extends Controller
{
    public function index(Request $request)
    {
        $receive_list = RcvHdr::select('rcv_hdr.*', 'sp.supplier_name', 's.store_name','c.client_name', 'u.name')
        ->leftJoin('suppliers as sp', 'sp.id', '=', 'rcv_hdr.supplier_id')
        ->leftJoin('store_list as s', 's.id', '=', 'rcv_hdr.store_id')
        ->leftJoin('client_list as c', 'c.id', '=', 'rcv_hdr.client_id')
        ->leftJoin('users as u', 'u.id', '=', 'rcv_hdr.created_by')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->leftJoin('suppliers as sp', 'sp.id', '=', 'rcv_hdr.supplier_id');
                    $query->leftJoin('store_list as s', 's.id', '=', 'rcv_hdr.store_id');
                    $query->leftJoin('client_list as c', 'c.id', '=', 'rcv_hdr.client_id');
                    // $query->orWhere('rcv_hdr.po_num','like', '%'.$s.'%');
                    $query->orWhere('sp.supplier_name', 'like', '%' . $s . '%');
                    $query->orWhere('s.store_name', 'LIKE', '%' . $s . '%');
                    $query->orWhere('c.client_name', 'LIKE', '%' . $s . '%');
                    $query->orWhere('rcv_hdr.rcv_no', 'LIKE', '%' . $s . '%');
                    $query->get();
                }
            }]
        ])->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('rcv_hdr.status', $s);
                }

                // if ($request->filter_date && $request->po_date) {
                //     if($request->filter_date == 'po_date') {
                //         $query->whereBetween('po_hdr.po_date', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                //     }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('rcv_hdr.created_at', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                    }
                    
                // }

                $query->get();
            }]
        ])
        ->paginate(20);

        return view('receive/index', ['receive_list'=>$receive_list]);
    }

    public function create()
    {
        $truck_type_list = TruckType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::all();
        $warehouse_list = Warehouse::all();

        $uom = UOM::all();

        return view('receive/create', [
            'client_list'=>$client_list, 
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'truck_type_list'=>$truck_type_list,
            'warehouse_list'=>$warehouse_list,
            'uom'=>$uom
        ]);
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'supplier'=>'required',
            'client'=>'required',
            'store'=>'required',
            'po_num'=>'required',
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
        ], [
            'supplier'=>'Supplier is required',
            'client'=>'Client  is required',
            'store'=>'Store is required',
            'po_num'=>'Po Number is required',
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
            'whse_qty.*' => 'Whse Qty  is required',
            'whse_uom.*' => 'Whse UOM  is required',
            'inv_qty.*' => 'Inv Qty  is required',
            'inv_uom.*' => 'Inv UOM  is required'
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
           
            $date_arrived = date("Y-m-d", strtotime($request->date_arrived))." ".date("H:i:s", strtotime($request->time_arrived));
            $date_departed = date("Y-m-d", strtotime($request->date_departed))." ".date("H:i:s", strtotime($request->time_departed));
                
            $rcv = RcvHdr::updateOrCreate(['rcv_no' => $rcv_no], [
                'po_num'=>$request->po_num,
                'store_id'=>$request->store,
                'client_id'=>$request->client,
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
            for($x=0; $x < count($request->product_id); $x++ ) {
                $dtl[] = array(
                    'rcv_no'=>$rcv_no,
                    'product_id'=>$request->product_id[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>$request->inv_qty[$x],
                    'inv_uom'=>$request->inv_uom[$x],
                    'whse_qty'=>$request->whse_qty[$x],
                    'whse_uom'=>$request->whse_uom[$x],
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime,
                );

                //add on the masterfile
                $masterfile[] = array(
                    'ref_no'=>$rcv_no,
                    'product_id'=>$request->product_id[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>$request->inv_qty[$x],
                    'inv_uom'=>$request->inv_uom[$x],
                    'whse_qty'=>$request->whse_qty[$x],
                    'whse_uom'=>$request->whse_uom[$x],
                    'store_id'=>$request->store,
                    'client_id'=>$request->client,
                    'warehouse_id'=>$request->warehouse,
                    'storage_location_id'=>null,
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime,
                );
            }

            $result= RcvDtl::where('rcv_no',$rcv_no)->delete();
            RcvDtl::insert($dtl);

            //update PO to closed
            PoHdr::where('po_num', '=', $request->po_num)->update(['status'=>'closed']);

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
        $client_list = Client::all();
        $warehouse_list = Warehouse::all();

        return view('receive/view', [
            'rcv'=>$rcv, 
            'client_list'=>$client_list, 
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'truck_type_list'=>$truck_type_list,
            'warehouse_list'=>$warehouse_list,
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
        $client_list = Client::all();
        $warehouse_list = Warehouse::all();

        return view('receive/edit', [
            'rcv'=>$rcv, 
            'client_list'=>$client_list, 
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'truck_type_list'=>$truck_type_list,
            'warehouse_list'=>$warehouse_list,
            'uom_list'=>$uom_list]);
    }

    public function receivePo($po_num)
    {
        $po = PoHdr::where('po_num', html_entity_decode($po_num))->first();

        if($po) {
            $uom_list = UOM::all();
            $truck_type_list = TruckType::all();
            $store_list = Store::all();
            $supplier_list = Supplier::all();
            $client_list = Client::all();
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
}
