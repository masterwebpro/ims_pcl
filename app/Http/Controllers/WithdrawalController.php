<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Client;
use App\Models\OrderType;
use App\Models\SeriesModel;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\UOM;
use App\Models\Warehouse;
use App\Models\WdDtl;
use App\Models\WdHdr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $wd_type = array(
        [
            'code' => 'pickup',
            'name' => 'Pick-Up',
        ],
        [
            'code' => 'delivery',
            'name' => 'Delivery',
        ]
    );

    public function index(Request $request)
    {
        $wd_list = WdHdr::select('wd_hdr.*', 'sp.supplier_name', 's.store_name','c.client_name', 'u.name')
        ->leftJoin('suppliers as sp', 'sp.id', '=', 'wd_hdr.supplier_id')
        ->leftJoin('store_list as s', 's.id', '=', 'wd_hdr.store_id')
        ->leftJoin('client_list as c', 'c.id', '=', 'wd_hdr.client_id')
        ->leftJoin('users as u', 'u.id', '=', 'wd_hdr.created_by')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->leftJoin('suppliers as sp', 'sp.id', '=', 'wd_hdr.supplier_id');
                    $query->leftJoin('store_list as s', 's.id', '=', 'wd_hdr.store_id');
                    $query->leftJoin('client_list as c', 'c.id', '=', 'wd_hdr.client_id');
                    $query->orWhere('wd_hdr.order_no','like', '%'.$s.'%');
                    $query->orWhere('sp.supplier_name', 'like', '%' . $s . '%');
                    $query->orWhere('s.store_name', 'LIKE', '%' . $s . '%');
                    $query->orWhere('c.client_name', 'LIKE', '%' . $s . '%');
                    $query->orWhere('wd_hdr.wd_no', 'LIKE', '%' . $s . '%');
                    $query->get();
                }
            }]
        ])->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('wd_hdr.status', $s);
                }

                // if ($request->filter_date && $request->po_date) {
                //     if($request->filter_date == 'po_date') {
                //         $query->whereBetween('po_hdr.po_date', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                //     }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('wd_hdr.created_at', [$request->withdrawal_date." 00:00:00", $request->withdrawal_date." 23:59:00"]);
                    }

                // }

                $query->get();
            }]
        ])
        ->paginate(20);

        return view('withdraw/index', ['wd_list'=>$wd_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $order_type = OrderType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::all();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('withdraw/create', [
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'order_type'=>$order_type,
            'warehouse_list'=>$warehouse_list,
            'uom'=>$uom,
            'wd_type' => $this->wd_type,
            'created_by' => Auth::user()->name,
            'today' => date('m/d/Y')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier'=>'required',
            'client'=>'required',
            'store'=>'required',
            'order_type'=>'required',
            'order_no'=>'required',
            'wd_type'=>'required',
            'withdraw_date'=>'required',
            'order_date'=>'required',
            'pickup_date'=>'required',
            'trgt_dlv_date'=>'required',
            'actual_dlv_date'=>'required',
            'warehouse'=>'required',
            'whse_qty.*' => 'required',
            'whse_uom.*' => 'required',
            'inv_qty.*' => 'required',
            'inv_uom.*' => 'required',
            'item_type.*' => 'required',
        ], [
            'supplier'=>'Supplier is required',
            'client'=>'Client  is required',
            'store'=>'Store is required',
            'order_type'=>'Order type is required',
            'order_no'=>'Order number is required',
            'wd_type'=>'WD type is required',
            'order_date'=>'Order date is required',
            'withdraw_date'=>'Withdrawal date is required',
            'pickup_date'=>'Pickup date is required',
            'trgt_dlv_date'=>'Target delivery date is required',
            'actual_dlv_date'=>'Actual delivery date is required',
            'warehouse'=>'Warehouse  is required',
            'whse_qty.*' => 'Qty  is required',
            'whse_uom.*' => 'UOM  is required',
            'inv_qty.*' => 'Qty  is required',
            'inv_uom.*' => 'UOM  is required',
            'item_type.*' => 'This is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::beginTransaction();

        try {
            $wd_no = $request->wd_no;
            if($wd_no=='') {
                $wd_no = $this->generateWdNo();

                $series[] = [
                    'series' => $wd_no,
                    'trans_type' => 'WD',
                    'created_at' => $this->current_datetime,
                    'updated_at' => $this->current_datetime,
                    'user_id' => Auth::user()->id,
                ];
                SeriesModel::insert($series);
            }

            $wd = WdHdr::updateOrCreate(['wd_no' => $wd_no], [
                'po_num'=>$request->po_num,
                'store_id'=>$request->store,
                'client_id'=>$request->client,
                'supplier_id'=>$request->supplier,
                'sales_invoice'=>$request->sales_invoice,
                'ar_no'=>$request->ar_no,
                'withdraw_date'=> date("Y-m-d", strtotime($request->withdraw_date)),
                'order_no'=>$request->order_no,
                'order_type'=>$request->order_type,
                'wd_type' => $request->wd_type,
                'order_date' => date("Y-m-d", strtotime($request->order_date)),
                'pickup_date' => date("Y-m-d", strtotime($request->pickup_date)),
                'target_dlv_date' => date("Y-m-d", strtotime($request->trgt_dlv_date)),
                'actual_dlv_date' => date("Y-m-d", strtotime($request->actual_dlv_date)),
                'warehouse_id'=>$request->warehouse,
                'status'=>$request->status,
                'remarks'=>$request->remarks,
                'created_by' =>Auth::user()->id,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

            //save on dtl
            if(isset($request->product_id)){
                $dtl = array();
                for($x=0; $x < count($request->product_id); $x++ ) {
                    $dtl[] = array(
                        'wd_no'=>$wd_no,
                        'product_id'=>$request->product_id[$x],
                        'inv_qty'=>$request->inv_qty[$x],
                        'inv_uom'=>$request->inv_uom[$x],
                        'whse_qty'=>$request->whse_qty[$x],
                        'whse_uom'=>$request->whse_uom[$x],
                        'pick_qty'=>$request->pick_qty[$x],
                        'created_at'=>$this->current_datetime,
                        'updated_at'=>$this->current_datetime,
                    );
                }
                if(count($dtl) > 0){
                    WdDtl::where('wd_no',$wd_no)->delete();
                    WdDtl::insert($dtl);
                }
            }

            $audit_trail[] = [
                'control_no' => $wd_no,
                'type' => 'WD',
                'status' => $request->status,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
                'user_id' => Auth::user()->id,
                'data' => null
            ];

            AuditTrail::insert($audit_trail);

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $wd,
                'id'=> _encode($wd->id)
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $wd = WdHdr::select('wd_hdr.*', 'u.name')
                ->leftJoin('users as u', 'u.id', '=', 'wd_hdr.created_by')
                ->where('wd_hdr.id', _decode($id))->first();
        $order_type = OrderType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::all();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('withdraw/view', [
            'wd' => $wd,
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'order_type'=>$order_type,
            'warehouse_list'=>$warehouse_list,
            'uom_list'=>$uom,
            'wd_type' => $this->wd_type,
            'created_by' => Auth::user()->name
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $wd = WdHdr::select('wd_hdr.*', 'u.name')
                ->leftJoin('users as u', 'u.id', '=', 'wd_hdr.created_by')
                ->where('wd_hdr.id', _decode($id))->first();
        $order_type = OrderType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::all();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('withdraw/edit', [
            'wd' => $wd,
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'order_type'=>$order_type,
            'warehouse_list'=>$warehouse_list,
            'uom_list'=>$uom,
            'wd_type' => $this->wd_type,
            'created_by' => Auth::user()->name
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function generateWdNo()
    {
        $data = SeriesModel::where('trans_type', '=', 'WD')->where('created_at', '>=', date('Y-m-01 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'));
        $count = $data->count();
        $count = $count + 1;
        $date = date('ym');

        $num = str_pad((int)$count, 5, "0", STR_PAD_LEFT);

        $series = "W-" . $date . "-" . $num;

        return $series;
    }
}
