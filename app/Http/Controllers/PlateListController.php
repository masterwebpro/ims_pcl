<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trucker;
use App\Models\PlateNoList;
use App\Models\TruckType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlateListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $trucker_list = PlateNoList::select('plate_no_list.*','trucker_list.trucker_name')
                    ->leftJoin('trucker_list','trucker_list.id','plate_no_list.trucker_id')
                ->where([
                    [function ($query) use ($request) {
                        if (($s = $request->q)) {
                            $query->orWhere('trucker_list.trucker_name','like', '%'.$s.'%');
                            $query->orWhere('plate_no_list.plate_no','like', '%'.$s.'%');
                            $query->orWhere('plate_no_list.vehicle_type', 'like', '%' . $s . '%');
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
                ->groupBy('plate_no_list.id')
                ->orderByDesc('created_at')
                ->paginate(20);
        return view('maintenance/platelist/index', ['trucker_list' => $trucker_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $truck_type = TruckType::all();
        $trucker = Trucker::all();

        return view('maintenance/platelist/create', [
            'truck_type' => $truck_type,
            'trucker' => $trucker
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
            'trucker_id' => 'required|present',
            'vehicle_type.*' => 'required|present',
            'plate_no.*' => 'required|present'
        ], [
            'trucker_id' => 'Trucker name is required',
            'vehicle_type.*' => 'Vehicle type is required',
            'plate_no.*' => 'Plate no is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::connection()->beginTransaction();
        try {
            if(isset($request->plate_no)){
                for($x=0; $x < count($request->plate_no); $x++ ) {
                    $plate = array(
                        'vehicle_type' => $request->vehicle_type[$x],
                        'plate_no' => $request->plate_no[$x],
                        'trucker_id'=> $request->trucker_id,
                        'is_enabled'=> $request->is_enabled,
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
        $plate_list = PlateNoList::select('plate_no_list.*','tl.trucker_name','tl.id as trucker_id')->where('plate_no_list.id',_decode($id))
                    ->leftJoin('trucker_list as tl','tl.id','=','plate_no_list.trucker_id')
                    ->first();
        $truck_type = TruckType::all();
        $trucker = Trucker::all();
        return view('maintenance/platelist/view', [
            'plate_list'=>$plate_list,
            'truck_type' => $truck_type,
            'trucker' => $trucker
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
        $plate_list = PlateNoList::select('plate_no_list.*','tl.trucker_name','tl.id as trucker_id')->where('plate_no_list.id',_decode($id))
                    ->leftJoin('trucker_list as tl','tl.id','=','plate_no_list.trucker_id')
                    ->first();
        $truck_type = TruckType::all();
        $trucker = Trucker::all();

        return view('maintenance/platelist/edit', [
            'plate_list'=>$plate_list,
            'truck_type' => $truck_type,
            'trucker' => $trucker
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
