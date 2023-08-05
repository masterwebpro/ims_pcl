<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\StorageLocationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StorageLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locations = StorageLocationModel::select('storage_locations.*','wh.warehouse_name','s.store_name','c.client_name')
                ->leftJoin('warehouses as wh', 'wh.id', '=', 'storage_locations.warehouse_id')
                ->leftJoin('store_list as s', 's.id', '=', 'wh.store_id')
                ->leftJoin('client_list as c', 'c.id', '=', 'wh.client_id')
                ->where([
                    [function ($query) use ($request) {
                        if (($s = $request->q)) {
                            $query->leftJoin('warehouses as wh', 'wh.id', '=', 'storage_locations.warehouse_id');
                            $query->leftJoin('store_list as s', 's.id', '=', 'wh.store_id');
                            $query->leftJoin('client_list as c', 'c.id', '=', 'wh.client_id');
                            $query->orWhere('wh.warehouse_name','like', '%'.$s.'%');
                            $query->orWhere('storage_locations.rack','like', '%'.$s.'%');
                            $query->orWhere('storage_locations.level','like', '%'.$s.'%');
                            $query->orWhere('storage_locations.location','like', '%'.$s.'%');
                            $query->get();
                        }
                    }]
                ])
                ->where([
                    [function ($query) use ($request) {
                        if ($request->filter_date) {
                            if($request->filter_date == 'created_at' && $request->date ) {
                                $query->whereBetween('storage_locations.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                            }
                        }

                        $query->get();
                    }]
                ])
                ->orderByDesc('storage_locations.created_at')
                ->paginate(20);
        return view('maintenance/location/index', ['locations' => $locations]);
    }
    public function create()
    {
        $client_list = Client::all();
        return view('maintenance/location/create', [
            'client_list'=> $client_list,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse' => 'required',
            'client' => 'required',
            'store' => 'required',
            'rack' => 'required',
            'level' => 'required',
        ], [
            'store' => 'Store is required',
            'client' => 'Client is required',
            'warehouse' => 'Warehouse is required',
            'rack' => 'Rack is required',
            'level' => 'Level is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::connection()->beginTransaction();

        try {

            if($request->storage_location_id) {
                $location = StorageLocationModel::where('storage_location_id', $request->storage_location_id);
                $location->update([
                    'warehouse_id'=>$request->warehouse,
                    'rack'=>$request->rack,
                    'level'=>$request->level,
                    'location'=>$request->rack."-".$request->level,
                    'is_enabled'=>($request->is_enabled == 'on') ? 1 : 0,
                    'updated_at'=>$this->current_datetime
                ]);
            }
            else {
                $location = StorageLocationModel::updateOrCreate(['storage_location_id' => $request->storage_location_id], [
                    'warehouse_id'=>$request->warehouse,
                    'rack'=>$request->rack,
                    'level'=>$request->level,
                    'location'=>$request->rack."-".$request->level,
                    'is_enabled'=>($request->is_enabled == 'on') ? 1 : 0,
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime,
                ]);
            }
            

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $location,
                //'id'=> _encode($location->storage_location_id)
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $location = StorageLocationModel::where('storage_location_id', _decode($id))->first();
        $client_list = Client::all();
        return view('maintenance/location/view', [
            'location'=>$location,
            'client_list' => $client_list,
        ]);
    }

    public function edit($id)
    {
        $location = StorageLocationModel::where('storage_location_id', _decode($id))->first();
        $client_list = Client::all();
        return view('maintenance/location/edit', [
            'location'=>$location,
            'client_list' => $client_list,
        ]);
    }
}
