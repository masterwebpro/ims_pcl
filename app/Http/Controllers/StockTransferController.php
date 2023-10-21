<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\TransferHdr;
use App\Models\TransferDtl;
use App\Models\SeriesModel;
use App\Models\AuditTrail;
use App\Models\MasterfileModel;

use Illuminate\Http\Request;

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
    { $client_list = Client::where('is_enabled', '1')->get();

        return view('stock/transfer/create', [
            'client_list'=>$client_list, 
        ]);
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        dd($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
