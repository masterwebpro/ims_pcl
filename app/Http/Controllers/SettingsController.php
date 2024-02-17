<?php

namespace App\Http\Controllers;

use App\Models\AvailableItem;
use App\Models\MasterdataModel;
use App\Models\MasterfileModel;

use App\Models\SeriesModel;
use App\Models\StorageLocationModel;

use App\Models\RcvHdr;
use App\Models\RcvDtl;
use App\Models\Products;
use App\Models\AuditTrail;

use App\Imports\ProductUpload;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UOM;
use App\Models\WdDtl;
use App\Models\WdHdr;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use DataTables;

class SettingsController extends Controller
{
    public function getStoreByClient(Request $request) {
        $data = \App\Models\Store::where('client_id', $request->client_id)->where('is_enabled', 1)->get();
        return response()->json($data);
    }


    function getProducts(Request $request) {
        $products = \App\Models\Products::select('product_id','sap_code','product_sku','product_code','product_name')
        ->where('is_enabled', $request->is_enabled)
        ->where('customer_id', $request->customer_id)
        ->get();
        // if($request->supplier_id) {
        //     $products->where('supplier_id', $request->supplier_id);
        // }
        // if($request->customer_id) {
        //     $products->where('customer_id', $request->customer_id);
        // }
        // if($request->is_enabled) {
        //     $products->where('is_enabled', $request->is_enabled);
        // }
        // $products->get();
        return Datatables::of($products)->addIndexColumn()->make();
    }

