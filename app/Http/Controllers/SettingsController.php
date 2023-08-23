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
        $data = \App\Models\Store::where('client_id', $request->client_id)->where('is_enabled', 1)->get();
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
        $data = \App\Models\Warehouse::where('store_id', $request->store_id)->where('is_enabled', 1)->get();
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

    function getMasterfile(Request $request, $client_id, $store_id, $warehouse_id) {
        $products = \App\Models\Products::where('supplier_id', $supplier_id)->get();
        return Datatables::of($products)->addIndexColumn()->make();
    }

    function getRackPerWarehouse($warehouse_id) {
        $data =  \App\Models\StorageLocationModel::select('rack')->where('warehouse_id', $warehouse_id)->orderBy('rack')->groupBy('rack')->get();
        return response()->json([
            'success'  => true,
            'data'    => $data]);
    }

    function getLayerPerWarehouse(Request $request, $warehouse_id, $rack) {
        $data =  \App\Models\StorageLocationModel::select('level')->where('warehouse_id', $warehouse_id)->where('rack', $rack)->orderBy('rack')->groupBy('rack')->get();
        return response()->json([
            'success'  => true,
            'data'    => $data]);
    }

    function getLocationPerWarehouse($warehouse_id) {
        $result =  \App\Models\StorageLocationModel::select('level', 'rack')->where('warehouse_id', $warehouse_id)->orderBy('rack')->groupBy('rack')->get();
        $data = [];
        foreach($result as $res) {
            $data[$warehouse_id][$res->rack][$res->level][] = array('layer'=>$res->level);
        }
        return  $data;
    }

    public function getLevel(Request $request) {
        $data = \App\Models\StorageLocationModel::where('rack', $request->rack)->where('warehouse_id', $request->warehouse_id)->get();
        return response()->json($data);
    }

    public function getStorageLocation(Request $request) {
        $data = \App\Models\StorageLocationModel::select('storage_location_id')->where('warehouse_id', $request->warehouse_id);

        if($request->rack > 0)
            $data->where('rack', $request->rack);
        if($request->layer > 0)
            $data->where('level', $request->layer);
    
        $data = $data->get();
        return response()->json($data);
    }

    public function getMasterfileData(Request $request) {
        $result = \App\Models\MasterfileModel::select('p.product_id','p.product_code','p.product_name','item_type', 'sl.storage_location_id as old_location_id', 'sl.location as old_location', 'iu.code as i_code',  'iu.uom_id as i_uom_id', 'wu.code as w_code', 'wu.uom_id as w_uom_id','sl.rack as rack', 'sl.level as layer', DB::raw("SUM(inv_qty) as inv_qty"), DB::raw("SUM(inv_qty) as whse_qty"))
                ->where('masterfiles.warehouse_id', $request->warehouse_id)
                ->having('inv_qty', '>', 0)
                ->having('whse_qty', '>', 0)
                ->leftJoin('products as p','p.product_id','masterfiles.product_id')
                ->leftJoin('storage_locations as sl','sl.storage_location_id','masterfiles.storage_location_id')
                ->leftJoin('uom as wu','wu.uom_id','masterfiles.whse_uom')
                ->leftJoin('uom as iu','iu.uom_id','masterfiles.inv_uom')
                ->groupBy('p.product_id','p.product_code','p.product_name','item_type', 'sl.storage_location_id', 'sl.location', 'iu.code',  'iu.uom_id', 'wu.code', 'wu.uom_id','sl.rack', 'sl.level');
        
        if(isset($request->storage_id))
            $result->whereIn('masterfiles.storage_location_id', explode(",",$request->storage_id));

        if($request->client_id > 0)
            $result->where('client_id', $request->client_id);

        if($request->store_id > 0)
            $result->where('store_id', $request->store_id);
        
        if($request->rcv_no > 0)
            $result->where('ref_no', $request->rcv_no);
    
        $record = $result->get();        
        return response()->json($record);
    }

    // public function getMasterfileData(Request $request) {
    //     $result = \App\Models\MasterfileModel::select('p.product_id','p.product_code','p.product_name','item_type', 'sl.storage_location_id as old_location_id', 'sl.location as old_location', 'inv_qty', 'iu.code as i_code',  'iu.uom_id as i_uom_id', 'whse_qty', 'wu.code as w_code', 'wu.uom_id as w_uom_id','sl.rack as rack', 'sl.level as layer')
    //             ->where('masterfiles.warehouse_id', $request->warehouse_id)
    //             ->leftJoin('products as p','p.product_id','masterfiles.product_id')
    //             ->leftJoin('storage_locations as sl','sl.storage_location_id','masterfiles.storage_location_id')
    //             ->leftJoin('uom as wu','wu.uom_id','masterfiles.whse_uom')
    //             ->leftJoin('uom as iu','iu.uom_id','masterfiles.inv_uom');
        
    //     if(isset($request->storage_id))
    //         $result->whereIn('masterfiles.storage_location_id', explode(",",$request->storage_id));

    //     if($request->client_id > 0)
    //         $result->where('client_id', $request->client_id);

    //     if($request->store_id > 0)
    //         $result->where('store_id', $request->store_id);
        
    //     if($request->rcv_no > 0)
    //         $result->where('ref_no', $request->rcv_no);
    
    //     $record = $result->get();        
    //     return response()->json($record);
    // }

    public function getNewLocation(Request $request, $warehouse_id) {

        $location_list = \App\Models\StorageLocationModel::select('storage_location_id','location')->where('warehouse_id', $warehouse_id)->get();
        
        $html = '<option value="">New Loc</option>';
        foreach ($location_list as $loc) {
            $html .= "<option value='".$loc->storage_location_id."'>".$loc->location."</option>";
        }
        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'data'    => $html
        ]);
    }

    function getProduct(Request $request) {
        $item_code = $request->item_code;
        $products = \App\Models\Products::select('*')
            ->orWhere('product_code', $item_code)
            ->orWhere('product_sku', $item_code)
            ->orWhere('product_upc', $item_code)
            ->first();
        return response()->json([
            'success'  => true,
            'id'=>rand(100,1000),
            'data'    => $products
        ]);
       
    }

}
