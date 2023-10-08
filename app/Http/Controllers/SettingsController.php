<?php

namespace App\Http\Controllers;

use App\Models\AvailableItem;
use App\Models\MasterfileModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UOM;
use App\Models\WdHdr;
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
        $data = \App\Models\PoHdr::select('po_num', 'id')->where('status', 'posted')->get();
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

    function getLocationWarehouse($warehouse_id) {
        $data =  \App\Models\StorageLocationModel::where('warehouse_id', $warehouse_id)->get();

        return $data;
        // return response()->json([
        //     'success'  => true,
        //     'data'    => $data]);
    }


    public function getLevel(Request $request) {
        $data = \App\Models\StorageLocationModel::where('rack', $request->rack)->where('warehouse_id', $request->warehouse_id)->get();
        return response()->json($data);
    }

    public function getStorageLocation(Request $request) {
        $data = \App\Models\StorageLocationModel::select('storage_location_id')->where('warehouse_id', $request->warehouse_id);

        if($request->location > 0)
            $data->where('storage_location_id', $request->location);

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

        if($request->location) {
            if(isset($request->storage_id)) {
                if($request->storage_id) {
                    $result->whereIn('masterfiles.storage_location_id', explode(",",$request->storage_id));
                }
            }
        } else {
            $result->where('masterfiles.storage_location_id', null);
        }

        if($request->client_id)
            $result->where('company_id', $request->client_id);

        if($request->store_id)
            $result->where('store_id', $request->store_id);

        if($request->rcv_no) {
            $result->where('masterfiles.ref_no', $request->rcv_no);
        }


        $record = $result->get();
        return response()->json($record);
    }

    public function getNewLocation(Request $request, $warehouse_id) {

        $location_list = \App\Models\StorageLocationModel::select('storage_location_id','location')->where('warehouse_id', $warehouse_id)->orderBy('location')->get();

        $html = '<option value="">Select Location</option>';
        foreach ($location_list as $loc) {
            $html .= "<option value='".$loc->storage_location_id."'>".$loc->location."</option>";
        }
        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'data'    => $html
        ]);
    }


    public function getAvailableItem(Request $request) {
        $result = MasterfileModel::select(
                        'masterfiles.product_id',
                        'client_name',
                        'store_name',
                        'w.warehouse_name',
                        'product_code',
                        'product_name',
                        'sl.location',
                        'masterfiles.whse_uom',
                        'masterfiles.inv_uom',
                        'masterfiles.item_type',
                        'masterfiles.status',
                        'uw.code as uw_code',
                        'ui.code as ui_code',
                        DB::raw('SUM(inv_qty) as inv_qty'),
                        DB::raw('SUM(whse_qty) as whse_qty')
                    )
                    ->leftJoin('products as p','p.product_id','=','masterfiles.product_id')
                    ->leftJoin('storage_locations as sl','sl.storage_location_id','=','masterfiles.storage_location_id')
                    ->leftJoin('client_list as cl','cl.id','=','masterfiles.company_id')
                    ->leftJoin('store_list as s','s.id','=','masterfiles.store_id')
                    ->leftJoin('warehouses as w','w.id','=','masterfiles.warehouse_id')
                    ->leftJoin('uom as uw','uw.uom_id','=','masterfiles.whse_uom')
                    ->leftJoin('uom as ui','ui.uom_id','=','masterfiles.inv_uom')
                    ->where('masterfiles.status','X')
                    ->groupBy([
                        'client_name',
                        'store_name',
                        'w.warehouse_name',
                        'product_name',
                        'sl.location',
                        'masterfiles.item_type',
                        'masterfiles.status',
                        'masterfiles.whse_uom',
                        'masterfiles.inv_uom'
                    ])
                    ->having('inv_qty','>', 0)
                    ->orderBy('product_name','ASC')
                    ->orderBy('sl.location','ASC');
        if(isset($request->master_id)){
            $result->whereNotIN('masterfiles.masterfile_id', json_decode($request->master_id));
        }

        if($request->company_id > 0){
            $result->where('masterfiles.company_id', $request->company_id);
        }

        if(isset($request->warehouse_id)){
            $result->where('masterfiles.warehouse_id', $request->warehouse_id);
        }

        if($request->customer_id > 0){
            $result->where('masterfiles.customer_id', $request->customer_id);
        }

        if($request->store_id > 0){
            $result->where('masterfiles.store_id', $request->store_id);
        }

        if($request->item_type){
            $result->where('masterfiles.item_type', $request->item_type);
        }

        if(isset($request->product)){
            $keyword = '%'.$request->product.'%';
            $result->where(function($cond)use($keyword){
                $cond->where('product_code','like',$keyword)
                ->orwhere('product_name','like',$keyword)
                ->orwhere('product_sku','like',$keyword);
            });
        }
        $record = $result->get();
        return response()->json($record);
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

    function getAllProduct(Request $request) {
        $data = \App\Models\Products::select('product_id', DB::raw("CONCAT(product_code,' - ',product_name) AS product"))->get();
        return response()->json($data);
    }

    function getWithdrawalList(Request $request){
        $result = WdHdr::select('wd_hdr.id',
                    'wd_hdr.wd_no',
                    'cl.client_name',
                    'del.client_name as deliver_to',
                    DB::raw('(select sum(inv_qty) from wd_dtl where wd_no = wd_hdr.wd_no) as no_of_package'),
                    'wd_hdr.order_no',
                    'wd_hdr.order_date',
                    'wd_hdr.po_num',
                    'wd_hdr.sales_invoice',
                    'wd_hdr.dr_no'
                    )
                    ->leftJoin('client_list as cl','cl.id','wd_hdr.customer_id')
                    ->leftJoin('client_list as del','del.id','wd_hdr.deliver_to_id')
                    ->where('wd_hdr.status','posted')
                    ->whereNull('dispatch_no')
                    ->orderBy('wd_hdr.order_date','ASC');
                    if(isset($request->keyword)){
                        $search = "%".$request->keyword."%";
                        $result->where(function($cond)use($search){
                            $cond->where('wd_hdr.wd_no',$search)
                            ->orwhere('wd_hdr.order_no','like',$search)
                            ->orwhere('wd_hdr.po_num','like',$search)
                            ->orwhere('wd_hdr.sales_invoice','like',$search)
                            ->orwhere('wd_hdr.dr_no','like',$search);
                        });
                    }
                    if($request->status)
                        $result->where('wd_hdr.status', $request->status);
                    if(isset($request->wd_no))
                        $result->whereNotIN('wd_hdr.wd_no', json_decode($request->wd_no));

        $data = $result->get();
        return response()->json($data);
    }

    function getTruckType(Request $request) {
        $data = \App\Models\TruckType::select('vehicle_code','vehicle_desc')->get();
        return response()->json($data);
    }

    function getAllPostedPo(Request $request) {
        $data = \App\Models\PoHdr::select('po_hdr.id','po_num', 'po_date', 's.supplier_name as supplier_name', 'cx.client_name as customer_name', 'cm.client_name as company_name', 'u.first_name as created_by', 'po_hdr.created_at' )
        ->where('po_hdr.status', 'posted')
        ->leftJoin('suppliers as s','s.id','po_hdr.supplier_id')
        ->leftJoin('client_list as cx','cx.id','po_hdr.customer_id')
        ->leftJoin('client_list as cm','cm.id','po_hdr.company_id')
        ->leftJoin('store_list as sl','sl.id','po_hdr.store_id')
        ->leftJoin('users as u','u.id','po_hdr.created_by')
        ->get();
        return Datatables::of($data)->addIndexColumn()->make();
    }

    public function _encode(Request $request, $value) {
        // return response()->json([
        //     'success'  => true,
        //     'data'    => _encode($value)
        // ]);
        return _encode($value);
    }

    function getPlateNoList(Request $request) {
        $data =  \App\Models\PlateNoList::get();

    }

    function getPlateNo(Request $request) {
        $data =  \App\Models\PlateNoList::select('plate_no_list.vehicle_type','tt.vehicle_code','tt.vehicle_desc','plate_no_list.plate_no','plate_no_list.trucker_id','tl.trucker_name')
                ->leftJoin('trucker_list as tl','tl.id','plate_no_list.trucker_id')
                ->leftJoin('truck_type as tt','tt.vehicle_code','plate_no_list.vehicle_type')
                ->groupBy('plate_no_list.plate_no');
                if(isset($request->plate_no)){
                    $data->where('plate_no_list.plate_no',$request->plate_no);
                }
        $result =  $data->get();
        return response()->json([
            'success'  => true,
            'data'    => $result]);
    }

    function getLocation(Request $request) {
        $data = \App\Models\StorageLocationModel::where('warehouse_id', $request->warehouse_id)->where('is_enabled', 1)->get();
        return response()->json($data);
    }

    function getAllPostedDo(Request $request) {
        $data = \App\Models\DoHdr::select('do_hdr.id','do_no','order_no', 'order_date', 'del.client_name as deliver_to', 'cx.client_name as customer_name', 'cm.client_name as company_name', 'u.name as created_by', 'do_hdr.created_at' )
        ->where('do_hdr.status', 'posted')
        ->leftJoin('client_list as del','del.id','do_hdr.deliver_to_id')
        ->leftJoin('client_list as cx','cx.id','do_hdr.customer_id')
        ->leftJoin('client_list as cm','cm.id','do_hdr.company_id')
        ->leftJoin('store_list as sl','sl.id','do_hdr.store_id')
        ->leftJoin('users as u','u.id','do_hdr.created_by')
        ->get();
        return Datatables::of($data)->addIndexColumn()->make();
    }

    public function getAvailableStocks(Request $request) {
        $result = MasterfileModel::select(
                        'masterfiles.product_id',
                        'client_name',
                        'store_name',
                        'w.warehouse_name',
                        'product_code',
                        'product_name',
                        'sl.location',
                        'masterfiles.whse_uom',
                        'masterfiles.inv_uom',
                        'masterfiles.item_type',
                        'masterfiles.status',
                        'uw.code as uw_code',
                        'ui.code as ui_code',
                        DB::raw('SUM(inv_qty) as inv_qty'),
                        DB::raw('SUM(whse_qty) as whse_qty')
                    )
                    ->leftJoin('products as p','p.product_id','=','masterfiles.product_id')
                    ->leftJoin('storage_locations as sl','sl.storage_location_id','=','masterfiles.storage_location_id')
                    ->leftJoin('client_list as cl','cl.id','=','masterfiles.company_id')
                    ->leftJoin('store_list as s','s.id','=','masterfiles.store_id')
                    ->leftJoin('warehouses as w','w.id','=','masterfiles.warehouse_id')
                    ->leftJoin('uom as uw','uw.uom_id','=','masterfiles.whse_uom')
                    ->leftJoin('uom as ui','ui.uom_id','=','masterfiles.inv_uom')
                    ->where('masterfiles.status','X')
                    ->groupBy([
                        'client_name',
                        'store_name',
                        'product_name',
                        'masterfiles.item_type',
                        'masterfiles.status',
                        'masterfiles.whse_uom',
                        'masterfiles.inv_uom'
                    ])
                    ->having('inv_qty','>', 0)
                    ->orderBy('product_name','ASC')
                    ->orderBy('sl.location','ASC');
        if(isset($request->master_id)){
            $result->whereNotIN('masterfiles.masterfile_id', json_decode($request->master_id));
        }

        if($request->company_id > 0){
            $result->where('masterfiles.company_id', $request->company_id);
        }

        if(isset($request->warehouse_id)){
            $result->where('masterfiles.warehouse_id', $request->warehouse_id);
        }

        if($request->customer_id > 0){
            $result->where('masterfiles.customer_id', $request->customer_id);
        }

        if($request->store_id > 0){
            $result->where('masterfiles.store_id', $request->store_id);
        }

        if($request->item_type){
            $result->where('masterfiles.item_type', $request->item_type);
        }

        if(isset($request->product)){
            $keyword = '%'.$request->product.'%';
            $result->where(function($cond)use($keyword){
                $cond->where('product_code','like',$keyword)
                ->orwhere('product_name','like',$keyword)
                ->orwhere('product_sku','like',$keyword);
            });
        }
        $record = $result->get();
        return response()->json($record);
    }

    function getParticulars(Request $request) {
        $data = \App\Models\Particular::get();
        return response()->json($data);
    }

    function getAllPostedDispatch(Request $request) {
        $data = \App\Models\DispatchHdr::select('id','dispatch_no','dispatch_date')->where('status', 'posted');
            if(isset($request->dispatch_no)){
                $data->whereNotIN('dispatch_no', json_decode($request->dispatch_no));
            }
            if(isset($request->plate_no)){
                $data->where('plate_no', $request->plate_no);
            }
        $record = $data->get();
        return response()->json($record);
    }
}
