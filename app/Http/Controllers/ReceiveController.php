<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\RcvHdr;
use App\Models\RcvDtl;
use App\Models\Client;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\UOM;

use DataTables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;



class ReceiveController extends Controller
{
    public function index(Request $request)
    {
        $receive_list = RcvHdr::select('rcv_hdr.*', 'sp.supplier_name', 's.store_name','c.client_name', 'u.name')
        ->leftJoin('suppliers as sp', 'sp.id', '=', 'rcv_hdr.supplier_id')
        ->leftJoin('store_list as s', 's.id', '=', 'rcv_hdr.store_id')
        ->leftJoin('client_list as c', 'c.id', '=', 'rcv_hdr.client_id')
        ->leftJoin('users as u', 'u.id', '=', 'rcv_hdr.created_by')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->leftJoin('suppliers as sp', 'sp.id', '=', 'rcv_hdr.supplier_id');
                    $query->leftJoin('store_list as s', 's.id', '=', 'rcv_hdr.store_id');
                    $query->leftJoin('client_list as c', 'c.id', '=', 'rcv_hdr.client_id');
                    // $query->orWhere('rcv_hdr.po_num','like', '%'.$s.'%');
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
                        $query->orWhere('rcv_hdr.status', $s);
                }

                // if ($request->filter_date && $request->po_date) {
                //     if($request->filter_date == 'po_date') {
                //         $query->whereBetween('po_hdr.po_date', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                //     }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('rcv_hdr.created_at', [$request->po_date." 00:00:00", $request->po_date." 23:59:00"]);
                    }
                    
                // }

                $query->get();
            }]
        ])
        ->paginate(20);

        return view('receive/index', ['receive_list'=>$receive_list]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        
    }

    public function show($id)
    {
        $rcv = RcvHdr::select('rcv_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'rcv_hdr.created_by')
        ->where('rcv_hdr.id', _decode($id))->first();
        
        $uom = UOM::all();
        return view('receive/view', ['rcv'=>$rcv, 'uom'=>$uom]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
}
