<?php

namespace App\Http\Controllers;

use App\Models\Pod;
use App\Http\Controllers\Controller;
use App\Models\WdDtl;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class PodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pod_list = Pod::select('pod.*', 'u.name',
            'wd_hdr.dispatch_no',
            'dispatch_hdr.trucker_name',
            'dispatch_hdr.truck_type',
            'dispatch_hdr.plate_no',
            'dispatch_hdr.driver',
            )
        ->leftJoin('wd_hdr', 'wd_hdr.wd_no', '=', 'pod.batch_no')
        ->leftJoin('dispatch_hdr', 'dispatch_hdr.dispatch_no', '=', 'wd_hdr.dispatch_no')
        ->leftJoin('users as u', 'u.id', '=', 'pod.created_by')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->orWhere('pod.batch_no','like', '%'.$s.'%');
                    $query->get();
                }
            }]
        ])->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('pod.status', $s);
                }

                if ($request->filter_date && $request->dispatch_date) {
                    if($request->filter_date == 'dispatch_date') {
                        $query->whereBetween('pod.dispatch_date', [$request->dispatch_date." 00:00:00", $request->dispatch_date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('pod.created_at', [$request->created_at." 00:00:00", $request->created_at." 23:59:00"]);
                    }

                }

                $query->get();
            }]
        ])
        ->paginate(20);
        return view('pod/index', ['pod_list'=>$pod_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'receive_by' => 'required',
            'receive_date' => 'required',
            'receive_time' => 'required',
            'arrived_date' => 'required',
            'arrived_time' => 'required',
            'depart_date' => 'required',
            'depart_time' => 'required',
            'received_qty.*' => 'required',
        ], [
            'receive_by'=>'Received by is required',
            'receive_date'=>'Receive date is required',
            'receive_time'=>'Receive time is required',
            'arrived_date'=>'Arrive date is required',
            'arrived_time'=>'Arrive time is required',
            'depart_date'=>'Depart date is required',
            'depart_time'=>'Depart time is required',
            'received_qty.*' => 'Receive quantity is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        DB::beginTransaction();

        try {
            $pod = Pod::find(_decode($request->id));
            $arrived_date = date("Y-m-d", strtotime($request->arrived_date))." ".date("H:i:s", strtotime($request->arrived_time));
            $receive_date = date("Y-m-d", strtotime($request->receive_date))." ".date("H:i:s", strtotime($request->receive_time));
            $depart_date = date("Y-m-d", strtotime($request->depart_date))." ".date("H:i:s", strtotime($request->depart_time));
            $pod->update([
                'receive_by' => $request->receive_by,
                'status' => $request->status,
                'arrived_date' => $arrived_date,
                'depart_date' => $depart_date,
                'receive_date' => $receive_date,
                'remarks' => $request->remarks
            ]);
            if(isset($request->wddtl_id)){
                for($x=0; $x < count($request->wddtl_id); $x++ ) {
                   $wddtl = WdDtl::find($request->wddtl_id[$x]);
                   $wddtl->update(['actual_rcv_qty' => $request->received_qty[$x]]);
                }
            }
            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $pod,
                'id'=> _encode($pod->id)
            ]);
        }
        catch (\Throwable $e) {
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
     * @param  \App\Models\Pod  $pod
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pod = Pod::select('pod.*', 'u.name',
        'wd_hdr.dispatch_no',
        'dispatch_hdr.trucker_name',
        'dispatch_hdr.truck_type',
        'dispatch_hdr.plate_no',
        'dispatch_hdr.driver',
        'dispatch_hdr.seal_no',
        )
    ->leftJoin('wd_hdr', 'wd_hdr.wd_no', '=', 'pod.batch_no')
    ->leftJoin('dispatch_hdr', 'dispatch_hdr.dispatch_no', '=', 'wd_hdr.dispatch_no')
        ->leftJoin('users as u', 'u.id', '=', 'pod.created_by')
        ->where('pod.id', _decode($id))->first();
        $status = [
            'Dispatch',
            'Incomplete',
            'Complete',
            'Delivered'
        ];
        return view('pod/view', ['pod'=>$pod, 'status' => $status]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pod  $pod
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pod = Pod::select('pod.*', 'u.name',
        'wd_hdr.dispatch_no',
        'dispatch_hdr.trucker_name',
        'dispatch_hdr.truck_type',
        'dispatch_hdr.plate_no',
        'dispatch_hdr.driver',
        'dispatch_hdr.seal_no',
        )
    ->leftJoin('wd_hdr', 'wd_hdr.wd_no', '=', 'pod.batch_no')
    ->leftJoin('dispatch_hdr', 'dispatch_hdr.dispatch_no', '=', 'wd_hdr.dispatch_no')
        ->leftJoin('users as u', 'u.id', '=', 'pod.created_by')
        ->where('pod.id', _decode($id))->first();
        $status = [
            'Dispatch',
            'Incomplete',
            'Complete',
            'Delivered'
        ];

        return view('pod/edit', ['pod'=>$pod, 'status' =>  $status]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pod  $pod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pod $pod)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pod  $pod
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pod $pod)
    {
        //
    }
}
