<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Store;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $warehouse = Warehouse::select('warehouses.*','s.store_name','c.client_name')
                ->leftJoin('store_list as s', 's.id', '=', 'warehouses.store_id')
                ->leftJoin('client_list as c', 'c.id', '=', 'warehouses.client_id')
                ->where([
                    [function ($query) use ($request) {
                        if (($s = $request->q)) {
                            $query->leftJoin('store_list as s', 's.id', '=', 'warehouses.store_id');
                            $query->leftJoin('client_list as c', 'c.id', '=', 'warehouses.client_id');
                            $query->orWhere('warehouses.warehouse_name','like', '%'.$s.'%');
                            $query->get();
                        }
                    }]
                ])
                ->where([
                    [function ($query) use ($request) {
                        if ($request->filter_date) {
                            if($request->filter_date == 'created_at' && $request->date ) {
                                $query->whereBetween('warehouses.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                            }
                        }

                        $query->get();
                    }]
                ])
                ->orderByDesc('created_at')
                ->paginate(20);
        return view('maintenance/warehouse/index', ['warehouse' => $warehouse]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $client_list = Client::where('is_enabled', '1')->get();
        $store_list = Store::all();
        $warehouse = Warehouse::all();

        return view('maintenance/warehouse/create', [
            'warehouse'=> $warehouse,
            'client_list'=> $client_list,
            'store_list'=> $store_list,
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
        DB::connection()->beginTransaction();
        $validator = Validator::make($request->all(), [
            'warehouse_name' => 'required',
            'client_id' => 'required',
            'store_id' => 'required',
        ], [
            'store_id' => 'Store  is required',
            'client_id' => 'Client is required',
            'warehouse_name' => 'Warehouse name is required',
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->toArray() as $error){
                return response()->json(["status" => false, "message" => $error[0]],200);
            }
        }
        try {
            $warehouse = Warehouse::updateOrCreate(['id' => $request->id], [
                'store_id' => $request->store_id,
                'client_id' => $request->client_id ,
                'warehouse_name'=>$request->warehouse_name,
                'is_enabled'=>$request->is_enabled,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $warehouse,
                'id'=> _encode($warehouse->id)
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
        $warehouse = Warehouse::find(_decode($id));
        $client_list = Client::where('is_enabled', '1')->get();
        $store_list = Store::all();

        return view('maintenance/warehouse/view', [
            'warehouse'=>$warehouse,
            'client_list' => $client_list,
            'store_list' => $store_list
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
        $warehouse = Warehouse::find(_decode($id));
        $client_list = Client::where('is_enabled', '1')->get();
        $store_list = Store::all();
        return view('maintenance/warehouse/edit', [
            'warehouse'=>$warehouse,
            'client_list' => $client_list,
            'store_list' => $store_list
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
}
