<?php

namespace App\Http\Controllers;

use App\Models\Pod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pod_list = Pod::select('pod.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'pod.created_by')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->q)) {
                    $query->orWhere('pod.batch_no','like', '%'.$s.'%');
                    $query->get();
                }
            }]
        ])->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('pod.status', $s);
                }

                if ($request->filter_date && $request->dispatch_date) {
                    if($request->filter_date == 'dispatch_date') {
                        $query->whereBetween('pod.dispatch_date', [$request->dispatch_date." 00:00:00", $request->dispatch_date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('pod.created_at', [$request->created_at." 00:00:00", $request->created_at." 23:59:00"]);
                    }

                }

                $query->get();
            }]
        ])
        ->paginate(20);
        return view('pod/index', ['pod_list'=>$pod_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pod  $pod
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pod = Pod::select('pod.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'pod.created_by')
        ->where('pod.id', _decode($id))->first();

        return view('pod/view', ['pod'=>$pod]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pod  $pod
     * @return \Illuminate\Http\Response
     */
    public function edit(Pod $pod)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pod  $pod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pod $pod)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pod  $pod
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pod $pod)
    {
        //
    }
}
