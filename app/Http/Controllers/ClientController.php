<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $client = Client::select('*')
                ->where([
                    [function ($query) use ($request) {
                        if (($s = $request->q)) {
                            $query->orWhere('client_code','like', '%'.$s.'%');
                            $query->orWhere('client_name','like', '%'.$s.'%');
                            $query->orWhere('client_type','like', '%'.$s.'%');
                            $query->orWhere('tin','like', '%'.$s.'%');
                            $query->orWhere('address_1','like', '%'.$s.'%');
                            $query->orWhere('address_2','like', '%'.$s.'%');
                            $query->orWhere('city','like', '%'.$s.'%');
                            $query->orWhere('province','like', '%'.$s.'%');
                            $query->orWhere('country','like', '%'.$s.'%');
                            $query->orWhere('zipcode','like', '%'.$s.'%');
                            $query->orWhere('phone_no','like', '%'.$s.'%');
                            $query->orWhere('email_address','like', '%'.$s.'%');
                            $query->orWhere('contact_person','like', '%'.$s.'%');
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
                ->orderByDesc('created_at')
                ->paginate(20);
        return view('maintenance/client/index', ['client' => $client, 'type' => ['T' => "Third-Party", 'C' => "Customer", 'S' => "Supplier"]]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $client = Client::all();

        return view('maintenance/client/create', [
            'client'=> $client,
            'client_type'=> ['T' => "Third-Party", 'C' => "Customer"],
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
            'client_name' => 'required',
            'client_code' => 'required',
            'client_type' => 'required',
            // 'tin' => 'required',
            'address_1' => 'required',
            'city' => 'required',
            'province' => 'required',
            'country' => 'required',
            'zipcode' => 'required',
            // 'phone_no' => 'required',
            // 'email_address' => 'required',
            // 'contact_person' => 'required',
        ], [
            'client_name' => 'Client name is required',
            'client_code' => 'Client code is required',
            'client_type' => 'Client type is required',
            // 'tin' => 'Tin is required',
            'address_1' => 'Address1 is required',
            'city' => 'City is required',
            'province' => 'Province is required',
            'country' => 'Country is required',
            'zipcode' => 'Zipcode is required',
            // 'phone_no' => 'phone no is required',
            // 'email_address' => 'Email address is required',
            // 'contact_person' => 'Contact person is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            $client = Client::updateOrCreate(['id' => $request->id], [
                'client_code' => $request->client_code,
                'client_name' => $request->client_name,
                'client_type' => $request->client_type,
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
                'data'    => $client,
                'id'=> _encode($client->id)
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
        $client = Client::find(_decode($id));

        return view('maintenance/client/view', [
            'client'=>$client,
            'client_type'=> ['T' => "Third-Party", 'C' => "Customer"],
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
        $client = Client::find(_decode($id));
        return view('maintenance/client/edit', [
            'client'=>$client,
            'client_type'=> ['T' => "Third-Party", 'C' => "Customer"],
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
