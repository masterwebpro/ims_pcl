<?php

namespace App\Http\Controllers;

use App\Models\Particular;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ParticularController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Particular::where([
                [function ($query) use ($request) {
                    if (($s = $request->q)) {
                        $query->orWhere('code','like', '%'.$s.'%');
                        $query->orWhere('name','like', '%'.$s.'%');
                        $query->get();
                    }
                }]
            ])
            ->where([
                [function ($query) use ($request) {
                    if ($request->filter_date) {
                        if($request->filter_date == 'created_at' && $request->date ) {
                            $query->whereBetween('created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                        }
                    }

                    $query->get();
                }]
            ])
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('maintenance/particulars/index', ['list' => $list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('maintenance/particulars/create');
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
            'code.*' => 'required|present',
            'name.*' => 'required|present',
            'amount.*' => 'required|present'
        ], [
            'code.*' => 'Code is required',
            'name.*' => 'Name is required',
            'amount.*' => 'Amount is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::connection()->beginTransaction();
        try {
            if(isset($request->code)){
                for($x=0; $x < count($request->code); $x++ ) {
                    $particular = array(
                        'code' => $request->code[$x],
                        'name' => $request->name[$x],
                        'amount' => $request->amount[$x],
                    );
                    Particular::updateOrCreate(['particular_id' => $request->particular_id[$x]],$particular);
                }
            }

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
            ]);

        }
        catch(\Throwable $e)
        {
            DB::connection()->rollBack();
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Particular  $particular
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $particular = Particular::find(_decode($id));
        return view('maintenance/particulars/view', [
        'particular' => $particular
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Particular  $particular
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $particular = Particular::find(_decode($id));
        return view('maintenance/particulars/edit', [
        'particular' => $particular
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Particular  $particular
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Particular $particular)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Particular  $particular
     * @return \Illuminate\Http\Response
     */
    public function destroy(Particular $particular)
    {
        //
    }
}
