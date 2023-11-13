<?php

namespace App\Http\Controllers;

use App\Models\Trucker;
use App\Http\Controllers\Controller;
use App\Models\PlateNoList;
use App\Models\TruckType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TruckerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $trucker_list = Trucker::select('trucker_list.*','pnl.plate_no','pnl.vehicle_type','pnl.id as plate_id','pnl.is_enabled as enable')
                    ->leftJoin('plate_no_list as pnl','pnl.trucker_id','trucker_list.id')
                ->where([
                    [function ($query) use ($request) {
                        if (($s = $request->q)) {
                            $query->orWhere('trucker_list.trucker_name','like', '%'.$s.'%');
                            $query->orWhere('pnl.plate_no','like', '%'.$s.'%');
                            $query->orWhere('pnl.vehicle_type', 'like', '%' . $s . '%');
                            $query->get();
                        }
                    }]
                ])
                ->where([
                    [function ($query) use ($request) {
                        if ($request->filter_date) {
                            if($request->filter_date == 'created_at' && $request->date ) {
                                $query->whereBetween('created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                            }
                        }

                        $query->get();
                    }]
                ])
                ->groupBy('pnl.id')
                ->orderByDesc('created_at')
                ->paginate(20);
        return view('maintenance/trucker/index', ['trucker_list' => $trucker_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $truck_type = TruckType::all();

        return view('maintenance/trucker/create', [
            'truck_type' => $truck_type
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
            'trucker_name' => 'required|present',
            'vehicle_type.*' => 'required|present',
            'plate_no.*' => 'required|present'
        ], [
            'trucker_name' => 'Trucker name is required',
            'vehicle_type.*' => 'Vehicle type is required',
            'plate_no.*' => 'Plate no is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::connection()->beginTransaction();
        try {
            $trucker = Trucker::updateOrCreate(['id' => isset($request->trucker_id) ? $request->trucker_id : null], [
                'trucker_name'=>$request->trucker_name,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

            if(isset($request->plate_no)){
                for($x=0; $x < count($request->plate_no); $x++ ) {
                    $plate = array(
                        'vehicle_type' => $request->vehicle_type[$x],
                        'plate_no' => $request->plate_no[$x],
                        'trucker_id'=> isset($request->trucker_id) ? $request->trucker_id[$x] :$trucker->id,
                    );
                    PlateNoList::updateOrCreate([
                        'id' => isset($request->id[$x]) ? $request->id[$x] : null
                    ],$plate);
                }
            }

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $trucker,
                'id'=> _encode($trucker->id)
            ]);

        }
        catch(\Throwable $e)
        {
            DB::connection()->rollBack();
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
     * @param  \App\Models\Trucker  $Trucker
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trucker = PlateNoList::select('plate_no_list.*','tl.trucker_name')->where('plate_no_list.id',_decode($id))
                    ->leftJoin('trucker_list as tl','tl.id','=','plate_no_list.trucker_id')
                    ->first();
        $truck_type = TruckType::all();
        return view('maintenance/trucker/view', [
            'trucker'=>$trucker,
            'truck_type' => $truck_type
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Trucker  $Trucker
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $trucker = PlateNoList::select('plate_no_list.*','tl.trucker_name')->where('plate_no_list.id',_decode($id))
                    ->leftJoin('trucker_list as tl','tl.id','=','plate_no_list.trucker_id')
                    ->first();
        $truck_type = TruckType::all();
        return view('maintenance/trucker/edit', [
            'trucker' => $trucker,
            'truck_type' => $truck_type
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Trucker  $Trucker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Trucker $Trucker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Trucker  $Trucker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Trucker $Trucker)
    {
        //
    }
}
