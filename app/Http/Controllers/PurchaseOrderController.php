<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\PoHdr;
use App\Models\PoDtl;
use App\Models\Client;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\UOM;

use DataTables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $po_list = PoHdr::select('po_hdr.*', 'sp.supplier_name', 's.store_name','c.client_name', 'u.name', DB::raw(
            '(SELECT sum(total_amount) as total_net 
                FROM po_dtl pd
                WHERE pd.po_num = po_hdr.po_num 
                group by pd.po_num) AS total_net'
        , true))
        ->leftJoin('suppliers as sp', 'sp.id', '=', 'po_hdr.supplier_id')
        ->leftJoin('store_list as s', 's.id', '=', 'po_hdr.store_id')
        ->leftJoin('client_list as c', 'c.id', '=', 'po_hdr.client_id')
        ->leftJoin('users as u', 'u.id', '=', 'po_hdr.created_by')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->leftJoin('suppliers as sp', 'sp.id', '=', 'po_hdr.supplier_id');
                    $query->leftJoin('store_list as s', 's.id', '=', 'po_hdr.store_id');
                    $query->leftJoin('client_list as c', 'c.id', '=', 'po_hdr.client_id');
                    $query->orWhere('po_hdr.po_num','like', '%'.$s.'%');
                    $query->orWhere('sp.supplier_name', 'like', '%' . $s . '%');
                    $query->orWhere('s.store_name', 'LIKE', '%' . $s . '%');
                    $query->orWhere('c.client_name', 'LIKE', '%' . $s . '%');
                    $query->get();
                }
            }]
        ])->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('po_hdr.status', $s);
                }

                if ($request->filter_date && $request->po_date) {
                    if($request->filter_date == 'po_date') {
                        $query->whereBetween('po_hdr.po_date', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('po_hdr.created_at', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                    }
                    
                }

                $query->get();
            }]
        ])
        ->paginate(20);

        return view('po/index', ['po_list'=>$po_list]);
    }

    public function create()
    {
        $client_list = Client::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $uom = UOM::all();

        return view('po/create', [
            'client_list'=>$client_list, 
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'uom'=>$uom
        ]);
    }

    public function store(Request $request)
    {
        
        DB::connection()->beginTransaction();

        try {
            $po = PoHdr::updateOrCreate(['id' => $request->po_id], [
                'po_num'=>$request->po_num,
                'store_id'=>$request->store,
                'client_id'=>$request->client,
                'supplier_id'=>$request->supplier,
                'po_date'=>date("Y-m-d", strtotime($request->po_date)),
                'status'=>$request->status,
                'created_by' =>Auth::user()->id,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);

            //save on dtl
            $dtl = array();
            for($x=0; $x < count($request->product_code); $x++ ) {
                $dtl[] = array(
                    'po_num'=>$request->po_num,
                    'product_id'=>$request->product_id[$x],
                    'uom_id'=>$request->uom[$x],
                    'requested_qty'=>$request->qty[$x],
                    'unit_amount'=>parseNumber(($request->unit_price[$x]) ? $request->unit_price[$x] : 0),
                    'discount'=>parseNumber(($request->discount[$x]) ? $request->discount[$x] : 0),
                    'total_amount'=>parseNumber(($request->amount[$x]) ? $request->amount[$x] : 0),
                );
            }

            $result= PoDtl::where('po_num',$request->po_num)->delete();
            PoDtl::insert($dtl);
            
            DB::connection()->commit();
            
            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $po,
                'id'=> _encode($po->id)
            ]);
        
        }
        catch(Throwable $e)
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
        $po = PoHdr::select('po_hdr.*', 'u.name', DB::raw(
            '(SELECT (sum(total_amount) - sum(discount))  as total_net 
                FROM po_dtl pd
                WHERE pd.po_num = po_hdr.po_num 
                group by pd.po_num) AS total_net'
        , true))
        ->leftJoin('users as u', 'u.id', '=', 'po_hdr.created_by')
        ->where('po_hdr.id', _decode($id))->first();
        
        $uom = UOM::all();
        return view('po/view', ['po'=>$po, 'uom'=>$uom]);
    }

    public function edit($id)
    {
        $po = PoHdr::select('po_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'po_hdr.created_by')
        ->where('po_hdr.id', _decode($id))->first();

        $client_list = Client::all();
        $store_list = Store::all();
        $supplier_list = Supplier::all();
        $uom = UOM::all();

        return view('po/edit', [
            'po'=>$po, 
            'client_list'=>$client_list, 
            'store_list'=>$store_list,
            'supplier_list'=>$supplier_list,
            'uom'=>$uom
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
