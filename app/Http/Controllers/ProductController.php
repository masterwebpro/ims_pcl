<?php

namespace App\Http\Controllers;

use App\Exports\ProductTemplate;
use App\Http\Controllers\Controller;
use App\Imports\ProductUpload;
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
use Maatwebsite\Excel\Facades\Excel;
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
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'product_name' => 'required',
            'uom_id' => 'required|present',
            'category_id' => 'required',
            'category_brand_id' => 'required',
        ], [
            'supplier_id' => 'Supplier is required',
            // 'product_code' => 'Product code  is required',
            'product_name' => 'Product name  is required',
            'uom_id' => 'Unit of measure is required',
            'category_id' => 'Category is required',
            'category_brand_id' => 'Brand is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        DB::connection()->beginTransaction();
        try {
            $product = Products::updateOrCreate(['product_id' => $request->product_id], [
                'product_code'=> isset($request->product_code) ? $request->product_code : "TEMP_CODE",
                'product_name'=>$request->product_name,
                'product_upc'=>$request->product_upc,
                'product_sku'=>$request->product_sku,
                'supplier_id'=>$request->supplier_id,
                'category_brand_id'=>$request->category_brand_id,
                'created_by' => Auth::user()->id,
                'is_enabled'=> isset($request->is_enabled) ? 1 : 0,
                'is_serialize'=>isset($request->is_serialize) ? 1 : 0,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);
            if(!isset($request->product_id) && $product->product_code == 'TEMP_CODE'){
                $cat_name = substr($request->category,0,1);
                $seq_name = substr(preg_replace('/[^a-zA-Z]/', '', $request->product_name),0,2);
                $product_code = $cat_name.$seq_name.str_pad($product->product_id, 6, '0', STR_PAD_LEFT);
                $product->update([
                    'product_code' => $product_code,
                    'product_upc' => isset($request->product_upc) ? $request->product_upc : $product_code,
                    'product_sku' => isset($request->product_sku) ? $request->product_sku : $product_code

                ]);
            }
            ProductPrice::updateOrCreate(["product_price_id" => $request->product_price_id],[
                'product_id'=> $product->product_id,
                'msrp' => isset($request->msrp) ? $request->msrp : 0,
                'supplier_price'=> isset($request->supplier_price) ? $request->supplier_price : 0,
                'special_price'=> isset($request->special_price) ? $request->special_price : 0,
                'srp'=> isset($request->product_srp) ? $request->product_srp : 0,
            ]);

            if(isset($request->uom_id)){
                ProductUom::where('product_id',$product->product_id)->delete();
                for($x=0; $x < count($request->uom_id); $x++ )
                {
                    ProductUom::updateOrCreate([
                            'product_id' => $product->product_id,
                            'uom_id' => $request->uom_id[$x],
                        ], [
                        'product_id' => $product->product_id,
                        'uom_id' => $request->uom_id[$x],
                    ]);
                }
            }
            else{
                ProductUom::where('product_id',$product->product_id)->delete();
            }
            if(isset($request->attribute_entity)){
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

    public function productTemplate()
    {
        $supplier_list = Supplier::select('supplier_code','supplier_name')->get()->toArray();
        $supplier[] = ['SUPPLIER CODE', 'SUPPLIER NAME'];
        foreach ($supplier_list as $key => $value){
            $supplier[] = [$value['supplier_code'],$value['supplier_name']];
        }
        $category_brand[] = ['CATEGORY','BRAND'];
        $category_list = CategoryBrand::select('cat.category_name as category','br.brand_name as brand')
                        ->leftJoin('categories as cat','cat.category_id','category_brands.category_id')
                        ->leftJoin('brands as br','br.brand_id','category_brands.brand_id')
                        ->get()->toArray();
        foreach ($category_list as $key => $value){
            $category_brand[] = [$value['category'],$value['brand']];
        }

        $unit[] = ['CODE','NAME'];
        $unit_list = UOM::select('code','uom_desc as name')->get()->toArray();
        foreach ($unit_list as $key => $value){
            $unit[] = [$value['code'],$value['name']];
        }
        $data = [
            'header' => [
                ['SUPPLIER CODE','PRODUCT CODE', 'PRODUCT NAME', 'CATEGORY','BRAND','UNIT'],
                ['TAMSONS-S', 'TNB224576','FUIDMASTER 507A Flush Valve 2', 'Tampsons','FLUIDMASTER','PC','PLEASE REMOVE THIS ROW BEFORE UPLOAD'],
                ['T-ACCU-001', 'TNB224577','5 STAR PNEUMATIC DOOR CLOSER - BRONZE', 'SBMCI','BARS','PC','PLEASE REMOVE THIS ROW BEFORE UPLOAD'],
            ],
            'supplier' => $supplier,
            'category_brand' => $category_brand,
            'unit' => $unit
        ];
        return Excel::download(new ProductTemplate($data), 'product_template.xlsx');
    }

    public function uploadProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|mimes:xlsx'
        ],['excel_file' => 'Excel file is required']);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        DB::connection()->beginTransaction();

        if ($request->hasFile('excel_file')) {
            try {
                $import = new ProductUpload;
                $data = Excel::toCollection($import, $request->file('excel_file'));
                if(isset($data[0]))
                {
                    $prod_data = $data[0];
                    $header =  $data[0][0];
                    $valid_header = ['SUPPLIER CODE', 'PRODUCT CODE','PRODUCT NAME', 'CATEGORY','BRAND','UNIT'];
                    foreach($header as $val){
                        if(!in_array($val,$valid_header)){
                            return response()->json(['status' => false, 'message' => 'File upload failed, Invalid header format. Please download the correct template.']);
                        }
                    }
                    if(count($prod_data) > 1){
                        $xdata = [];
                        $rows = 2;
                        for($i=1;$i< count($prod_data);$i++){
                            if(isset($prod_data[$i][0]) && isset($prod_data[$i][1]) && isset($prod_data[$i][2]) && isset($prod_data[$i][3]) && isset($prod_data[$i][4]) && isset($prod_data[$i][5])){
                                $supplier   =     Supplier::select('id')->where('supplier_code',$prod_data[$i][0])->orWhere('supplier_name',$prod_data[$i][0])->first();
                                $category   =     CategoryBrand::select('category_brands.*')
                                                ->leftJoin('categories as cat','cat.category_id','category_brands.category_id')
                                                ->leftJoin('brands as br','br.brand_id','category_brands.brand_id')
                                                ->where('category_name',$prod_data[$i][3])
                                                ->where('brand_name',$prod_data[$i][4])
                                                ->first();

                                $xdata[$i]['is_enabled'] = 1;
                                $xdata[$i]['is_serialize'] = 0;

                                $uom = UOM::select('uom_id','code','uom_desc as name')->where('code',$prod_data[$i][5])->orWhere('uom_desc',$prod_data[$i][5])->first();
                                if($supplier){
                                    $xdata[$i]['supplier_id'] = $supplier['id'];
                                }
                                else{
                                    return response()->json(['status' => false, 'message' => "File upload failed, Row no {$rows} supplier name is not found."]);
                                }

                                $xdata[$i]['product_code'] = $prod_data[$i][1];

                                if(isset($prod_data[$i][1])){
                                    $xdata[$i]['product_name'] = $prod_data[$i][2];
                                }
                                else{
                                    return response()->json(['status' => false, 'message' => "File upload failed, Row no {$rows} product name is required."]);
                                }
                                if($category)
                                {
                                    $xdata[$i]['category_brand_id'] = $category['category_brand_id'];
                                    $xdata[$i]['category'] = $prod_data[$i][3];
                                    $xdata[$i]['category_id'] = $category['category_id'];
                                }
                                else{
                                    return response()->json(['status' => false, 'message' => "File upload failed, Row no {$rows} category brand not found."]);
                                }
                                if($uom)
                                {
                                    $xdata[$i]['uom_id'][] = $uom['uom_id'];
                                }else{
                                    return response()->json(['status' => false, 'message' => "File upload failed, Row no {$rows} unit of measure not found."]);
                                }

                            }else{
                                return response()->json(['status' => false, 'message' => "File upload failed, Row no {$rows} all header must have value."]);
                            }
                            $rows++;
                        }

                        foreach($xdata as $item){
                            $request = new Request($item);
                            $this->store($request);
                        }
                        DB::commit();
                        return response()->json([
                            'success'  => true,
                            'message' => 'File uploaded successfully!'
                        ]);
                    }
                    else{
                        return response()->json(['status' => false, 'message' => 'File upload failed, No data to be uploaded.']);
                    }
                }
                else{
                    return response()->json(['status' => false, 'message' => 'File upload failed, No data to be uploaded.']);
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
    }else{
            return response()->json(['message' => 'File upload failed'], 400);
        }

    }
}
