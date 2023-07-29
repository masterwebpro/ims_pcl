<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $supplier = Supplier::where([
                    [function ($query) use ($request) {
                        if (($s = $request->q)) {
                            $query->orWhere('supplier_name','like', '%'.$s.'%');
                            $query->orWhere('supplier_code','like', '%'.$s.'%');
                            $query->orWhere('contact_no','like', '%'.$s.'%');
                            $query->orWhere('supplier_address','like', '%'.$s.'%');
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
        return view('maintenance/supplier/index', ['supplier' => $supplier]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supplier = Supplier::all();

        return view('maintenance/supplier/create', [
            'supplier'=> $supplier,
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
            'supplier_name' => 'required',
            'supplier_code' => 'required',
            'contact_no' => 'required',
            'supplier_address' => 'required'
        ], [
            'supplier_name' => 'Supplier name is required',
            'supplier_code' => 'Supplier code is required',
            'contact_no' => 'Supplier contact is required',
            'supplier_address' => 'Supplier address is required'
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->toArray() as $error){
                return response()->json(["status" => false, "message" => $error[0]],200);
            }
        }

        try {
            $supplier = Supplier::updateOrCreate(['id' => $request->id], [
                'supplier_name' => $request->supplier_name,
                'supplier_code' => $request->supplier_code ,
                'contact_no'=>$request->contact_no,
                'supplier_address'=>$request->supplier_address,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $supplier,
                'id'=> _encode($supplier->id)
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
        $supplier = Supplier::find(_decode($id));

        return view('maintenance/supplier/view', [
            'supplier'=>$supplier
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
        $supplier = Supplier::find(_decode($id));
        return view('maintenance/supplier/edit', [
            'supplier'=>$supplier,
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
