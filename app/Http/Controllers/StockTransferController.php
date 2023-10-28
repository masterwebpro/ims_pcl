<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\TransferHdr;
use App\Models\TransferDtl;
use App\Models\SeriesModel;
use App\Models\Warehouse;
use App\Models\AuditTrail;
use App\Models\MasterfileModel;
use App\Models\Products;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Throwable;

class StockTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $client_list = Client::where('is_enabled', '1')->get();

        $active_list = TransferHdr::select('*')
            ->where('status','open')->orderByDesc('created_at')
            ->paginate(20);
        
        $posted_list = TransferHdr::select('*')
            ->where('status','posted')->orderByDesc('created_at')
            ->paginate(20);

        return view('stock/transfer/index', [
            'active_list'=>$active_list, 
            'posted_list'=>$posted_list, 
            'client_list'=>$client_list, 
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $client_list = Client::where('is_enabled', '1')->get();

        return view('stock/transfer/create', [
            'client_list'=>$client_list, 
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

        $validator = Validator::make($request->all(), [
            'source_company'=>'required',
            'source_site'=>'required',
            'transaction_date'=>'required',
            'requested_by'=>'required',
            'product_id.*' => 'required',
            'source_warehouse.*' => 'required',
            'source_location.*' => 'required',
            'source_inv_qty.*' => 'required',
            'source_inv_uom.*' => 'required',
            'dest_warehouse.*' => 'required',
            'dest_location.*' => 'required',
            'dest_inv_qty.*' => 'required',
            'dest_inv_uom.*' => 'required',
            
        ], [
            'source_company'=>'Source Company is required',
            'company'=>'Company is required',
            'source_site'=>'Source Site is required',
            'transaction_date'=>'Trans date is required',
            'requested_by'=>'Requested By is required',
            'product_id.*' => 'Product is required',
            'source_warehouse.*' => 'Source warehouse id required',
            'source_location.*' => 'Source location is required',
            'source_inv_qty.*' => 'Source Inv qty is required',
            'source_inv_uom.*' => 'Source UOM is required',
            'dest_warehouse.*' => 'Dest warehouse is required',
            'dest_location.*' => 'Dest Location is required',
            'dest_inv_qty.*' => 'Dest Inv qtyrequired',
            'dest_inv_uom.*' => 'Dest Inv UOM required',
            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::connection()->beginTransaction();
        try {
            $ref_no = $request->ref_no;

            if($ref_no=='') {
                $ref_no = generateSeries('ST');

                $series[] = [
                    'series' => $ref_no,
                    'trans_type' => 'ST',
                    'created_at' => $this->current_datetime,
                    'updated_at' => $this->current_datetime,
                    'user_id' => Auth::user()->id,
                ];

                SeriesModel::insert($series);
            }

            $transfer = TransferHdr::updateOrCreate(['ref_no' => $ref_no], [
                'source_company_id'=>$request->source_company,
                'source_store_id'=>$request->source_site,
                'trans_date'=>$request->transaction_date,
                'requested_by'=>$request->requested_by,
                'dr_no'=>$request->dr_no,
                'remarks'=>$request->remarks,
                'status'=>$request->status,
                'created_by' =>Auth::user()->id,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);
            //save on dtl
            $dtl = array();

            $masterfile = [];
            $audit_trail=[];
            $_stockInMasterdata= [];
            $_stockOutMasterdata= [];
            $_isReservedInMasterdata=[];

            for($x=0; $x < count($request->product_id); $x++ ) {
                $dtl[] = array(
                    'ref_no'=>$ref_no,
                    'product_id'=>$request->product_id[$x],
                    'source_warehouse_id'=>$request->source_warehouse[$x],
                    'source_storage_location_id'=>$request->source_location[$x],
                    'source_item_type'=>$request->item_type[$x],
                    'source_inv_qty'=>$request->source_inv_qty[$x],
                    'source_inv_uom'=>$request->source_inv_uom[$x],
                    'dest_warehouse_id'=>$request->dest_warehouse[$x],
                    'dest_storage_location_id'=>$request->dest_location[$x],
                    'dest_item_type'=>$request->item_type[$x],
                    'dest_inv_qty'=>$request->dest_inv_qty[$x],
                    'dest_inv_uom'=>$request->dest_inv_uom[$x],
                    'rcv_dtl_id'=>$request->rcv_dtl_id[$x],
                );

                //get company ID
                $warehouse = _getWarehouseDtl($request->dest_warehouse[$x]);
                if(!$warehouse) {
                    return response()->json([
                        'success'  => false,
                        'message' => 'Unknown warehouse. Please try again.',
                    ]);
                }

                $product = Products::where('product_id',$request->product_id[$x])->first();

                // //add on the masterfile new location
                $masterfile[] = array(
                    'ref_no'=>$ref_no,
                    'customer_id'=>$product->customer_id,
                    'store_id'=>$warehouse->store_id,
                    'company_id'=>$warehouse->client_id,
                    'warehouse_id'=>$request->dest_warehouse[$x],
                    'storage_location_id'=>$request->dest_location[$x],
                    'product_id'=>$request->product_id[$x],
                    'item_type'=>$request->item_type[$x],
                    'trans_type'=>'ST',
                    'status'=>'X',
                    'inv_qty'=>$request->dest_inv_qty[$x],
                    'inv_uom'=>$request->dest_inv_uom[$x],
                    'whse_qty'=>$request->dest_inv_qty[$x],
                    'whse_uom'=>$request->dest_inv_uom[$x],
                    'created_at'=>date("Y-m-d H:i:s", strtotime("+5 sec")),
                    'updated_at'=>date("Y-m-d H:i:s", strtotime("+5 sec"))
                    
                );

                //deduct from old
                $masterfile[] = array(
                    'ref_no'=>$ref_no,
                    'customer_id'=>$product->customer_id,
                    'store_id'=>$request->source_site,
                    'company_id'=>$request->source_company,
                    'warehouse_id'=>$request->source_warehouse[$x],
                    'storage_location_id'=>$request->source_location[$x],
                    'trans_type'=>'ST',
                    'status'=>'X',
                    'product_id'=>$request->product_id[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>($request->source_inv_qty[$x] * -1),
                    'inv_uom'=>$request->source_inv_uom[$x],
                    'whse_qty'=>($request->source_inv_qty[$x] * -1),
                    'whse_uom'=>$request->source_inv_uom[$x],
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime
                );

                $_stockOutMasterdata[] = array(
                    'customer_id'=>$product->customer_id,
                    'company_id'=>$request->source_company,
                    'store_id'=>$request->source_site,
                    'warehouse_id'=>$request->source_warehouse[$x],
                    'product_id'=>$request->product_id[$x],
                    'storage_location_id'=>($request->source_location[$x] == 0 || $request->source_location[$x] == 'null') ? NULL : $request->source_location[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>($request->dest_inv_qty[$x]),
                    'inv_uom'=>$request->dest_inv_uom[$x],
                    'whse_qty'=>($request->dest_inv_qty[$x]),
                    'whse_uom'=>$request->dest_inv_uom[$x],
                    'rcv_dtl_id'=>$request->rcv_dtl_id[$x],
                );

                $_stockInMasterdata[] = array(
                    'customer_id'=>$product->customer_id,
                    'company_id'=>$warehouse->client_id,
                    'store_id'=>$warehouse->store_id,
                    'warehouse_id'=>$request->dest_warehouse[$x],
                    'product_id'=>$request->product_id[$x],
                    'storage_location_id'=>$request->dest_location[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>($request->dest_inv_qty[$x]),
                    'inv_uom'=>$request->dest_inv_uom[$x],
                    'whse_qty'=>($request->dest_inv_qty[$x]),
                    'whse_uom'=>$request->dest_inv_uom[$x],
                    'rcv_dtl_id'=>$request->rcv_dtl_id[$x],
                );
            }

            $result= TransferDtl::where('ref_no',$ref_no)->delete();
            TransferDtl::insert($dtl);

            $audit_trail[] = [
                'control_no' => $ref_no,
                'type' => 'ST',
                'status' => $request->status,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
                'user_id' => Auth::user()->id,
                'data' => null
            ];

            if($request->status == 'posted') {
                //add on the masterfile
                MasterfileModel::insert($masterfile);
                
                $audit_trail[] = [
                    'control_no' => $ref_no,
                    'type' => 'masterfile',
                    'status' => $request->status,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                    'user_id' => Auth::user()->id,
                    'data' => null
                ];

                _stockInMasterData($_stockInMasterdata);
                _stockOutMasterData($_stockOutMasterdata);
            } 

            AuditTrail::insert($audit_trail);

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $transfer,
                'id'=> _encode($transfer->id)
            ]);

        }
        catch(\Throwable $e)
        {
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $transfer_hdr = TransferHdr::select('*')->where('id', _decode($id))->first();
        $client_list = Client::where('is_enabled', '1')->get();

        $transfer_dtl = TransferDtl::select('*')->where('ref_no', $transfer_hdr->ref_no)->get();

        $warehouses = Warehouse::where('store_id',$transfer_hdr->source_store_id)->get();

        // $location = (new SettingsController)->getLocationPerWarehouse($mv_hdr->warehouse_id);

        return view('stock/transfer/show', [
            'client_list'=>$client_list, 
            'transfer_hdr'=> $transfer_hdr,
            'transfer_dtl'=> $transfer_dtl,
            'warehouses'=>$warehouses,
            // 'location'=>$location,
        ]);
    }

    public function edit($id)
    {
        $transfer_hdr = TransferHdr::select('*')->where('id', _decode($id))->first();
        $client_list = Client::where('is_enabled', '1')->get();

        $transfer_dtl = TransferDtl::select('*')->where('ref_no', $transfer_hdr->ref_no)->get();

        $warehouses = Warehouse::where('store_id',$transfer_hdr->source_store_id)->get();

        // $location = (new SettingsController)->getLocationPerWarehouse($mv_hdr->warehouse_id);

        return view('stock/transfer/edit', [
            'client_list'=>$client_list, 
            'transfer_hdr'=> $transfer_hdr,
            'transfer_dtl'=> $transfer_dtl,
            'warehouses'=>$warehouses,
            // 'location'=>$location,
        ]);
    }

    public function destroy(Request $request)
    {
        DB::connection()->beginTransaction();
        try 
        {
            $ref_no = $request->ref_no;
            if($ref_no) {
                $rcv = TransferHdr::where('ref_no', $ref_no)->delete();
                $rcv_dtl = TransferDtl::where('ref_no', $ref_no)->delete();
               
                $audit_trail[] = [
                    'control_no' => $ref_no,
                    'type' => 'ST',
                    'status' => 'deleted',
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                    'user_id' => Auth::user()->id,
                    'data' => 'deleted'
                ];

                AuditTrail::insert($audit_trail);

                DB::connection()->commit();

                return response()->json([
                    'success'  => true,
                    'message' => 'deleted successfully!',
                    'data'    => $rcv
                ]);

            } else {
                return response()->json([
                    'success'  => false,
                    'message' => 'Unable to process request. Please try again.',
                    'data'    => $e->getMessage()
                ]);
            }
        }
        catch(\Throwable $e)
        {
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        }  
    }

    public function unpost(Request $request) {
        
        DB::connection()->beginTransaction();
        try 
        {

            $ref_no = $request->ref_no;
            $transfer_dtl = TransferDtl::where('ref_no', $ref_no)->get();
            $transfer_hdr = TransferHdr::where('ref_no', $ref_no)->first();

            $masterfile = [];
            $_stockOutMasterdata = [];
            $_stockInMasterdata = [];

            foreach($transfer_dtl as $transfer) {
            
                $product = Products::where('product_id',$transfer->product_id)->first();

                //get company ID
                $warehouse = _getWarehouseDtl($transfer->dest_warehouse_id);

                // dd($warehouse);
                if(!$warehouse) {
                    return response()->json([
                        'success'  => false,
                        'message' => 'Unknown warehouse. Please try again.',
                    ]);
                }

                // //add on the masterfile new location
                $masterfile[] = array(
                    'ref_no'=>$ref_no,
                    'customer_id'=>$product->customer_id,
                
                    'store_id'=>$transfer_hdr->source_store_id,
                    'company_id'=>$transfer_hdr->source_company_id,

                    'warehouse_id'=>$transfer->source_warehouse_id,
                    'storage_location_id'=>$transfer->source_storage_location_id,
                    'product_id'=>$transfer->product_id,
                    'item_type'=>$transfer->source_item_type,
                    'trans_type'=>'ST',
                    'status'=>'X',
                    'inv_qty'=>$transfer->source_inv_qty,
                    'inv_uom'=>$transfer->source_inv_uom,
                    'whse_qty'=>$transfer->source_inv_qty,
                    'whse_uom'=>$transfer->source_inv_uom,
                    'created_at'=>date("Y-m-d H:i:s", strtotime("+5 sec")),
                    'updated_at'=>date("Y-m-d H:i:s", strtotime("+5 sec"))
                    
                );


                //deduct from old
                $masterfile[] = array(
                    'ref_no'=>$ref_no,
                    'customer_id'=>$product->customer_id,
                    'store_id'=>$warehouse->store_id,
                    'company_id'=>$warehouse->client_id,
                    'warehouse_id'=>$transfer->dest_warehouse_id,
                    'storage_location_id'=>$transfer->dest_storage_location_id,
                    'trans_type'=>'ST',
                    'status'=>'X',
                    'product_id'=>$transfer->product_id,
                    'item_type'=>$transfer->dest_item_type,
                    'inv_qty'=>($transfer->dest_inv_qty * -1),
                    'inv_uom'=>$transfer->dest_inv_uom,
                    'whse_qty'=>($transfer->dest_inv_qty * -1),
                    'whse_uom'=>$transfer->dest_inv_uom,
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime
                );

            
                //deduct the qty to dest
                $_stockOutMasterdata[] = array(
                    'company_id'=>$warehouse->client_id,
                    'customer_id'=>$product->customer_id,
                    'store_id'=>$warehouse->store_id,
                    'warehouse_id'=>$transfer->dest_warehouse_id,
                    'product_id'=>$transfer->product_id,
                    'storage_location_id'=>($transfer->dest_storage_location_id == 0 || $transfer->dest_storage_location_id == 'null') ? NULL : $transfer->dest_storage_location_id,
                    'item_type'=>$transfer->dest_item_type,
                    'inv_qty'=>($transfer->dest_inv_qty),
                    'inv_uom'=>$transfer->dest_inv_uom,
                    'whse_qty'=>($transfer->dest_inv_qty),
                    'whse_uom'=>$transfer->dest_inv_uom,
                    'rcv_dtl_id'=>$transfer->rcv_dtl_id,
                );

                // add the qty to source
                $_stockInMasterdata[] = array(
                    'company_id'=>$transfer_hdr->source_company_id,
                    'store_id'=>$transfer_hdr->source_store_id,
                    'customer_id'=>$product->customer_id,
                    'warehouse_id'=>$transfer->source_warehouse_id,
                    'product_id'=>$transfer->product_id,
                    'storage_location_id'=>$transfer->source_storage_location_id,
                    'item_type'=>$transfer->source_item_type,
                    'inv_qty'=>$transfer->source_inv_qty,
                    'inv_uom'=>$request->source_inv_uom,
                    'whse_qty'=>$transfer->source_inv_qty,
                    'whse_uom'=>$request->source_inv_uom,
                    'rcv_dtl_id'=>$transfer->rcv_dtl_id,
                );
            }

            $audit_trail[] = [
                'control_no' => $ref_no,
                'type' => 'ST',
                'status' => 'open',
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
                'user_id' => Auth::user()->id,
                'data' => null
            ];

            MasterfileModel::insert($masterfile);
                
            $audit_trail[] = [
                'control_no' => $ref_no,
                'type' => 'masterfile',
                'status' => 'open',
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
                'user_id' => Auth::user()->id,
                'data' => null
            ];

            _stockInMasterData($_stockInMasterdata);
            _stockOutMasterData($_stockOutMasterdata);
        

            AuditTrail::insert($audit_trail);

            //update header to open
            TransferHdr::where('ref_no', $ref_no)->update(['status'=>'open']);
            
            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $transfer,
                'id'=> _encode($transfer->id)
            ]);
        }
        catch(\Throwable $e)
        {
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        } 

    }
}
     