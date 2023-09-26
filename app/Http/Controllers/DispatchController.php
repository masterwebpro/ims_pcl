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
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->orWhere('dispatch_hdr.dispatch_no','like', '%'.$s.'%');
                    $query->get();
                }
            }]
        ])->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('dispatch_hdr.status', $s);
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
            $dispatch_date = isset($request->dispatch_date) ? $request->dispatch_date : date("Y-m-d");
            $dispatch = DispatchHdr::updateOrCreate(['dispatch_no' => $dispatch_no], [
                'dispatch_no'=>$dispatch_no,
                'dispatch_date'=> $dispatch_date,
                'dispatch_by'=> $request->dispatch_by,
                'start_datetime'=> $start,
                'finish_datetime'=> $finish,
                'depart_datetime'=> $date_departed,
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
            $wd_id = WdHdr::where('dispatch_no',$dispatch_no)->pluck('id');
            WdHdr::whereIN('id',$wd_id)->update(['dispatch_no' => null]);
            DispatchDtl::where('dispatch_no',$dispatch_no)->delete();
            if(isset($request->wd_no)){
                for($x=0; $x < count($request->wd_no); $x++ ) {
                    $dtl = array(
                        'wd_no' => $request->wd_no[$x],
                        'qty' => $request->wd_qty[$x],
                        'dispatch_no'=>$dispatch_no,
                    );
                    $dtl = DispatchDtl::create($dtl);
                    WdHdr::where('wd_no',$request->wd_no[$x])->update(['dispatch_no' => $dispatch_no]);

                    if($request->status == 'posted'){
                        $wd_dtl = WdDtl::where('wd_no',$request->wd_no[$x])->get();
                        foreach($wd_dtl as $wdtl){
                            $masterData = MasterfileModel::find($wdtl->masterfile_id);
                            MasterfileModel::create([
                                'ref_no'=> $wdtl->wd_no,
                                'status' => 'R',
                                'trans_type' => 'WD',
                                'item_type' => $masterData->item_type,
                                'date_received' => $masterData->date_received,
                                'product_id'=>$wdtl->product_id,
                                'storage_location_id'=> $masterData->storage_location_id,
                                'inv_qty'=> -$wdtl->inv_qty,
                                'inv_uom'=> $wdtl->inv_uom,
                                'whse_qty'=> -$wdtl->inv_qty,
                                'whse_uom'=> $wdtl->inv_uom,
                                'warehouse_id' => $masterData->warehouse_id,
                                'customer_id' => $masterData->customer_id,
                                'company_id' => $masterData->company_id,
                                'store_id' => $masterData->store_id
                            ]);
                        }

                        Pod::create([
                            'batch_no' => $request->wd_no[$x],
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
    public function destroy($dispatch)
    {
        //
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
}
