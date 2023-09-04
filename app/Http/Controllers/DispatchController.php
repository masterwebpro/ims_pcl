<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Http\Controllers\Controller;
use App\Models\DispatchHdr;
use App\Models\TruckType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dispatch_list = DispatchHdr::select('dispatch_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'dispatch_hdr.created_by')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->orWhere('dispatch_hdr.dispatch_no','like', '%'.$s.'%');
                    $query->get();
                }
            }]
        ])->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('dispatch_hdr.status', $s);
                }

                if ($request->filter_date && $request->dispatch_date) {
                    if($request->filter_date == 'dispatch_date') {
                        $query->whereBetween('dispatch_hdr.dispatch_date', [$request->dispatch_date." 00:00:00", $request->dispatch_date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('dispatch_hdr.created_at', [$request->created_at." 00:00:00", $request->created_at." 23:59:00"]);
                    }

                }

                $query->get();
            }]
        ])
        ->paginate(20);

        return view('dispatch/index', ['dispatch_list'=>$dispatch_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $truck_type_list = TruckType::all();
        return view('dispatch/create', [
            'truck_type_list' => $truck_type_list,
            'created_by' => Auth::user()->name,
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dispatch  $dispatch
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
     * @param  \App\Models\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dispatch $dispatch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dispatch $dispatch)
    {
        //
    }
}
