<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $client_list = User::all();
        $dateRangeParts = explode(" to ", $request->date);
        $startDate = isset($dateRangeParts[0]) ? $dateRangeParts[0] : "";
        $endDate = isset($dateRangeParts[1]) ? $dateRangeParts[1] : "";

        $data_list = AuditTrail::select('audit_trail.*','u.name as created_by')
                    ->leftJoin('users as u', 'u.id', '=', 'audit_trail.user_id')
                    ->orderBy('audit_trail.control_no','ASC')
                    ->orderBy('audit_trail.created_at','ASC')
                    ->where([
                        [function ($query) use ($request,$startDate,$endDate) {
                            if (($s = $request->status)) {
                                if($s != 'all')
                                    $query->orWhere('audit_trail.status', $s);
                            }
                            if ($request->q) {
                                $query->where('audit_trail.control_no', $request->q);
                            }

                            if ($request->type) {
                                $query->where('audit_trail.type', $request->type);
                            }

                            if (isset($request->user_id)) {
                                $query->where('audit_trail.user_id', $request->user_id);
                            }

                            if (isset($request->date) && $startDate && $endDate) {
                                $query->whereBetween('audit_trail.created_at', [$startDate,$endDate]);
                            }

                            $query->get();
                        }]
                    ])
                    ->get();
        return view('audit.index',compact('client_list','data_list','request'));
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
