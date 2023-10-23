<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\MasterData;
use App\Models\MasterdataModel;
use App\Models\MasterfileModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterDataController extends Controller
{
    public function index(Request $request){
        $dateRangeParts = explode(" to ", $request->date);
        $startDate = isset($dateRangeParts[0]) ? $dateRangeParts[0] : "";
        $endDate = isset($dateRangeParts[1]) ? $dateRangeParts[1] : "";

        $master_list = MasterdataModel::select(
                        'masterdata.inv_qty',
                        'masterdata.whse_qty',
                        'masterdata.reserve_qty',
                        'rh.date_received as received_date',
                        'rd.lot_no',
                        'rd.expiry_date',
                        'rd.manufacture_date',
                        'products.product_code',
                        'products.product_name',
                        's.store_name',
                        'wh.warehouse_name',
                        'uw.code as uw_code',
                        'ui.code as ui_code',
                        'sl.location'
                        // 'com.client_name as company_name',
                        // 'c.client_name',
                        )
                        ->leftJoin('rcv_dtl as rd', 'rd.id', '=', 'masterdata.rcv_dtl_id')
                        ->leftJoin('rcv_hdr as rh', 'rh.rcv_no', '=', 'rd.rcv_no')
                        ->leftJoin('products', 'products.product_id', '=', 'masterdata.product_id')
                        ->leftJoin('uom as uw','uw.uom_id','=','masterdata.whse_uom')
                        ->leftJoin('uom as ui','ui.uom_id','=','masterdata.inv_uom')
                        ->leftJoin('storage_locations as sl','sl.storage_location_id','=','masterdata.storage_location_id')
                        ->leftJoin('store_list as s', 's.id', '=', 'masterdata.store_id')
                        ->leftJoin('warehouses as wh', 'wh.id', '=', 'masterdata.warehouse_id');
                        // ->leftJoin('client_list as c', 'c.id', '=', 'masterdata.customer_id')
                        // ->leftJoin('client_list as com', 'com.id', '=', 'masterdata.company_id')
                        if ($request->keyword) {
                            $master_list->where(function($query)use($request){
                                $query->where('products.product_code', $request->keyword);
                                $query->orWhere('products.product_name', $request->keyword);
                                $query->orWhere('masterdata.lot_no', $request->keyword);
                                $query->orWhere('masterdata.item_type', $request->keyword);
                            });
                        }
                        if ($request->filter_date) {
                            if($request->filter_date == 'received_date') {
                                $master_list->whereBetween('masterdata.received_date', [$request->date." 00:00:00", $request->date." 23:59:59"]);
                            }

                            if($request->filter_date == 'received_date' && $startDate && $endDate)
                            {
                                $master_list->whereBetween('masterdata.received_date',[$startDate,$endDate]);
                            }
                        }

                        if ($request->customer) {
                            $master_list->where('masterdata.customer_id', $request->customer);
                        }

                        if ($request->company) {
                            $master_list->where('masterdata.company_id', $request->company);
                        }
        $result = $master_list->paginate(20);
        $deliver_list = Client::where('client_type','T')->get();
        $client_list = Client::where('is_enabled', '1')->get();
        return view('master/index', ['master_list'=>$result, 'deliver_list'=> $deliver_list, 'client_list'=> $client_list, 'request'=> $request]);
    }

    public function store(){
        $result = MasterfileModel::select(
            'masterfiles.product_id',
            'masterfiles.company_id',
            'masterfiles.customer_id',
            'masterfiles.store_id',
            'masterfiles.warehouse_id',
            'masterfiles.storage_location_id',
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
            DB::raw('SUM(masterfiles.inv_qty) as inv_qty'),
            DB::raw('SUM(masterfiles.whse_qty) as whse_qty')
        )
        ->leftJoin('products as p','p.product_id','=','masterfiles.product_id')
        ->leftJoin('storage_locations as sl','sl.storage_location_id','=','masterfiles.storage_location_id')
        ->leftJoin('client_list as cl','cl.id','=','masterfiles.company_id')
        ->leftJoin('store_list as s','s.id','=','masterfiles.store_id')
        ->leftJoin('warehouses as w','w.id','=','masterfiles.warehouse_id')
        ->leftJoin('rcv_dtl as rd','rd.rcv_no','=','masterfiles.ref_no')
        ->leftJoin('rcv_hdr as rh','rh.rcv_no','=','masterfiles.ref_no')
        ->leftJoin('uom as uw','uw.uom_id','=','masterfiles.whse_uom')
        ->leftJoin('uom as ui','ui.uom_id','=','masterfiles.inv_uom')
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
        ->orderBy('sl.location','ASC')
        ->get();
        DB::beginTransaction();
        try {
            foreach($result as $res){
                MasterdataModel::updateOrCreate([
                    'customer_id' => $res->customer_id,
                    'company_id' => $res->company_id,
                    'store_id' => $res->store_id,
                    'warehouse_id' => $res->warehouse_id,
                    'product_id' => $res->product_id,
                    'storage_location_id' => $res->storage_location_id,
                    'item_type' => $res->item_type,
                    'expiry_date' => $res->expiry_date,
                    'lot_no' => $res->lot_no,
                    'received_date' => $res->received_date
                ],[
                    'customer_id' => $res->customer_id,
                    'company_id' => $res->company_id,
                    'store_id' => $res->store_id,
                    'warehouse_id' => $res->warehouse_id,
                    'product_id' => $res->product_id,
                    'storage_location_id' => $res->storage_location_id,
                    'item_type' => $res->item_type,
                    'inv_qty' => $res->inv_qty,
                    'inv_uom' => $res->inv_uom,
                    'whse_qty' => $res->whse_qty,
                    'whse_uom' => $res->whse_uom,
                    'expiry_date' => $res->expiry_date,
                    'lot_no' => $res->lot_no,
                    'received_date' => $res->received_date
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
