<?php

namespace App\Http\Controllers;

use App\Models\DoHdr;
use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Client;
use App\Models\DoDtl;
use App\Models\OrderType;
use App\Models\RcvHdr;
use App\Models\SeriesModel;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\TruckType;
use App\Models\UOM;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $do_type = array(
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
        $do_list = DoHdr::select('do_hdr.*', 'cl.client_name as deliver_to', 's.store_name','c.client_name', 'u.name')
        ->leftJoin('client_list as cl', 'cl.id', '=', 'do_hdr.deliver_to_id')
        ->leftJoin('store_list as s', 's.id', '=', 'do_hdr.store_id')
        ->leftJoin('client_list as c', 'c.id', '=', 'do_hdr.customer_id')
        ->leftJoin('users as u', 'u.id', '=', 'do_hdr.created_by')
        ->orderByDesc('do_hdr.created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('do_hdr.status', $s);
                }

                if ($request->q) {
                    $query->where('do_hdr.do_no', $request->q);
                    $query->orWhere('do_hdr.order_no', $request->q);
                    $query->orWhere('do_hdr.sales_invoice', $request->q);
                    $query->orWhere('do_hdr.po_num', $request->q);
                }

                if ($request->filter_date && $request->order_date) {
                    if($request->filter_date == 'order_date') {
                        $query->whereBetween('do_hdr.order_date', [$request->order_date." 00:00:00", $request->order_date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('do_hdr.created_at', [$request->order_date." 00:00:00", $request->order_date." 23:59:00"]);
                    }

                }

                if ($request->delivery_to) {
                    $query->where('do_hdr.delivery_to_id', $request->delivery_to);
                }

                if ($request->customer) {
                    $query->where('do_hdr.customer_id', $request->customer);
                }

                if ($request->company) {
                    $query->where('do_hdr.company_id', $request->company);
                }

                $query->get();
            }]
        ])
        ->paginate(20);
        $deliver_list = Client::where('client_type','T')->get();
        $client_list = Client::where('is_enabled', '1')->get();
        return view('do/index', ['do_list'=>$do_list, 'deliver_list'=> $deliver_list, 'client_list'=> $client_list, 'request'=> $request]);
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
        $company_list = Client::where('client_type','O')->get();
        $client_list = Client::where('client_type','C')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('do/create', [
            'client_list'=>$client_list,
            'company_list'=>$company_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'deliver_list'=>$deliver_list,
            'order_type'=>$order_type,
            'warehouse_list'=>$warehouse_list,
            'uom'=>$uom,
            'do_type' => $this->do_type,
            'created_by' => Auth::user()->name
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
            'company'=>'required',
            'customer'=>'required',
            'deliver_to'=>'required',
            'store'=>'required',
            'order_type'=>'required',
            'order_no'=>'required',
            'do_type'=>'required',
            'order_date'=>'required',
            'pickup_date'=>'required',
            'trgt_dlv_date'=>'required',
            'actual_dlv_date'=>'required',
            'inv_qty.*' => 'required',
            'inv_uom.*' => 'required'
        ], [
            'company'=>'Company  is required',
            'customer'=>'Customer  is required',
            'deliver_to'=>'Deliver to is required',
            'store'=>'Store is required',
            'order_type'=>'Order type is required',
            'order_no'=>'Order number is required',
            'do_type'=>'DO type is required',
            'order_date'=>'Order date is required',
            'pickup_date'=>'Pickup date is required',
            'trgt_dlv_date'=>'Target delivery date is required',
            'actual_dlv_date'=>'Actual delivery date is required',
            'inv_qty.*' => 'Qty  is required',
            'inv_uom.*' => 'UOM  is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::beginTransaction();

        try {
            $do_no = $request->do_no;
            if($do_no=='') {
                $do_no = $this->generateDoNo();

                $series[] = [
                    'series' => $do_no,
                    'trans_type' => 'DO',
                    'created_at' => $this->current_datetime,
                    'updated_at' => $this->current_datetime,
                    'user_id' => Auth::user()->id,
                ];
                SeriesModel::insert($series);
            }

            $do = DoHdr::updateOrCreate(['do_no' => $do_no], [
                'po_num'=>$request->po_num,
                'store_id'=>$request->store,
                'customer_id'=>$request->customer,
                'company_id'=>$request->company,
                'deliver_to_id'=>$request->deliver_to,
                'sales_invoice'=>$request->sales_invoice,
                'order_no'=>$request->order_no,
                'order_type'=>$request->order_type,
                'do_type' => $request->do_type,
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
                DoDtl::where('do_no',$do_no)->delete();
                $dtl = array();
                for($x=0; $x < count($request->product_id); $x++ ) {
                    $dtl[] = array(
                        'do_no'=>$do_no,
                        'product_id'=>$request->product_id[$x],
                        'inv_qty'=>$request->inv_qty[$x],
                        'inv_uom'=>$request->inv_uom[$x],
                        'unserve_qty'=>$request->inv_qty[$x],
                        'created_at'=>$this->current_datetime,
                        'updated_at'=>$this->current_datetime,
                    );
                }
                DoDtl::insert($dtl);
            }

            $audit_trail[] = [
                'control_no' => $do_no,
                'type' => 'DO',
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
                'data'    => $do,
                'id'=> _encode($do->id)
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
     * @param  \App\Models\DoHdr  $doHdr
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $do = DoHdr::select('do_hdr.*', 'u.name')
                ->leftJoin('users as u', 'u.id', '=', 'do_hdr.created_by')
                ->where('do_hdr.id', _decode($id))->first();
        $order_type = OrderType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::where('client_type','C')->get();
        $company_list = Client::where('client_type','O')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();
        return view('do/view', [
            'do' => $do,
            'client_list'=>$client_list,
            'company_list'=>$company_list,
            'deliver_list'=>$deliver_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'order_type'=>$order_type,
            'warehouse_list'=>$warehouse_list,
            'uom_list'=>$uom,
            'do_type' => $this->do_type,
            'created_by' => Auth::user()->name
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DoHdr  $doHdr
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $do = DoHdr::select('do_hdr.*', 'u.name')
                ->leftJoin('users as u', 'u.id', '=', 'do_hdr.created_by')
                ->where('do_hdr.id', _decode($id))->first();
        $order_type = OrderType::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $client_list = Client::where('client_type','C')->get();
        $company_list = Client::where('client_type','O')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('do/edit', [
            'do' => $do,
            'client_list'=>$client_list,
            'company_list'=>$company_list,
            'deliver_list' => $deliver_list,
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'order_type'=>$order_type,
            'warehouse_list'=>$warehouse_list,
            'uom_list'=>$uom,
            'do_type' => $this->do_type,
            'created_by' => Auth::user()->name
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DoHdr  $doHdr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DoHdr $doHdr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DoHdr  $doHdr
     * @return \Illuminate\Http\Response
     */
    public function destroy(DoHdr $doHdr)
    {
        //
    }

    public function generateDoNo()
    {
        $data = SeriesModel::where('trans_type', '=', 'DO')->where('created_at', '>=', date('Y-m-01 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'));
        $count = $data->count();
        $count = $count + 1;
        $date = date('ym');

        $num = str_pad((int)$count, 5, "0", STR_PAD_LEFT);

        $series = "D-" . $date . "-" . $num;

        return $series;
    }
}
