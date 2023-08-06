<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryBrand;
use App\Models\Products;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $product_list = Products::select('products.*','u.name as updated_by','sup.supplier_name')
                        ->leftJoin('users as u','u.id','products.created_by')
                        ->leftJoin('suppliers as sup','sup.id','products.supplier_id')
                        ->with('category_brand')
                        ->where([
                            [function ($query) use ($request) {
                                if (($s = $request->q)) {
                                    $query->leftJoin('users as u','u.id','products.created_by');
                                    $query->orWhere('u.name','like', '%'.$s.'%');
                                    $query->orWhere('products.product_name','like', '%'.$s.'%');
                                    $query->orWhere('products.product_upc','like', '%'.$s.'%');
                                    $query->orWhere('products.product_sku','like', '%'.$s.'%');
                                    $query->get();
                                }
                            }]
                        ])
                        ->where([
                            [function ($query) use ($request) {
                                if ($request->filter_date) {
                                    if($request->filter_date == 'created_at' && $request->date ) {
                                        $query->whereBetween('products.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                                    }
                                }

                                $query->get();
                            }]
                        ])
                        ->orderByDesc('products.created_at')
                        ->paginate(20);
        return view('maintenance/product/index', ['product_list' => $product_list]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supplier_list = Supplier::all();
        $category = Category::all();
        return view('maintenance/product/create',['supplier_list' => $supplier_list, 'category' => $category]);
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
            'product_code' => 'required',
            'product_name' => 'required',
            'supplier_id' => 'required',
        ], [
            'supplier_id' => 'Supplier is required',
            'product_code' => 'Product code  is required',
            'product_name' => 'Product name  is required'
        ]);

        if ($validator->fails()) {
            foreach($validator->errors()->toArray() as $error){
                return response()->json(["status" => false, "message" => $error[0]],200);
            }
        }

        try {
            $product = Products::updateOrCreate(['product_id' => $request->product_id], [
                'product_code'=>$request->product_code,
                'product_name'=>$request->product_name,
                'product_upc'=>$request->product_upc,
                'product_sku'=>$request->product_sku,
                'supplier_id'=>$request->supplier_id,
                'category_brand_id'=>$request->category_brand_id,
                'created_by' => Auth::user()->id,
                'is_enabled'=>$request->is_enabled,
                'is_serialize'=>$request->is_serialize,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

        DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $product,
                'id'=> _encode($product->product_id)
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
