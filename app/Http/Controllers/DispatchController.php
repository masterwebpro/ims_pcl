<?php

namespace App\Http\Controllers;

use App\Models\DispatchDtl;
use App\Models\DispatchTruck;
use App\Http\Controllers\Controller;
use App\Models\DispatchHdr;
use App\Models\WdHdr;
use App\Models\WdDtl;
use App\Models\MasterfileModel;
use App\Models\SeriesModel;
use App\Models\TruckType;
use App\Models\AuditTrail;
use App\Models\MasterdataModel;
use App\Models\Pod;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DispatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dispatch_list = DispatchHdr::select('dispatch_hdr.*', 'u.name')
        ->withCount('items')
        ->leftJoin('users as u', 'u.id', '=', 'dispatch_hdr.created_by')
        ->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('dispatch_hdr.status', $s);
                }

                if ($request->q) {
                    $query->where('dispatch_hdr.dispatch_no', $request->q)
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

                if ($request->filter_date && $request->dispatch_date) {
                    if($request->filter_date == 'dispatch_date') {
                        $query->whereBetween('dispatch_hdr.dispatch_date', [$request->dispatch_date." 00:00:00", $request->dispatch_date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('dispatch_hdr.created_at', [$request->created_at." 00:00:00", $request->created_at." 23:59:00"]);
                    }

                }

                $query->get();
            }]
        ])
        ->paginate(20);
        return view('dispatch/index', ['dispatch_list'=>$dispatch_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $truck_type_list = TruckType::all();
        $plate_no_list =  \App\Models\PlateNoList::select('plate_no_list.vehicle_type','tt.vehicle_code','tt.vehicle_desc','plate_no_list.plate_no','plate_no_list.trucker_id','tl.trucker_name')
        ->leftJoin('trucker_list as tl','tl.id','plate_no_list.trucker_id')
        ->leftJoin('truck_type as tt','tt.vehicle_code','plate_no_list.vehicle_type')
        ->groupBy('plate_no_list.plate_no')
        ->get();
        return view('dispatch/create', [
            'truck_type_list' => $truck_type_list,
            'plate_no_list' => $plate_no_list,
            'created_by' => Auth::user()->name,
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
        // return $request;
        $validator = Validator::make($request->all(), [
            'plate_no' => 'required',
            'dispatch_by' => 'required',
            'dispatch_date' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
            'finish_date' => 'required',
            'finish_time' => 'required',
            'date_departed' => 'required',
            'time_departed' => 'required',
            'start_pick_date' => 'required',
            'start_pick_time' => 'required',
            'finish_pick_date' => 'required',
            'finish_pick_time' => 'required',
            'arrival_date' => 'required',
            'arrival_time' => 'required',
            'wd_no.*' => 'required',
            'trucker_name' => 'required',
            'truck_type' => 'required',
            'seal_no' => 'required',
            'driver' => 'required',
        ], [
            'dispatch_by'=>'Dispatch by is required',
            'dispatch_date'=>'Dispatch date is required',
            'start_date'=>'Start date is required',
            'start_time'=>'Start time is required',
            'finish_date'=>'Finish date is required',
            'finish_time'=>'Finish time is required',
            'date_departed'=>'Depart date is required',
            'time_departed'=>'Depart time is required',
            'start_pick_date' => 'Start picking date is required',
            'start_pick_time'=>'Start picking time is required',
            'finish_pick_date' => 'Finish picking  date is required',
            'finish_pick_time'=>'Finish picking time is required',
            'arrival_date' => 'Arrival date is required',
            'arrival_date'=>'Arrival time is required',
            'wd_no.*'=>'Withdrawal is required',
            'trucker_name'=>'Trucker name is required',
            'truck_type'=>'Truck type is required',
            'plate_no' => 'Plate no is required',
            'seal_no' => 'Seal no is required',
            'driver' => 'Driver name is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        DB::beginTransaction();

        try {
            $dispatch_no = $request->dispatch_no;
            if($dispatch_no=='') {
                $dispatch_no = $this->generateDispatchNo("DP","D");

                $series[] = [
                    'series' => $dispatch_no,
                    'trans_type' => 'DP',
                    'created_at' => $this->current_datetime,
                    'updated_at' => $this->current_datetime,
                    'user_id' => Auth::user()->id,
                ];
                SeriesModel::insert($series);
            }
            $start = date("Y-m-d", strtotime($request->start_date))." ".date("H:i:s", strtotime($request->start_time));
            $finish = date("Y-m-d", strtotime($request->finish_date))." ".date("H:i:s", strtotime($request->finish_time));
            $date_departed = date("Y-m-d", strtotime($request->date_departed))." ".date("H:i:s", strtotime($request->time_departed));
            $start_pick = date("Y-m-d", strtotime($request->start_pick_date))." ".date("H:i:s", strtotime($request->start_pick_time));
            $finish_pick = date("Y-m-d", strtotime($request->finish_pick_date))." ".date("H:i:s", strtotime($request->finish_pick_time));
            $arrival = date("Y-m-d", strtotime($request->arrival_date))." ".date("H:i:s", strtotime($request->arrival_time));

            $dispatch_date = isset($request->dispatch_date) ? $request->dispatch_date : date("Y-m-d");
            $dispatch = DispatchHdr::updateOrCreate(['dispatch_no' => $dispatch_no], [
                'dispatch_no'=>$dispatch_no,
                'dispatch_date'=> $dispatch_date,
                'dispatch_by'=> $request->dispatch_by,
                'start_datetime'=> $start,
                'finish_datetime'=> $finish,
                'depart_datetime'=> $date_departed,
                'start_picking_datetime'=> $start_pick,
                'finish_picking_datetime'=> $finish_pick,
                'arrival_datetime'=> $arrival,
                'trucker_name' => $request->trucker_name,
                'seal_no' => $request->seal_no,
                'truck_type' => $request->truck_type,
                'plate_no' => $request->plate_no,
                'driver' => $request->driver,
                'helper' => $request->helper,
                'contact_no' => $request->contact_no,
                'status'=>$request->status,
                'created_by' =>Auth::user()->id,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);
            // $wd_id = WdHdr::where('dispatch_no',$dispatch_no)->pluck('id');
            // WdHdr::whereIN('id',$wd_id)->update(['dispatch_no' => null]);
            $dispatch_dtl = DispatchDtl::where('dispatch_no',$dispatch_no)->get();
            foreach ($dispatch_dtl as $key => $dtl){
                $wd_detail = WdDtl::find($dtl->wd_dtl_id);
                $wd_detail->update(['dispatch_qty' => $wd_detail->dispatch_qty - $dtl->qty]);
            }
            DispatchDtl::where('dispatch_no',$dispatch_no)->delete();
            if(isset($request->wd_dtl_id)){
                $wd_no = array();
                for($x=0; $x < count($request->wd_dtl_id); $x++ ) {
                    $wd_no[$request->wd_no[$x]] = $request->wd_no[$x];
                    $dtl = array(
                        'wd_no' => $request->wd_no[$x],
                        'wd_dtl_id' => $request->wd_dtl_id[$x],
                        'qty' => $request->dispatch_qty[$x],
                        'dispatch_no'=>$dispatch_no,
                    );
                    $dtl = DispatchDtl::create($dtl);
                    $totDispatch = DispatchDtl::select(DB::raw('sum(qty) as dispatch_qty'))->where('wd_dtl_id',$request->wd_dtl_id[$x])->first();
                    $wd_detail = WdDtl::find($request->wd_dtl_id[$x]);
                    $wd_detail->update(['dispatch_qty' => $totDispatch->dispatch_qty]);

                    if($request->status == 'posted'){
                        $masterData = MasterdataModel::find($wd_detail->master_id);
                        if($masterData->inv_qty >= $request->dispatch_qty[$x] && $wd_detail->inv_qty >= $request->dispatch_qty[$x]){
                            $masterData->update([
                                'inv_qty' => $masterData->inv_qty - $request->dispatch_qty[$x],
                                'whse_qty' => $masterData->whse_qty - $request->dispatch_qty[$x],
                                'reserve_qty' => $masterData->reserve_qty - $request->dispatch_qty[$x],
                            ]);

                            MasterfileModel::create([
                                'ref_no'=> $wd_detail->wd_no,
                                'status' => 'R',
                                'trans_type' => 'WD',
                                'item_type' => $masterData->item_type,
                                'date_received' => isset($masterData->received_date) ? $masterData->received_date : "",
                                'product_id'=>$wd_detail->product_id,
                                'storage_location_id'=> $masterData->storage_location_id,
                                'inv_qty'=> -$request->dispatch_qty[$x],
                                'inv_uom'=> $wd_detail->inv_uom,
                                'whse_qty'=> -$request->dispatch_qty[$x],
                                'whse_uom'=> $wd_detail->inv_uom,
                                'warehouse_id' => $masterData->warehouse_id,
                                'customer_id' => $masterData->customer_id,
                                'company_id' => $masterData->company_id,
                                'store_id' => $masterData->store_id,
                                'ref1_no' => $wd_detail->wd_no,
                                'ref1_type' => 'WD'
                            ]);
                        }
                        else{
                            DB::rollBack();
                            return response()->json([
                                'success'  => false,
                                'message' => "Line no {$x} inventory quantity is less than dispatch quantity",
                            ]);
                        }
                    }
                }

                if($request->status == 'posted'){
                    $wd = array_values($wd_no);
                    for($w=0; $w < count($wd); $w++ ) {
                        Pod::updateOrCreate(
                            [
                                'batch_no' => $wd[$w],
                            ],
                            [
                            'batch_no' => $wd[$w],
                            'status' => 'dispatch',
                            'dispatch_by' => $request->dispatch_by,
                            'dispatch_date' => $dispatch_date,
                            'created_by' =>Auth::user()->id
                        ]);
                    }
                }

            }

            $audit_trail[] = [
                'control_no' => $dispatch_no,
                'type' => 'DP',
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
                'data'    => $dispatch,
                'id'=> _encode($dispatch->id)
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
     * @param  \App\Models\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dispatch = DispatchHdr::select('dispatch_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'dispatch_hdr.created_by')
        ->where('dispatch_hdr.id', _decode($id))->first();

        $truck_type_list = TruckType::all();
        $plate_no_list =  \App\Models\PlateNoList::select('plate_no_list.vehicle_type','tt.vehicle_code','tt.vehicle_desc','plate_no_list.plate_no','plate_no_list.trucker_id','tl.trucker_name')
                        ->leftJoin('trucker_list as tl','tl.id','plate_no_list.trucker_id')
                        ->leftJoin('truck_type as tt','tt.vehicle_code','plate_no_list.vehicle_type')
                        ->groupBy('plate_no_list.plate_no')
                        ->get();
        return view('dispatch/view', [
            'dispatch' => $dispatch,
            'truck_type_list' => $truck_type_list,
            'plate_no_list' => $plate_no_list,
            'created_by' => Auth::user()->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $dispatch = DispatchHdr::select('dispatch_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'dispatch_hdr.created_by')
        ->where('dispatch_hdr.id', _decode($id))->first();
        $plate_no_list =  \App\Models\PlateNoList::select('plate_no_list.vehicle_type','tt.vehicle_code','tt.vehicle_desc','plate_no_list.plate_no','plate_no_list.trucker_id','tl.trucker_name')
        ->leftJoin('trucker_list as tl','tl.id','plate_no_list.trucker_id')
        ->leftJoin('truck_type as tt','tt.vehicle_code','plate_no_list.vehicle_type')
        ->groupBy('plate_no_list.plate_no')
        ->get();
        $truck_type_list = TruckType::all();
        return view('dispatch/edit', [
            'dispatch' => $dispatch,
            'plate_no_list' => $plate_no_list,
            'truck_type_list' => $truck_type_list,
            'created_by' => Auth::user()->name
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $dispatch)
    {
        //
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
            $dispatch_no = $request->dispatch_no;
            if($dispatch_no) {
                $dispatch_dtl = DispatchDtl::where('dispatch_no', $dispatch_no)->get();
                DispatchHdr::where('dispatch_no', $dispatch_no)->delete();
                DispatchDtl::where('dispatch_no', $dispatch_no)->delete();
                foreach($dispatch_dtl as $dtl){
                    $wd_detail = WdDtl::find($dtl->wd_dtl_id);
                    $wd_detail->update([
                        'dispatch_qty' => $wd_detail->dispatch_qty - $dtl->qty
                    ]);
                }
                $audit_trail[] = [
                    'control_no' => $dispatch_no,
                    'type' => 'D',
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
                    'data'    => $dispatch_no
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

    public function generateDispatchNo($type,$prefix)
    {
        $data = SeriesModel::where('trans_type', '=', $type)->where('created_at', '>=', date('Y-m-01 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'));
        $count = $data->count();
        $count = $count + 1;
        $date = date('ym');

        $num = str_pad((int)$count, 5, "0", STR_PAD_LEFT);

        $series = "$prefix-" . $date . "-" . $num;

        return $series;
    }

    public function deliveryslip($id)
    {
        ob_start();
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        $dispatch = DispatchHdr::select('dispatch_hdr.*', 'u.name')
                ->leftJoin('users as u', 'u.id', '=', 'dispatch_hdr.created_by')
                ->where('dispatch_hdr.id', _decode($id))->first();
        $pdf = PDF::loadView('dispatch.deliveryslip', [
            'dispatch' => $dispatch,
            'created_by' => Auth::user()->name
        ]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download($dispatch->dispatch_no.'.pdf');
    }

    public function unpost(Request $request)
    {
        DB::connection()->beginTransaction();
        try
        {
            $dispatch_no = $request->dispatch_no;
            if($dispatch_no) {
                $dispatch = DispatchHdr::where('dispatch_no', $dispatch_no)->update(['status'=>'open']);
                $dispatch_dtl = DispatchDtl::where('dispatch_no', $dispatch_no)->get();
                foreach($dispatch_dtl as $dtl){
                    $wd_detail = WdDtl::find($dtl->wd_dtl_id);
                    $masterdata = MasterdataModel::find($wd_detail->master_id);
                    $masterdata->update([
                        'inv_qty' => $masterdata->inv_qty + $dtl->qty,
                        'whse_qty' => $masterdata->whse_qty + $dtl->qty,
                        'reserve_qty' => $masterdata->reserve_qty + $dtl->qty
                    ]);
                    MasterfileModel::where(function($cond)use($dtl, $masterdata){
                        $cond->where('ref_no', $dtl->wd_no)
                        ->where('product_id',$masterdata->product_id)
                        ->where('customer_id',$masterdata->customer_id)
                        ->where('warehouse_id',$masterdata->warehouse_id)
                        ->where('company_id',$masterdata->company_id)
                        ->where('inv_uom',$masterdata->inv_uom);
                    })->delete();

                }

                $audit_trail[] = [
                    'control_no' => $dispatch_no,
                    'type' => 'D',
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
                    'data'    => $dispatch
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
}
