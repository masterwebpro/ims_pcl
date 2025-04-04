<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\MvHdr;
use App\Models\MvDtl;
use App\Models\SeriesModel;
use App\Models\AuditTrail;
use App\Models\MasterfileModel;
use App\Models\Products;

use App\Http\Controllers\SettingsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Throwable;


class StockMovementController extends Controller
{
    public function index()
    {
        $client_list = Client::where('is_enabled', '1')->get();

        $active_list = MvHdr::select('*')
            ->where('status','open')->orderByDesc('created_at')
            ->paginate(20);

        $posted_list = MvHdr::select('*')
            ->where('status','posted')->orderByDesc('created_at')
            ->paginate(20);

        return view('stock/movement/index', [
            'active_list'=>$active_list,
            'posted_list'=>$posted_list,
            'client_list'=>$client_list,
        ]);
    }

    public function create(Request $request )
    {
        $client_list = Client::where('is_enabled', '1')->get();

        $session = $request->session()->get('mv_data');

        if(empty($session)) {
            return to_route('movement.index');
        }

        $warehouse = _decode($session['warehouse']);
        $store = _decode($session['store']);
        $company = _decode($session['company']);

        //get rack and layer
        $locations = (new SettingsController)->getLocationWarehouse($warehouse);

        return view('stock/movement/create', [
            'client_list'=>$client_list,
            'warehouse_id' =>$warehouse? $warehouse : '',
            'store_id'=> $store? $store : '',
            'company_id'=> $company? $company : '',
            'locations'=> $locations,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id'=>'required',
            'company_id'=>'required',
            'store_id'=>'required',
            'item_type.*'=>'required',
            'product_id.*' => 'required',
            'old_location.*' => 'required',
            'old_inv_qty.*' => 'required',
            'old_inv_uom.*' => 'required',
            'new_location.*' => 'required',
            'new_inv_qty.*' => 'required|required_with:old_inv_qty.*|numeric|min:1|lte:old_inv_qty.*',
            'new_inv_uom.*' => 'required',

        ], [
            'warehouse'=>'Warehouse is required',
            'company'=>'Company is required',
            'store'=>'Store is required',
            'product_id.*' => 'Product is required',
            'item_type.*'=>'Item type is required',
            'old_location.*' => 'Old Location is required',
            'old_inv_uom.*' => 'Inv Qty is required',
            'new_location.*' => 'New Location is required',
            'new_inv_qty.*' => 'Insufficient Qty',
            'new_inv_uom.*' => 'Inv UOM required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $transfer_qty = 0;
        $original_qty = 0;

        for($x=0; $x < count($request->product_id); $x++ ) {
            $key = $request->product_id[$x]."|".$request->rcv_dtl_id[$x]."|".$request->old_location[$x]."|".$request->master_id[$x];

            $product_info = Products::where('product_id',$request->product_id[$x])->first();

            if(!isset($data[$key])) {
                $data[$key]['transfer_qty']  = $request->new_inv_qty[$x];
                $data[$key]['original_qty'] = $request->old_inv_qty[$x];
                $data[$key]['sap_code'] = $product_info->sap_code;
                $data[$key]['source_location'] = $request->old_location[$x];
            } else {
                $data[$key]['transfer_qty']  += $request->new_inv_qty[$x];
            }
        }
        //validation
        $has_error = [];
        foreach($data as $idx=>$item) {
            if($item['original_qty'] < $item['transfer_qty']) {
                $has_error[] = "Insufficeint QTY: ". $item['sap_code']." in location ". getStorageLocation($item['source_location']). ". (Actual Qty: ".$item['original_qty'].", Transfer Qty: ".$item['transfer_qty'].")" ;
            }

        }

        if($has_error) {
            DB::connection()->rollBack();
            return response()->json([
                'success'  => false,
                'message' => 'Insufficient Qty!',
                'error_msg'=> $has_error
            ]);
        }

        DB::connection()->beginTransaction();
        try {
            $ref_no = $request->ref_no;

            if($ref_no=='') {
                $ref_no = $this->generateSMNo();

                $series[] = [
                    'series' => $ref_no,
                    'trans_type' => 'SM',
                    'created_at' => $this->current_datetime,
                    'updated_at' => $this->current_datetime,
                    'user_id' => Auth::user()->id,
                ];

                SeriesModel::insert($series);
            }
            //updateOrCreate
            $sm = MvHdr::updateOrCreate(['ref_no' => $ref_no], [
                'store_id'=>$request->store_id,
                'company_id'=>$request->company_id,
                'warehouse_id'=>$request->warehouse_id,
                'status'=>$request->status,
                'remarks'=>$request->remarks,
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
                    'old_storage_location_id'=>$request->old_location[$x],
                    'old_item_type'=>$request->item_type[$x],
                    'old_inv_qty'=>$request->old_inv_qty[$x],
                    'old_inv_uom'=>$request->old_inv_uom[$x],
                    'old_whse_qty'=>$request->old_inv_qty[$x],
                    'old_whse_uom'=>$request->old_inv_uom[$x],
                    'new_storage_location_id'=>$request->new_location[$x],
                    'new_item_type'=>$request->item_type[$x],
                    'new_inv_qty'=>$request->new_inv_qty[$x],
                    'new_inv_uom'=>$request->new_inv_uom[$x],
                    'new_whse_qty'=>$request->new_inv_qty[$x],
                    'new_whse_uom'=>$request->new_inv_uom[$x],
                    'rcv_dtl_id'=>$request->rcv_dtl_id[$x],
                    'master_id'=>$request->master_id[$x],
                    // 'ref1_no'=>$request->ref1_no[$x],
                    // 'ref1_type'=>$request->ref1_type[$x],
                );

                //
                $product = Products::where('product_id',$request->product_id[$x])->first();

                // //add on the masterfile new location
                $masterfile[] = array(
                    'ref_no'=>$ref_no,
                    'customer_id'=>$product->customer_id,
                    'store_id'=>$request->store_id,
                    'company_id'=>$request->company_id,
                    'warehouse_id'=>$request->warehouse_id,
                    'storage_location_id'=>$request->new_location[$x],
                    'product_id'=>$request->product_id[$x],
                    'item_type'=>$request->item_type[$x],
                    'trans_type'=>'SM',
                    'status'=>'X',
                    'inv_qty'=>$request->new_inv_qty[$x],
                    'inv_uom'=>$request->new_inv_uom[$x],
                    'whse_qty'=>$request->new_inv_qty[$x],
                    'whse_uom'=>$request->new_inv_uom[$x],
                    // 'ref1_no'=>$request->ref1_no[$x],
                    // 'ref1_type'=>$request->ref1_type[$x],
                    'created_at'=>date("Y-m-d H:i:s", strtotime("+5 sec")),
                    'updated_at'=>date("Y-m-d H:i:s", strtotime("+5 sec"))

                );

                //deduct from old
                $masterfile[] = array(
                    'ref_no'=>$ref_no,
                    'store_id'=>$request->store_id,
                    'customer_id'=>$product->customer_id,
                    'company_id'=>$request->company_id,
                    'warehouse_id'=>$request->warehouse_id,
                    'storage_location_id'=>$request->old_location[$x],
                    'trans_type'=>'SM',
                    'status'=>'X',
                    'product_id'=>$request->product_id[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>($request->new_inv_qty[$x] * -1),
                    'inv_uom'=>$request->new_inv_uom[$x],
                    'whse_qty'=>($request->new_inv_qty[$x] * -1),
                    'whse_uom'=>$request->new_inv_uom[$x],
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime
                );

                $_stockOutMasterdata[] = array(
                    'customer_id'=>$product->customer_id,
                    'company_id'=>$request->company_id,
                    'store_id'=>$request->store_id,
                    'warehouse_id'=>$request->warehouse_id,
                    'product_id'=>$request->product_id[$x],
                    'storage_location_id'=>($request->old_location[$x] == 0 || $request->old_location[$x] == 'null') ? NULL : $request->old_location[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>($request->new_inv_qty[$x]),
                    'inv_uom'=>$request->new_inv_uom[$x],
                    'whse_qty'=>($request->new_inv_qty[$x]),
                    'whse_uom'=>$request->new_inv_uom[$x],
                    'rcv_dtl_id'=>$request->rcv_dtl_id[$x],
                    'master_id'=>$request->master_id[$x],
                    // 'expiry_date'=>$request->expiry_date,
                    // 'lot_no'=>$request->lot_no,
                    // 'received_date'=>date("Y-m-d", strtotime($request->date_received)),
                );

                $_stockInMasterdata[] = array(
                    'customer_id'=>$product->customer_id,
                    'company_id'=>$request->company_id,
                    'store_id'=>$request->store_id,
                    'warehouse_id'=>$request->warehouse_id,
                    'product_id'=>$request->product_id[$x],
                    'storage_location_id'=>$request->new_location[$x],
                    'item_type'=>$request->item_type[$x],
                    'inv_qty'=>($request->new_inv_qty[$x]),
                    'inv_uom'=>$request->new_inv_uom[$x],
                    'whse_qty'=>($request->new_inv_qty[$x]),
                    'whse_uom'=>$request->new_inv_uom[$x],
                    'rcv_dtl_id'=>$request->rcv_dtl_id[$x],
                    'master_id'=>$request->master_id[$x],
                );
            }

            $result= MvDtl::where('ref_no',$ref_no)->delete();
            MvDtl::insert($dtl);

            $audit_trail[] = [
                'control_no' => $ref_no,
                'type' => 'SM',
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
                'data'    => $sm,
                'id'=> _encode($sm->id)
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
        $mv_hdr = MvHdr::select('*')->where('id', _decode($id))->first();
        $client_list = Client::where('is_enabled', '1')->get();

        $mv_dtl = MvDtl::select('*')->where('ref_no', $mv_hdr->ref_no)->get();

        $location = (new SettingsController)->getLocationPerWarehouse($mv_hdr->warehouse_id);
        return view('stock/movement/view', [
            'client_list'=>$client_list,
            'mv_hdr'=> $mv_hdr,
            'mv_dtl'=> $mv_dtl,
            'location'=>$location,
        ]);
    }

    public function edit($id)
    {
        $mv_hdr = MvHdr::select('*')->where('id', _decode($id))->first();
        $client_list = Client::where('is_enabled', '1')->get();

        $mv_dtl = MvDtl::select('*')->where('ref_no', $mv_hdr->ref_no)->get();

        $location = (new SettingsController)->getLocationPerWarehouse($mv_hdr->warehouse_id);
        return view('stock/movement/edit', [
            'client_list'=>$client_list,
            'mv_hdr'=> $mv_hdr,
            'mv_dtl'=> $mv_dtl,
            'location'=>$location,
        ]);
    }

    public function getValidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse'=>'required',
            'company'=>'required',
            'store'=>'required',

        ], [
            'warehouse'=>'Warehouse is required',
            'company'=>'Company  is required',
            'store'=>'Store is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {

            $data = [
                'warehouse'=>_encode($request->warehouse),
                'company'=>_encode($request->company),
                'store'=>_encode($request->store)
            ];

            session(['mv_data' => $data]);

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
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

    public function generateSMNo()
    {
        $data = SeriesModel::where('trans_type', '=', 'SM')->where('created_at', '>=', date('Y-m-01 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'));
        $count = $data->count();
        $count = $count + 1;
        $date = date('ym');

        $num = str_pad((int)$count, 5, "0", STR_PAD_LEFT);

        $series = "SM-" . $date . "-" . $num;

        return $series;
    }

    public function getNewLocation($warehouse_id, $storage_id) {

        $location_list = \App\Models\StorageLocationModel::select('storage_location_id','location')->where('warehouse_id', $warehouse_id)->orderBy('location')->get();

        $html = '<option value="">New Loc</option>';
        foreach ($location_list as $loc) {
            $sel = ($loc->storage_location_id == $storage_id) ? 'selected' : '';
            $html .= "<option $sel value='".$loc->storage_location_id."'>".$loc->location."</option>";
        }
        return $html;
    }
    public function destroy(Request $request)
    {
        DB::connection()->beginTransaction();

        try
        {
            $ref_no = $request->ref_no;
            if($ref_no) {
                $mv_hdr = MvHdr::where('ref_no', $ref_no)->delete();
                $mv_dtl = MvDtl::where('ref_no', $ref_no)->delete();
                MasterfileModel::where('ref_no', $ref_no)->delete();
                $audit_trail[] = [
                    'control_no' => $ref_no,
                    'type' => 'SM',
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
                    'data'    => $mv_hdr
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

    public function unpost(Request $request)
    {
        DB::connection()->beginTransaction();

        try
        {
            $ref_no = $request->ref_no;
            if($ref_no) {
                $rcv = MvHdr::where('ref_no', $ref_no)->update(['status'=>'open']);

                //remove on masterfiles
                $mster_hdr = MasterfileModel::where('ref_no', $ref_no)->delete();

                //get all rcvdtl_id in mvdtl
                $rcvdtl_id_list = MvDtl::where('ref_no', $ref_no)->get();
                $mv = MvHdr::where('ref_no', $ref_no)->first();

                $_stockOutMasterdata = [];
                $_stockInMasterdata = [];

                foreach($rcvdtl_id_list as $rec) {
                    $data[] = $rec->rcv_dtl_id;
                    //deduct the qty to dest

                    $product = Products::where('product_id',$rec->product_id)->first();

                    $_stockOutMasterdata[] = array(
                        'company_id'=>$mv->company_id,
                        'customer_id'=>$product->customer_id,
                        'store_id'=>$mv->store_id,
                        'warehouse_id'=>$mv->warehouse_id,
                        'product_id'=>$rec->product_id,
                        'storage_location_id'=> $rec->new_storage_location_id,
                        'item_type'=>$rec->new_item_type,
                        'inv_qty'=>($rec->new_inv_qty),
                        'inv_uom'=>$rec->new_inv_uom,
                        'whse_qty'=>($rec->new_inv_qty),
                        'whse_uom'=>$rec->new_inv_uom,
                        'rcv_dtl_id'=>$rec->rcv_dtl_id,
                    );

                    $_stockInMasterdata[] = array(
                        'company_id'=>$mv->company_id,
                        'customer_id'=>$product->customer_id,
                        'store_id'=>$mv->store_id,
                        'warehouse_id'=>$mv->warehouse_id,
                        'product_id'=>$rec->product_id,
                        'storage_location_id'=>$rec->old_storage_location_id,
                        'item_type'=>$rec->old_item_type,
                        'inv_qty'=>($rec->new_inv_qty),
                        'inv_uom'=>$rec->old_inv_uom,
                        'whse_qty'=>($rec->new_inv_qty),
                        'whse_uom'=>$rec->old_inv_uom,
                        'rcv_dtl_id'=>$rec->rcv_dtl_id,
                        'master_id'=>$rec->master_id,
                    );

                }

                _stockInMasterData($_stockInMasterdata);
                _stockOutMasterData($_stockOutMasterdata);


                $audit_trail[] = [
                    'control_no' => $ref_no,
                    'type' => 'SM',
                    'status' => 'open',
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                    'user_id' => Auth::user()->id,
                    'data' => 'unpost'
                ];

                AuditTrail::insert($audit_trail);

                DB::connection()->commit();

                return response()->json([
                    'success'  => true,
                    'message' => 'Updated successfully!',
                    'data'    => $rcv
                ]);

            } else {
                DB::rollBack();
                return response()->json([
                    'success'  => false,
                    'message' => 'Unable to process request. Please try again.',
                    'data'    => 'Reference is empty.'
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
}