    function getProductBySupplier(Request $request, $supplier_id) {
        $products = \App\Models\Products::where('supplier_id', $supplier_id)->get();
        return Datatables::of($products)->addIndexColumn()->make(true);
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
        $result = \App\Models\MasterdataModel::select('p.product_id','p.product_code','p.product_name','masterdata.item_type', 'masterdata.rcv_dtl_id', 'sl.storage_location_id as old_location_id', DB::raw("case when sl.location is null or sl.location = '' then 'RA' else  sl.location end as old_location"), 'iu.code as i_code',  'iu.uom_id as i_uom_id', 'wu.code as w_code', 'wu.uom_id as w_uom_id','sl.rack as rack', 'sl.level as layer', 'masterdata.inv_qty', 'masterdata.whse_qty')
                ->where('masterdata.warehouse_id', $request->warehouse_id)
                ->where('masterdata.inv_qty', '>', 0)
                ->where('masterdata.whse_qty', '>', 0)
                ->leftJoin('products as p','p.product_id','masterdata.product_id')
                ->leftJoin('storage_locations as sl','sl.storage_location_id','masterdata.storage_location_id')
                ->leftJoin('uom as wu','wu.uom_id','masterdata.whse_uom')
                ->leftJoin('uom as iu','iu.uom_id','masterdata.inv_uom')
                ->leftJoin('rcv_dtl as rd','rd.id','masterdata.rcv_dtl_id')
                ->leftJoin('rcv_hdr as rh','rh.rcv_no','rd.rcv_no');

        if($request->location) {
            if(isset($request->storage_id)) {
                if($request->storage_id) {
                    $result->whereIn('masterdata.storage_location_id', explode(",",$request->storage_id));
                }
            }

            if($request->location == 'ra') {
                $result->where('masterdata.storage_location_id', null);
            } else {
                $result->where('masterdata.storage_location_id', $request->location);
            }
        }

        if($request->client_id)
            $result->where('masterdata.company_id', $request->client_id);

        if($request->store_id)
            $result->where('masterdata.store_id', $request->store_id);

        if($request->rcv_no) {
            $result->where('rh.rcv_no', $request->rcv_no);
        }

        if($request->product_name) {
            $result->where('p.product_name', 'like', '%'.$request->product_name.'%');
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
                        'sap_code',
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
                ->orwhere('sap_code','like',$keyword)
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
            ->orWhere('sap_code', $item_code)
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

    function withdrawalDetails(Request $request){
        $result = WdDtl::select('wd_dtl.id',
                    'wd_dtl.wd_no',
                    'cl.client_name',
                    'del.client_name as deliver_to',
                    DB::raw('sum(wd_dtl.inv_qty - wd_dtl.dispatch_qty) as inv_qty'),
                    'wd_dtl.inv_uom',
                    'wd_hdr.order_no',
                    'wd_hdr.order_date',
                    'wd_hdr.po_num',
                    'wd_hdr.sales_invoice',
                    'wd_hdr.dr_no',
                    'p.product_code',
                    'p.sap_code',
                    'p.product_name',
                    'ui.code as ui_code',
                    )
                    ->leftJoin('products as p','p.product_id','=','wd_dtl.product_id')
                    ->leftJoin('uom as ui','ui.uom_id','=','wd_dtl.inv_uom')
                    ->leftJoin('wd_hdr','wd_hdr.wd_no','wd_dtl.wd_no')
                    ->leftJoin('client_list as cl','cl.id','wd_hdr.customer_id')
                    ->leftJoin('client_list as del','del.id','wd_hdr.deliver_to_id')
                    ->where('wd_hdr.status','posted')
                    // ->whereNull('wd_hdr.dispatch_no')
                    ->groupBy('wd_dtl.id')
                    ->having('inv_qty','>',0)
                    ->orderBy('wd_hdr.order_date','ASC');
                    if(isset($request->keyword)){
                        $search = "%".$request->keyword."%";
                        $result->where(function($cond)use($search){
                            $cond->where('wd_dtl.wd_no','like',$search)
                            ->orwhere('wd_hdr.order_no','like',$search)
                            ->orwhere('wd_hdr.po_num','like',$search)
                            ->orwhere('wd_hdr.sales_invoice','like',$search)
                            ->orwhere('wd_hdr.dr_no','like',$search)
                            ->orwhere('p.product_code','like',$search)
                            ->orwhere('p.product_name','like',$search)
                            ->orwhere('ui.code','like',$search);
                        });
                    }
                    if($request->status)
                        $result->where('wd_hdr.status', $request->status);
                    if(isset($request->wddtl_id))
                        $result->whereNotIN('wd_dtl.id', json_decode($request->wddtl_id));

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
        $result = MasterdataModel::select(
                        'masterdata.id as master_id',
                        'masterdata.rcv_dtl_id',
                        'masterdata.product_id',
                        'client_name',
                        'store_name',
                        'w.warehouse_name',
                        'product_code',
                        'sap_code',
                        'product_name',
                        'sl.location',
                        'masterdata.whse_uom',
                        'masterdata.inv_uom',
                        'masterdata.item_type',
                        'uw.code as uw_code',
                        'ui.code as ui_code',
                        DB::raw('sum(masterdata.inv_qty - masterdata.reserve_qty) as inv_qty'),
                        DB::raw('sum(masterdata.whse_qty - masterdata.reserve_qty) as whse_qty'),
                        'rd.lot_no',
                        'rd.expiry_date',
                        'rh.date_received as received_date',
                        'rd.manufacture_date'
                    )
                    ->leftJoin('rcv_dtl as rd','rd.id','=','masterdata.rcv_dtl_id')
                    ->leftJoin('rcv_hdr as rh','rh.rcv_no','=','rd.rcv_no')
                    ->leftJoin('products as p','p.product_id','=','masterdata.product_id')
                    ->leftJoin('storage_locations as sl','sl.storage_location_id','=','masterdata.storage_location_id')
                    ->leftJoin('client_list as cl','cl.id','=','masterdata.company_id')
                    ->leftJoin('store_list as s','s.id','=','masterdata.store_id')
                    ->leftJoin('warehouses as w','w.id','=','masterdata.warehouse_id')
                    ->leftJoin('uom as uw','uw.uom_id','=','masterdata.whse_uom')
                    ->leftJoin('uom as ui','ui.uom_id','=','masterdata.inv_uom')
                    ->havingRaw('sum(masterdata.inv_qty - masterdata.reserve_qty) > 0')
                    ->groupBy('masterdata.id')
                    ->orderBy('rh.date_received','ASC')
                    ->orderBy('product_name','ASC')
                    ->orderBy('sl.location','ASC');
        if(isset($request->master_id)){
            $result->whereNotIN('masterdata.id', json_decode($request->master_id));
        }

        if($request->company_id > 0){
            $result->where('masterdata.company_id', $request->company_id);
        }

        if(isset($request->warehouse_id)){
            $result->where('masterdata.warehouse_id', $request->warehouse_id);
        }

        if($request->customer_id > 0){
            $result->where('masterdata.customer_id', $request->customer_id);
        }

        if($request->store_id > 0){
            $result->where('masterdata.store_id', $request->store_id);
        }

        if($request->item_type){
            $result->where('masterdata.item_type', $request->item_type);
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
        if($record){
            foreach($record as &$rec){
                $rec->lot_no =  $rec->lot_no != null ? $rec->lot_no : "";
                $rec->location =  $rec->location != null ? $rec->location : "";
                $rec->expiry_date =  ($rec->expiry_date != null &&  $rec->expiry_date != '0000-00-00 00:00:00') ? date('Y/m/d',strtotime($rec->expiry_date)) : "";
                $rec->received_date =  ($rec->received_date != null  &&  $rec->received_date != '0000-00-00 00:00:00') ? date('Y/m/d',strtotime($rec->received_date)) : "";
                $rec->manufacture_date =  ($rec->manufacture_date != null   &&  $rec->manufacture_date != '0000-00-00 00:00:00') ? date('Y/m/d',strtotime($rec->manufacture_date)) : "";
            }
        }
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

    function uploadBeginningInv() {
        return view('master/upload');
    }

    function parseBeginningInv(Request $request) {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|mimes:xlsx,xls'
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
                    $valid_header = ['company_id', 'store_id','warehouse_id', 'product_code','location','item_type','whse_qty','whse_uom','inv_qty','inv_uom'];
                    foreach($header as $val){
                        if(!in_array($val,$valid_header)){
                            return response()->json(['status' => false, 'message' => 'File upload failed, Invalid header format. Please download the correct template.']);
                        }
                    }

                    //generate RCV and Rcv DTL
                    $rcv_no = generateSeries('RCV');

                    $series[] = [
                        'series' => $rcv_no,
                        'trans_type' => 'RCV',
                        'created_at' => $this->current_datetime,
                        'updated_at' => $this->current_datetime,
                        'user_id' => Auth::user()->id,
                    ];


                    $masterfile = [];

                    // dd($prod_data);
                    if(count($prod_data) > 1){
                        $xdata = [];
                        $rows = 2;
                        for($i=1;$i< count($prod_data);$i++){
                            $company_id = $prod_data[$i][0];
                            $store_id = $prod_data[$i][1];
                            $warehouse_id = $prod_data[$i][2];
                            $product_code = $prod_data[$i][3];
                            $location = $prod_data[$i][4];
                            $item_type = $prod_data[$i][5];
                            $whse_qty = $prod_data[$i][6];
                            $whse_uom = $prod_data[$i][7];
                            $inv_qty = $prod_data[$i][8];
                            $inv_uom = $prod_data[$i][9];

                            if(isset($prod_data[$i][0]) && isset($prod_data[$i][1]) && isset($prod_data[$i][2]) && isset($prod_data[$i][3]) && isset($prod_data[$i][4]) && isset($prod_data[$i][5])){
                                $storage  = StorageLocationModel::select('storage_location_id as id')->where('location',$location)->first();

                                $product  = Products::select('product_id')->where('product_code',$product_code)->first();

                                if($product)
                                    $product_id = $product->product_id;
                                else
                                    return response()->json(['status' => false, 'message' => "File upload failed, Row no {$rows} product brand not found."]);


                                if($storage) {
                                    $storage_location_id = $storage->id;
                                } else {
                                    if($location == 'RA') {
                                        $storage_location_id = NULL;
                                    } else {
                                        return response()->json(['status' => false, 'message' => "File upload failed, Row no {$rows} storage location id brand not found."]);
                                    }
                                }

                                $w_uom  = UOM::select('uom_id as id')->where('code',$whse_uom)->first();

                                $i_uom  = UOM::select('uom_id as id')->where('code',$inv_uom)->first();

                                $item = array(
                                    'rcv_no'=>$rcv_no,
                                    'product_id'=>$product_id,
                                    'item_type'=>$item_type,
                                    'inv_qty'=>$inv_qty,
                                    'inv_uom'=>$i_uom->id,
                                    'whse_qty'=>$whse_qty,
                                    'whse_uom'=>$w_uom->id,
                                    'created_at'=>$this->current_datetime,
                                    'updated_at'=>$this->current_datetime,
                                );

                                $rcv_dtl = RcvDtl::create($item);

                                //add on the masterfile
                                $masterfile[] = array(
                                    'ref_no'=>$rcv_no,
                                    'status'=>'X',
                                    'trans_type'=>'RV',
                                    'product_id'=>$product_id,
                                    'item_type'=>$item_type,
                                    'inv_qty'=>$inv_qty,
                                    'inv_uom'=>$i_uom->id,
                                    'whse_qty'=>$whse_qty,
                                    'whse_uom'=>$w_uom->id,
                                    'store_id'=>$store_id,
                                    'company_id'=>$company_id,
                                    'warehouse_id'=>$warehouse_id,
                                    'storage_location_id'=>$storage_location_id,
                                    'ref1_no'=>$rcv_no,
                                    'ref1_type'=>'rcv',
                                    'created_at'=>$this->current_datetime,
                                    'updated_at'=>$this->current_datetime,
                                );

                                $masterdata[] = array(
                                    'customer_id'=>0,
                                    'company_id'=>$company_id,
                                    'store_id'=>$store_id,
                                    'warehouse_id'=>$warehouse_id,
                                    'product_id'=>$product_id,
                                    'storage_location_id'=>$storage_location_id,
                                    'item_type'=>$item_type,
                                    'inv_qty'=>$inv_qty,
                                    'inv_uom'=>$i_uom->id,
                                    'whse_qty'=>$whse_qty,
                                    'whse_uom'=>$w_uom->id,
                                    'rcv_dtl_id'=>$rcv_dtl->id,
                                );

                            }else{
                                return response()->json(['status' => false, 'message' => "File upload failed, Row no {$rows} all header must have value."]);
                            }
                            $rows++;
                        }
                        $audit_trail[] = [
                            'control_no' => $rcv_no,
                            'type' => 'RCV',
                            'status' => 'X',
                            'created_at' => date('y-m-d h:i:s'),
                            'updated_at' => date('y-m-d h:i:s'),
                            'user_id' => Auth::user()->id,
                        ];

                        MasterfileModel::insert($masterfile);

                        _stockInMasterData($masterdata);

                        AuditTrail::insert($audit_trail);
                        SeriesModel::insert($series);

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
        } else{
            return response()->json(['message' => 'File upload failed'], 400);
        }
    }

    public function getAvailableItems(Request $request) {
        $result = MasterdataModel::select(
                        'masterdata.product_id',
                        'product_code',
                        'sap_code',
                        'product_name',
                        'masterdata.whse_uom',
                        'masterdata.inv_uom',
                        'masterdata.item_type',
                        'uw.code as uw_code',
                        'ui.code as ui_code',
                        DB::raw('sum(masterdata.inv_qty - masterdata.reserve_qty) as inv_qty'),
                        DB::raw('sum(masterdata.whse_qty - masterdata.reserve_qty) as whse_qty'),
                    )
                    ->leftJoin('products as p','p.product_id','=','masterdata.product_id')
                    ->leftJoin('uom as uw','uw.uom_id','=','masterdata.whse_uom')
                    ->leftJoin('uom as ui','ui.uom_id','=','masterdata.inv_uom')
                    ->groupBy('masterdata.product_id')
                    ->orderBy('product_name','ASC');
        if(isset($request->master_id)){
            $result->whereNotIN('masterdata.id', json_decode($request->master_id));
        }

        if($request->company_id > 0){
            $result->where('masterdata.company_id', $request->company_id);
        }

        if(isset($request->warehouse_id)){
            $result->where('masterdata.warehouse_id', $request->warehouse_id);
        }

        if($request->customer_id > 0){
            $result->where('masterdata.customer_id', $request->customer_id);
        }

        if($request->store_id > 0){
            $result->where('masterdata.store_id', $request->store_id);
        }

        if($request->item_type){
            $result->where('masterdata.item_type', $request->item_type);
        }

        if(isset($request->product)){
            $keyword = '%'.$request->product.'%';
            $result->where(function($cond)use($keyword){
                $cond->where('product_code','like',$keyword)
                ->orwhere('sap_code','like',$keyword)
                ->orwhere('product_name','like',$keyword)
                ->orwhere('product_sku','like',$keyword);
            });
        }
        $record = $result->get();
        return response()->json($record);
    }

}
