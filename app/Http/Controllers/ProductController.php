<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attributes;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\CategoryBrand;
use App\Models\ProductAttribute;
use App\Models\ProductPrice;
use App\Models\Products;
use App\Models\ProductUom;
use App\Models\Supplier;
use App\Models\UOM;
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
        $uom = UOM::all();
        return view('maintenance/product/create',['supplier_list' => $supplier_list, 'category' => $category, 'uom' => $uom]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        DB::connection()->beginTransaction();
        $validator = Validator::make($request->all(), [
            'product_code' => 'required',
            'product_name' => 'required',
            'supplier_id' => 'required'
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

            ProductPrice::updateOrCreate(["product_price_id" => $request->product_price_id],[
                'product_id'=> $product->product_id,
                'msrp' => $request->msrp,
                'supplier_price'=> $request->supplier_price,
                'special_price'=> $request->special_price,
                'srp'=> $request->product_srp,
            ]);

            $uom = json_decode($request->uom_id);

            if(!empty($uom)){
                ProductUom::where('product_id',$product->product_id)->delete();
                foreach($uom as $key => $uom_id)
                {
                    ProductUom::updateOrCreate([
                            'product_id' => $product->product_id,
                            'uom_id' => $uom_id,
                        ], [
                        'product_id' => $product->product_id,
                        'uom_id' => $uom_id,
                    ]);
                }
            }
            else{
                ProductUom::where('product_id',$product->product_id)->delete();
            }
            $attribute_entity = json_decode($request->attribute_entity);
            if(!empty($attribute_entity)){
                ProductAttribute::where('product_id',$product->product_id)->delete();
                foreach($attribute_entity as $key => $entity)
                {
                    ProductAttribute::updateOrCreate([
                            'product_id' => $product->product_id,
                            'attribute_id' => $entity->attribute_id,
                        ], [
                        'product_id' => $product->product_id,
                        'attribute_id' => $entity->attribute_id,
                        'attribute_value' => $entity->attribute_code,
                    ]);
                }
            }
            else{
                ProductAttribute::where('product_id',$product->product_id)->delete();
            }

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
        $product = Products::find(_decode($id));
        $supplier_list = Supplier::all();
        $category = Category::all();
        $brand = Brand::all();
        $uom = UOM::all();
        $prod_category = CategoryBrand::where('category_brand_id',$product->category_brand_id)->first();
        $prod_uom = ProductUom::where('product_id',$product->product_id)->pluck('uom_id');
        $price = ProductPrice::where('product_id',$product->product_id)->first();
        return view('maintenance/product/view',
        [
            'product' => $product,
            'supplier_list' => $supplier_list,
            'category' => $category,
            'brand' => $brand,
            'uom' => $uom,
            'category' => $category,
            'price' => $price,
            'prod_category' => $prod_category,
            'prod_uom' => $prod_uom->toArray()
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
        $product = Products::find(_decode($id));
        $supplier_list = Supplier::all();
        $category = Category::all();
        $brand = Brand::all();
        $prod_category = CategoryBrand::where('category_brand_id',$product->category_brand_id)->first();
        $uom = UOM::all();
        $prod_uom = ProductUom::where('product_id',$product->product_id)->pluck('uom_id');
        $price = ProductPrice::where('product_id',$product->product_id)->first();
        return view('maintenance/product/edit',
        [
            'product' => $product,
            'supplier_list' => $supplier_list,
            'category' => $category,
            'brand' => $brand,
            'uom' => $uom,
            'price' => $price,
            'prod_category' => $prod_category,
            'prod_uom' => $prod_uom->toArray()
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
