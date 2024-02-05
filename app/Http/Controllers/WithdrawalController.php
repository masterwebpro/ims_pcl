<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Client;
use App\Models\DoHdr;
use App\Models\MasterdataModel;
use App\Models\MasterfileModel;
use App\Models\OrderType;
use App\Models\SeriesModel;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\TruckType;
use App\Models\UOM;
use App\Models\Warehouse;
use App\Models\WdDtl;
use App\Models\WdDtlItemize;
use App\Models\WdHdr;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
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
        $dateRangeParts = explode(" to ", $request->date);
        $startDate = isset($dateRangeParts[0]) ? $dateRangeParts[0] : "";
        $endDate = isset($dateRangeParts[1]) ? $dateRangeParts[1] : "";

        $wd_list = WdHdr::select('wd_hdr.*', 'cl.client_name as deliver_to', 's.store_name','c.client_name as customer_name','com.client_name as company_name', 'u.name')
        ->leftJoin('client_list as cl', 'cl.id', '=', 'wd_hdr.deliver_to_id')
        ->leftJoin('store_list as s', 's.id', '=', 'wd_hdr.store_id')
        ->leftJoin('client_list as c', 'c.id', '=', 'wd_hdr.customer_id')
        ->leftJoin('client_list as com', 'com.id', '=', 'wd_hdr.company_id')
        ->leftJoin('users as u', 'u.id', '=', 'wd_hdr.created_by')
        ->orderByDesc('wd_hdr.created_at')
        ->where([
            [function ($query) use ($request, $startDate, $endDate) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('wd_hdr.status', $s);
                }

                if ($request->q) {
                    $query->where('wd_hdr.wd_no', $request->q);
                    $query->orWhere('wd_hdr.dr_no', $request->q);
                    $query->orWhere('wd_hdr.order_no', $request->q);
                    $query->orWhere('wd_hdr.sales_invoice', $request->q);
                    $query->orWhere('wd_hdr.po_num', $request->q);
                    $query->orWhere('wd_hdr.dispatch_no', $request->q);
                }

                if ($request->filter_date) {
                    if($request->filter_date == 'withdraw_date') {
                        $query->whereBetween('wd_hdr.withdraw_date', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('wd_hdr.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                    }

                    if($request->filter_date == 'withdraw_date' && $startDate && $endDate)
                    {
                        $query->whereBetween('wd_hdr.withdraw_date',[$startDate,$endDate]);
                    }

                    if($request->filter_date == 'created_at' && $startDate && $endDate)
                    {
                        $query->whereBetween('wd_hdr.created_at',[$startDate,$endDate]);
                    }
                }
                if ($request->delivery_to) {
                    $query->where('wd_hdr.delivery_to_id', $request->delivery_to);
                }

                if ($request->customer) {
                    $query->where('wd_hdr.customer_id', $request->customer);
                }

                if ($request->company) {
                    $query->where('wd_hdr.company_id', $request->company);
                }
                $query->get();
            }]
        ])
        ->paginate(20);
        // echo "<pre>";
        // print_r($wd_list);
        // echo "</pre>";
        // die();
        $deliver_list = Client::where('client_type','T')->get();
        $client_list = Client::where('is_enabled', '1')->get();
        return view('withdraw/index', ['wd_list'=>$wd_list, 'deliver_list'=> $deliver_list, 'client_list'=> $client_list, 'request'=> $request]);
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
        $company_list = Client::where('client_type','O')->get();
        $client_list = Client::where('client_type','C')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('withdraw/create', [
            'company_list'=>$company_list,
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'deliver_list'=>$deliver_list,
            'order_type'=>$order_type,
            'warehouse_list'=>$warehouse_list,
            'uom'=>$uom,
            'wd_type' => $this->wd_type,
            'created_by' => Auth::user()->name,
            'today' => date('m/d/Y'),
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
            'client'=>'required',
            'deliver_to'=>'required',
            'store'=>'required',
            'order_type'=>'required',
            'order_no'=>'required',
            'wd_type'=>'required',
            'withdraw_date'=>'required',
            'order_date'=>'required',
            'pickup_date'=>'required',
            'trgt_dlv_date'=>'required',
            // 'actual_dlv_date'=>'required',
            // 'warehouse'=>'required',
            // 'whse_qty.*' => 'required',
            // 'whse_uom.*' => 'required',
            'inv_qty.*' => 'required|gt:0',
            // 'inv_uom.*' => 'required',
            // 'item_type.*' => 'required',
        ], [
            'company'=>'Company is required',
            'client'=>'Customer is required',
            'deliver_to'=>'Deliver to is required',
            'store'=>'Store is required',
            'order_type'=>'Order type is required',
            'order_no'=>'Order number is required',
            'wd_type'=>'WD type is required',
            'order_date'=>'Order date is required',
            'withdraw_date'=>'Withdrawal date is required',
            'pickup_date'=>'Pickup date is required',
            'trgt_dlv_date'=>'Target delivery date is required',
            // 'actual_dlv_date'=>'Actual delivery date is required',
            // 'warehouse'=>'Warehouse  is required',
            // 'whse_qty.*' => 'Qty  is required',
            // 'whse_uom.*' => 'UOM  is required',
            'inv_qty.*' => 'Withdraw Qty is required',
            // 'inv_uom.*' => 'UOM  is required',
            // 'item_type.*' => 'This is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        DB::beginTransaction();

        try {
            $wd_no = $request->wd_no;
            if($wd_no=='') {
                $wd_no = $this->generateWdNo("WD","W");

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
                'company_id'=>$request->company,
                'customer_id'=>$request->client,
                'deliver_to_id'=>$request->deliver_to,
                'sales_invoice'=>$request->sales_invoice,
                'dr_no'=>$request->dr_no,
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
            $serialist = json_decode($request->serial_list);
            if(isset($request->product_id)){
                $wd_dtl_ids = WdDtl::where('wd_no',$wd_no)->pluck('id');
                WdDtl::where('wd_no',$wd_no)->delete();
                WdDtlItemize::whereIn('wd_dtl_id',$wd_dtl_ids)->delete();

                if($request->status == 'posted'){
                    $twd_no = $this->generateWdNo("TWD","TWD");
                    $series[] = [
                        'series' => $twd_no,
                        'trans_type' => 'TWD',
                        'created_at' => $this->current_datetime,
                        'updated_at' => $this->current_datetime,
                        'user_id' => Auth::user()->id,
                    ];
                    SeriesModel::insert($series);
                }

                for($x=0; $x < count($request->product_id); $x++ ) {
                    $serial_data = array();
                    if($request->is_serialize[$x] == 1)
                    {
                        $targetMasterfileId = $request->master_id[$x];
                        $targetProductId = $request->product_id[$x];

                        foreach ($serialist as $innerArray) {
                            $filteredItems = array_filter($innerArray, function ($item) use ($targetMasterfileId, $targetProductId) {
                                return $item->master_id == $targetMasterfileId && $item->product_id == $targetProductId;
                            });

                            if (!empty($filteredItems)) {
                                $serial_data = $filteredItems;
                            }
                        }
                    }
                    $dtl = array(
                        'wd_no'=>$wd_no,
                        'product_id'=>$request->product_id[$x],
                        'master_id'=>$request->master_id[$x],
                        'rcv_dtl_id'=>$request->rcv_dtl_id[$x],
                        'inv_qty'=> ($request->is_serialize[$x] == 1) ? (count($serial_data) > 0 ) ? count($serial_data) : $request->inv_qty[$x] : $request->inv_qty[$x],
                        'inv_uom'=>$request->inv_uom[$x],
                        'created_at'=>$this->current_datetime,
                        'updated_at'=>$this->current_datetime,
                    );
                    $wddtl = WdDtl::create($dtl);


                    if($request->is_serialize[$x] == 1)
                    {
                        if(!empty($serial_data))
                        {
                            $serial = array();
                            foreach($serial_data as $sr){
                                $serial[] = array(
                                    'wd_dtl_id' => $wddtl->id,
                                    'serial_no' => $sr->serial_no,
                                    'warranty_no' => $sr->warranty_no,
                                );
                            }
                            WdDtlItemize::insert($serial);
                        }
                        else{
                            if($request->status == 'posted'){
                                DB::rollBack();
                                return response()->json([
                                    'success'  => false,
                                    'message' => 'Line no '.($x + 1).' serial is required',
                                    'data' => "Error on posting data"
                                ]);
                            }
                        }
                    }

                    if($request->status == 'posted'){
                        $masterData = MasterdataModel::find($request->master_id[$x]);
                        $inv_qty = ($request->is_serialize[$x] == 1) ? (count($serial_data) > 0 ) ? count($serial_data) : $request->inv_qty[$x] : $request->inv_qty[$x];
                        $reserve = $masterData->reserve_qty + $inv_qty;
                        if($masterData->inv_qty >= $reserve){
                            $masterData->update(['reserve_qty' => $reserve]);
                            MasterfileModel::create([
                                'ref_no'=> $twd_no,
                                'status' => 'X',
                                'trans_type' => 'WD',
                                'date_received' => isset($masterData->received_date) ? $masterData->received_date : "",
                                'item_type' => $masterData->item_type,
                                'product_id'=>$request->product_id[$x],
                                'storage_location_id'=> $masterData->storage_location_id,
                                'inv_qty'=> -$inv_qty,
                                'inv_uom'=> $masterData->inv_uom,
                                'whse_qty'=> -$inv_qty,
                                'whse_uom'=> $masterData->whse_uom,
                                'warehouse_id' => $masterData->warehouse_id,
                                'customer_id' => $masterData->customer_id,
                                'company_id' => $masterData->company_id,
                                'store_id' => $masterData->store_id,
                                'ref1_no' => $wd_no,
                                'ref1_type' => 'WD'
                            ]);
                            MasterfileModel::create([
                                'ref_no'=> $twd_no,
                                'status' => 'R',
                                'trans_type' => 'WD',
                                'item_type' => $masterData->item_type,
                                'date_received' => isset($masterData->received_date) ? $masterData->received_date : "",
                                'product_id'=>$request->product_id[$x],
                                'storage_location_id'=>$masterData->storage_location_id,
                                'inv_qty'=> $inv_qty,
                                'inv_uom'=> $masterData->inv_uom,
                                'whse_qty'=> $inv_qty,
                                'whse_uom'=> $masterData->whse_uom,
                                'warehouse_id' => $masterData->warehouse_id,
                                'customer_id' => $masterData->customer_id,
                                'company_id' => $masterData->company_id,
                                'store_id' => $masterData->store_id,
                                'ref1_no' => $wd_no,
                                'ref1_type' => 'WD'
                            ]);
                        }
                        else{
                            DB::rollBack();
                            return response()->json([
                                'success'  => false,
                                'message' => 'Line no '.($x + 1).' reserve quantity is higher than available stocks.',
                                'data' => "Error on posting data"
                            ]);
                        }
                    }
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
        // dd($wd);
        $order_type = OrderType::all();
        $store_list = Store::all();
        $company_list = Client::where('client_type','O')->get();
        $client_list = Client::where('client_type','C')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('withdraw/view', [
            'wd' => $wd,
            'company_list'=>$company_list,
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'deliver_list'=>$deliver_list,
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
        $company_list = Client::where('client_type','O')->get();
        $client_list = Client::where('client_type','C')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('withdraw/edit', [
            'wd' => $wd,
            'company_list'=>$company_list,
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'deliver_list'=>$deliver_list,
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
    public function destroy(Request $request)
    {
        DB::connection()->beginTransaction();

        try
        {
            $wd_no = $request->wd_no;
            if($wd_no) {
                WdHdr::where('wd_no', $wd_no)->delete();
                WdDtl::where('wd_no', $wd_no)->delete();

                $audit_trail[] = [
                    'control_no' => $wd_no,
                    'type' => 'WD',
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
                    'data'    => $wd_no
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

    public function generateWdNo($type,$prefix)
    {
        $data = SeriesModel::where('trans_type', '=', $type)->where('created_at', '>=', date('Y-m-01 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'));
        $count = $data->count();
        $count = $count + 1;
        $date = date('ym');

        $num = str_pad((int)$count, 5, "0", STR_PAD_LEFT);

        $series = "$prefix-" . $date . "-" . $num;

        return $series;
    }

    public function picklist($id)
    {
        ob_start();
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        $wd = WdHdr::select('wd_hdr.*', 'cl.client_name', 'del.client_name as deliver_to', 'u.name')
                ->selectRaw('CONCAT(del.address_1, " ", del.city, " " , del.province, " ", del.country, " ", del.zipcode) as address')
                ->leftJoin('client_list as cl', 'cl.id', '=', 'wd_hdr.customer_id')
                ->leftJoin('client_list as del', 'del.id', '=', 'wd_hdr.deliver_to_id')
                ->leftJoin('users as u', 'u.id', '=', 'wd_hdr.created_by')
                ->where('wd_hdr.id', _decode($id))->first();
        $pdf = PDF::loadView('withdraw.picklist', [
            'wd' => $wd,
            'created_by' => Auth::user()->name
        ]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download($wd->order_no.'.pdf');
    }

    public function withdrawalslip($id)
    {
        ob_start();
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        $wd = WdHdr::select('wd_hdr.*', 'cl.client_name', 'del.client_name as deliver_to', 'u.name')
                ->selectRaw('CONCAT(del.address_1, " ", del.city, " " , del.province, " ", del.country, " ", del.zipcode) as address')
                ->leftJoin('client_list as cl', 'cl.id', '=', 'wd_hdr.customer_id')
                ->leftJoin('client_list as del', 'del.id', '=', 'wd_hdr.deliver_to_id')
                ->leftJoin('users as u', 'u.id', '=', 'wd_hdr.created_by')
                ->where('wd_hdr.id', _decode($id))->first();
        $pdf = PDF::loadView('withdraw.withdrawalslip', [
            'wd' => $wd,
            'created_by' => Auth::user()->name
        ]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download($wd->wd_no.'.pdf');
    }

    public function unpost(Request $request)
    {
        DB::connection()->beginTransaction();
        try
        {
            $wd_no = $request->wd_no;
            if($wd_no) {
                if(hasDispatch($wd_no)) {
                    return response()->json([
                        'success'  => false,
                        'message' => 'Unable to unpost the transaction! Please remove it in Dispatch first.',
                        'data'    => $wd_no
                    ]);

                } else {
                        $wd = WdHdr::where('wd_no', $wd_no)->update(['status'=>'open']);
                        $mster_hdr = MasterfileModel::where('ref1_no', $wd_no)->delete();
                        $wd_dtl = WdDtl::select('master_id',DB::raw('sum(inv_qty) as inv_qty'))->where('wd_no', $wd_no)->groupBy('master_id')->get();
                        foreach($wd_dtl as $dtl){
                            $msterdata = MasterdataModel::find($dtl->master_id);
                            $msterdata->update(['reserve_qty' => $msterdata->reserve_qty - $dtl->inv_qty]);
                        }

                        $audit_trail[] = [
                            'control_no' => $wd_no,
                            'type' => 'WD',
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
                            'data'    => $wd
                        ]);
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

    public function withdrawDo($do_id)
    {
        $do_id = _decode($do_id);

        $do = DoHdr::where('id', $do_id)->first();

        if($do) {
            $uom_list = UOM::all();
            $truck_type_list = TruckType::all();
            $store_list = Store::all();
            $supplier_list = Supplier::all();
            $client_list = Client::where('is_enabled', '1')->get();
            $warehouse_list = Warehouse::all();
            $order_type = OrderType::all();
            $store_list = Store::all();
            $company_list = Client::where('client_type','O')->get();
            $client_list = Client::where('client_type','C')->get();
            $deliver_list = Client::where('client_type','T')->get();
            $warehouse_list = Warehouse::all();
            $uom = UOM::all();

            return view('withdraw/create', [
                'company_list'=>$company_list,
                'client_list'=>$client_list,
                'store_list'=>$store_list,
                'deliver_list'=>$deliver_list,
                'order_type'=>$order_type,
                'warehouse_list'=>$warehouse_list,
                'uom'=>$uom,
                'wd_type' => $this->wd_type,
                'created_by' => Auth::user()->name,
                'today' => date('m/d/Y'),
            ]);
            return view('receive/po', [
                'do'=>$do,
                'client_list'=>$client_list,
                'supplier_list'=>$supplier_list,
                'truck_type_list'=>$truck_type_list,
                'uom_list'=>$uom_list
            ]);
        } else {
            return view('error/no-found');
        }

    }
}
