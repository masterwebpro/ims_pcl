<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\MvHdr;
use App\Models\MvDtl;
use App\Models\SeriesModel;
use App\Models\AuditTrail;
use App\Models\MasterfileModel;

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
        $location = [
            "rack" => "",
            "layer"=> ""
        ];

        $location = (new SettingsController)->getLocationPerWarehouse($warehouse);

        return view('stock/movement/create', [
            'client_list'=>$client_list, 
            'warehouse_id' =>$warehouse? $warehouse : '',
            'store_id'=> $store? $store : '',
            'company_id'=> $company? $company : '',
            'location'=> $location,
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
                );

                // //add on the masterfile new location
                $masterfile[] = array(
                    'ref_no'=>$ref_no,
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
                    'created_at'=>$this->current_datetime,
                    'updated_at'=>$this->current_datetime,
                );

                //deduct from old
                $masterfile[] = array(
                    'ref_no'=>$ref_no,
                    'store_id'=>$request->store_id,
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
                    'updated_at'=>$this->current_datetime,
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
}
