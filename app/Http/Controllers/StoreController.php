<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $store = Store::select('store_list.*','c.client_name')
                ->leftJoin('client_list as c', 'c.id', '=', 'store_list.client_id')
                ->where([
                    [function ($query) use ($request) {
                        if (($s = $request->q)) {
                            $query->leftJoin('client_list as c', 'c.id', '=', 'store_list.client_id');
                            $query->orWhere('c.client_name','like', '%'.$s.'%');
                            $query->orWhere('store_list.store_code','like', '%'.$s.'%');
                            $query->orWhere('store_list.store_name','like', '%'.$s.'%');
                            $query->orWhere('store_list.tin','like', '%'.$s.'%');
                            $query->orWhere('store_list.address_1','like', '%'.$s.'%');
                            $query->orWhere('store_list.address_2','like', '%'.$s.'%');
                            $query->orWhere('store_list.city','like', '%'.$s.'%');
                            $query->orWhere('store_list.province','like', '%'.$s.'%');
                            $query->orWhere('store_list.country','like', '%'.$s.'%');
                            $query->orWhere('store_list.zipcode','like', '%'.$s.'%');
                            $query->orWhere('store_list.phone_no','like', '%'.$s.'%');
                            $query->orWhere('store_list.email_address','like', '%'.$s.'%');
                            $query->orWhere('store_list.contact_person','like', '%'.$s.'%');
                            $query->get();
                        }
                    }]
                ])
                ->where([
                    [function ($query) use ($request) {
                        if ($request->filter_date) {
                            if($request->filter_date == 'created_at' && $request->date ) {
                                $query->whereBetween('store_list.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                            }
                        }

                        $query->get();
                    }]
                ])
                ->orderByDesc('store_list.created_at')
                ->paginate(20);
        return view('maintenance/store/index', ['store' => $store]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $store = Store::all();
        $client = Client::all();
        return view('maintenance/store/create', [
            'store'=> $store,
            'client'=> $client
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
            'store_code' => 'required',
            'store_name' => 'required',
            'client_id' => 'required',
            'tin' => 'required',
            'address_1' => 'required',
            'city' => 'required',
            'province' => 'required',
            'country' => 'required',
            'zipcode' => 'required',
            'phone_no' => 'required',
            'email_address' => 'required',
            'contact_person' => 'required',
        ], [
            'store_code' => 'Store code is required',
            'store_name' => 'Store name is required',
            'client_id' => 'Client is required',
            'tin' => 'Tin is required',
            'address_1' => 'Address_1 is required',
            'city' => 'City is required',
            'province' => 'Province is required',
            'country' => 'Country is required',
            'zipcode' => 'Zipcode is required',
            'phone_no' => 'phone no is required',
            'email_address' => 'Email address is required',
            'contact_person' => 'Contact person is required',
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->toArray() as $error){
                return response()->json(["status" => false, "message" => $error[0]],200);
            }
        }

        try {
            $store = Store::updateOrCreate(['id' => $request->id], [
                'store_code' => $request->store_code,
                'store_name' => $request->store_name,
                'client_id' => $request->client_id,
                'tin' => $request->tin,
                'address_1' => $request->address_1,
                'address_2' => $request->address_2,
                'city' => $request->city,
                'province' => $request->province,
                'country' => $request->country,
                'zipcode' => $request->zipcode,
                'phone_no' => $request->phone_no,
                'email_address' => $request->email_address,
                'contact_person' => $request->contact_person,
                'is_vatable' => $request->is_vatable,
                'is_enabled' => $request->is_enabled,
                'created_by' => Auth::user()->id,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $store,
                'id'=> _encode($store->id)
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
         $store = Store::find(_decode($id));
         $client = Client::All();

        return view('maintenance/store/view', [
            'store'=>$store,
            'client'=> $client,
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
        $store = Store::find(_decode($id));
        $client = Client::All();

        return view('maintenance/store/edit', [
            'store'=>$store,
            'client'=> $client,
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
