<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Client;
use App\Models\MasterfileModel;
use App\Models\OrderType;
use App\Models\SeriesModel;
use App\Models\Store;
use App\Models\Supplier;
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
        $wd_list = WdHdr::select('wd_hdr.*', 'cl.client_name as deliver_to', 's.store_name','c.client_name', 'u.name')
        ->leftJoin('client_list as cl', 'cl.id', '=', 'wd_hdr.deliver_to_id')
        ->leftJoin('store_list as s', 's.id', '=', 'wd_hdr.store_id')
        ->leftJoin('client_list as c', 'c.id', '=', 'wd_hdr.client_id')
        ->leftJoin('users as u', 'u.id', '=', 'wd_hdr.created_by')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->leftJoin('client_list as cl', 'cl.id', '=', 'wd_hdr.deliver_to_id');
                    $query->leftJoin('store_list as s', 's.id', '=', 'wd_hdr.store_id');
                    $query->leftJoin('client_list as c', 'c.id', '=', 'wd_hdr.client_id');
                    $query->orWhere('wd_hdr.order_no','like', '%'.$s.'%');
                    $query->orWhere('cl.client_name', 'like', '%' . $s . '%');
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
        $client_list = Client::where('client_type','C')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();
        // $location = [
        //     "rack" => "",
        //     "layer"=> ""
        // ];

        // $location = (new SettingsController)->getLocationPerWarehouse($warehouse);


        return view('withdraw/create', [
            'client_list'=>$client_list,
            'store_list'=>$store_list,
            'deliver_list'=>$deliver_list,
            'order_type'=>$order_type,
            'warehouse_list'=>$warehouse_list,
            'uom'=>$uom,
            'wd_type' => $this->wd_type,
            'created_by' => Auth::user()->name,
            'today' => date('m/d/Y'),
            // 'location' => $location,
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
            'actual_dlv_date'=>'required',
            // 'warehouse'=>'required',
            // 'whse_qty.*' => 'required',
            // 'whse_uom.*' => 'required',
            'inv_qty.*' => 'required|gt:0',
            // 'inv_uom.*' => 'required',
            // 'item_type.*' => 'required',
        ], [
            'client'=>'Client  is required',
            'deliver_to'=>'Deliver to is required',
            'store'=>'Store is required',
            'order_type'=>'Order type is required',
            'order_no'=>'Order number is required',
            'wd_type'=>'WD type is required',
            'order_date'=>'Order date is required',
            'withdraw_date'=>'Withdrawal date is required',
            'pickup_date'=>'Pickup date is required',
            'trgt_dlv_date'=>'Target delivery date is required',
            'actual_dlv_date'=>'Actual delivery date is required',
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
                'client_id'=>$request->client,
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
                for($x=0; $x < count($request->product_id); $x++ ) {
                    $serial_data = array();
                    if($request->is_serialize[$x] == 1)
                    {
                        $targetMasterfileId = $request->masterfile_id[$x];
                        $targetProductId = $request->product_id[$x];

                        foreach ($serialist as $innerArray) {
                            $filteredItems = array_filter($innerArray, function ($item) use ($targetMasterfileId, $targetProductId) {
                                return $item->masterfile_id == $targetMasterfileId && $item->product_id == $targetProductId;
                            });

                            if (!empty($filteredItems)) {
                                $serial_data = $filteredItems;
                            }
                        }
                    }
                    $dtl = array(
                        'wd_no'=>$wd_no,
                        'product_id'=>$request->product_id[$x],
                        'masterfile_id'=>$request->masterfile_id[$x],
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
                        $masterData = MasterfileModel::find($request->masterfile_id[$x]);
                        $inv_qty = ($request->is_serialize[$x] == 1) ? (count($serial_data) > 0 ) ? count($serial_data) : $request->inv_qty[$x] : $request->inv_qty[$x];
                        $twd_no = $this->generateWdNo("TWD","TWD");
                        $series[] = [
                            'series' => $twd_no,
                            'trans_type' => 'TWD',
                            'created_at' => $this->current_datetime,
                            'updated_at' => $this->current_datetime,
                            'user_id' => Auth::user()->id,
                        ];
                        SeriesModel::insert($series);
                        MasterfileModel::create([
                            'ref_no'=> $twd_no,
                            'status' => 'X',
                            'trans_type' => 'WD',
                            'item_type' => $masterData->item_type,
                            'product_id'=>$request->product_id[$x],
                            'storage_location_id'=> $masterData->storage_location_id,
                            'inv_qty'=> -$inv_qty,
                            'inv_uom'=> $masterData->inv_uom,
                            'whse_qty'=> -$inv_qty,
                            'whse_uom'=> $masterData->whse_uom,
                            'warehouse_id' => $masterData->warehouse_id,
                            'client_id' => $masterData->client_id,
                            'store_id' => $masterData->store_id
                        ]);
                        MasterfileModel::create([
                            'ref_no'=> $twd_no,
                            'status' => 'R',
                            'trans_type' => 'WD',
                            'item_type' => $masterData->item_type,
                            'product_id'=>$request->product_id[$x],
                            'storage_location_id'=>$masterData->storage_location_id,
                            'inv_qty'=> $inv_qty,
                            'inv_uom'=> $masterData->inv_uom,
                            'whse_qty'=> $inv_qty,
                            'whse_uom'=> $masterData->whse_uom,
                            'warehouse_id' => $masterData->warehouse_id,
                            'client_id' => $masterData->client_id,
                            'store_id' => $masterData->store_id
                        ]);
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
                'data'    => throw new Exception($e)//$e->getMessage()
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
        $client_list = Client::where('client_type','C')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('withdraw/view', [
            'wd' => $wd,
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
        $client_list = Client::where('client_type','C')->get();
        $deliver_list = Client::where('client_type','T')->get();
        $warehouse_list = Warehouse::all();
        $uom = UOM::all();

        return view('withdraw/edit', [
            'wd' => $wd,
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
    public function destroy($id)
    {
        //
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
                ->leftJoin('client_list as cl', 'cl.id', '=', 'wd_hdr.client_id')
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
}
