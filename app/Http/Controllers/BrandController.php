<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $brand_list = Brand::select('*')
                ->where([
                    [function ($query) use ($request) {
                        if (($s = $request->q)) {
                            $query->orWhere('brand_name','like', '%'.$s.'%');
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
                ->orderBy('sort_by','ASC')
                ->orderByDesc('created_at')
                ->paginate(20);
        return view('maintenance/brand/index', ['brand_list' => $brand_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brand = Brand::all();

        return view('maintenance/brand/create', [
            'brand'=>$brand
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
            'brand_name' => 'required',
            'sort_by' => 'required',
        ], [
            'brand_name' => 'Brand name  is required',
            'sort_by' => 'sort by  is required'
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->toArray() as $error){
                return response()->json(["status" => false, "message" => $error[0]],200);
            }
        }
        try {
            $brand = Brand::updateOrCreate(['brand_id' => $request->brand_id], [
                'brand_name'=>$request->brand_name,
                'sort_by'=>$request->sort_by,
                'is_enabled'=>$request->is_enabled,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $brand,
                'id'=> _encode($brand->brand_id)
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
         $brand = Brand::find(_decode($id));

        return view('maintenance/brand/view', [
            'brand'=>$brand
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
         $brand = Brand::find(_decode($id));

        return view('maintenance/brand/edit', [
            'brand'=>$brand
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
