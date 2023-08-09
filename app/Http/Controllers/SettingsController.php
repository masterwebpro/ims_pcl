<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UOM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use DataTables;

class SettingsController extends Controller
{
    public function getStoreByClient(Request $request) {
        $data = \App\Models\Store::where('client_id', $request->client_id)->get();
        return response()->json($data);
    }

    function getProducts(Request $request) {
        $products = \App\Models\Products::all();
        return Datatables::of($products)->addIndexColumn()->make();
    }

    function getProductBySupplier(Request $request, $supplier_id) {
        $products = \App\Models\Products::where('supplier_id', $supplier_id)->get();
        return Datatables::of($products)->addIndexColumn()->make();
    }

    function getPostedPo(Request $request) {
        $data = \App\Models\PoHdr::select('po_num')->where('status', 'posted')->get();
        return response()->json($data);
    }

    public function getUom(Request $request) {

        $uom_list = UOM::all();
        $rand_no = mt_rand(1000,9999)."A";

       // $html = '<select name="uom[]" data-id="'.$rand_no.'" id="uom_'.$rand_no.'" class="uom form-select select2">';
        $html = '<option value="">UOM</option>';
        foreach ($uom_list as $uom) {
            $html .= "<option value='".$uom->uom_id."'>".$uom->code."</option>";
        }
       // $html .= " </select> ";

        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'data'    => $html
        ]);
    }

    public function getWarehouseByStore(Request $request) {
        $data = \App\Models\Warehouse::where('store_id', $request->store_id)->get();
        return response()->json($data);
    }

    public function getBrandByCategory(Request $request) {
        $data = \App\Models\CategoryBrand::select('brands.brand_name','category_brands.brand_id','category_brands.category_brand_id')
                ->where('category_brands.category_id', $request->category_id)
                ->leftJoin('brands','brands.brand_id','category_brands.brand_id')
                ->groupBy('category_brands.brand_id')
                ->get();
        return response()->json($data);
    }

    public function getAttributeEntity(Request $request){
        $data = \App\Models\AttributeEntity::where('attribute_id', $request->attribute_id)->get();
        return response()->json([
            'success'  => true,
            'data'    => $data]);
    }

    public function getCategoryAttribute(Request $request){
        $data =  \App\Models\CategoryAttribute::select('category_attributes.attribute_id','attributes.attribute_code','attributes.attribute_name','attributes.attribute_input_type')
                    ->leftJoin('attributes','attributes.attribute_id','category_attributes.attribute_id')
                    ->where('category_attributes.category_id',$request->category_id)
                    ->get();
        return response()->json([
            'success'  => true,
            'data'    => $data]);
    }

    public function getProductAttribute(Request $request){
        $data =  \App\Models\ProductAttribute::select('product_attributes.product_attribute_id','product_attributes.attribute_id','product_attributes.attribute_value','attributes.attribute_code','attributes.attribute_name','attributes.attribute_input_type')
                    ->leftJoin('attributes','attributes.attribute_id','product_attributes.attribute_id')
                    ->where('product_attributes.product_id',$request->product_id)
                    ->get();
        return response()->json([
            'success'  => true,
            'data'    => $data]);
    }

}
