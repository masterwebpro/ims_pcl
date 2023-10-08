<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\DispatchHdr;
use App\Models\ExpenseCharges;
use App\Models\ExpenseDtl;
use App\Models\ExpenseHdr;
use App\Models\SeriesModel;
use App\Models\TruckType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $expense_list = ExpenseHdr::select('expense_hdr.*', 'u.name','tl.trucker_name','pl.vehicle_type','tt.vehicle_code','tt.vehicle_desc')
        ->leftJoin('plate_no_list as pl','pl.plate_no','expense_hdr.plate_no')
        ->leftJoin('trucker_list as tl','tl.id','pl.trucker_id')
        ->leftJoin('truck_type as tt','tt.vehicle_code','pl.vehicle_type')
        ->leftJoin('users as u', 'u.id', '=', 'expense_hdr.created_by')
        ->orderByDesc('created_at')
        ->where([
            [function ($query) use ($request) {
                if (($s = $request->status)) {
                    if($s != 'all')
                        $query->orWhere('expense_hdr.status', $s);
                }

                if ($request->q) {
                    $query->where('expense_hdr.expense_no', $request->q)
                        ->orWhere('expense_hdr.trucker_name', $request->q)
                        ->orWhere('expense_hdr.truck_type', $request->q)
                        ->orWhere('expense_hdr.plate_no', $request->q);
                }

                if ($request->filter_date && $request->expense_date) {
                    if($request->filter_date == 'expense_date') {
                        $query->whereBetween('expense_hdr.expense_date', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                    }
                    if($request->filter_date == 'created_at') {
                        $query->whereBetween('expense_hdr.created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                    }

                }

                $query->get();
            }]
        ])
        ->paginate(20);
        return view('expense/index', ['expense_list'=>$expense_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $plate_no_list =  \App\Models\PlateNoList::select('plate_no_list.vehicle_type','tt.vehicle_code','tt.vehicle_desc','plate_no_list.plate_no','plate_no_list.trucker_id','tl.trucker_name')
        ->leftJoin('trucker_list as tl','tl.id','plate_no_list.trucker_id')
        ->leftJoin('truck_type as tt','tt.vehicle_code','plate_no_list.vehicle_type')
        ->groupBy('plate_no_list.plate_no')
        ->get();
        return view('expense/create', ['plate_no_list'=>$plate_no_list]);
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
            'plate_no' => 'required',
            'expense_date' => 'required',
            'particular' => 'required',
            'amount' => 'required'
        ], [
            'plate_no' => 'Plate no is required',
            'expense_date'=>'Expense date is required',
            'dispatch_no.*'=>'Dispatch is required',
            'particular.*'=>'Particular is required',
            'amount.*'=>'Amount is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        DB::beginTransaction();

        try {
            $expense_no = $request->expense_no;
            if($expense_no=='') {
                $expense_no = $this->generateexpenseNo("EX","E");

                $series[] = [
                    'series' => $expense_no,
                    'trans_type' => 'EX',
                    'created_at' => $this->current_datetime,
                    'updated_at' => $this->current_datetime,
                    'user_id' => Auth::user()->id,
                ];
                SeriesModel::insert($series);
            }

            $expense_date = isset($request->expense_date) ? $request->expense_date : date("Y-m-d");
            $expense = ExpenseHdr::updateOrCreate(['expense_no' => $expense_no], [
                'expense_no'=>$expense_no,
                'expense_date'=> $expense_date,
                'plate_no' => $request->plate_no,
                'status'=>$request->status,
                'created_by' =>Auth::user()->id,
                'created_at'=>$this->current_datetime,
                'updated_at'=>$this->current_datetime,
            ]);
            ExpenseDtl::where('expense_no',$expense_no)->delete();
            ExpenseCharges::where('expense_no',$expense_no)->delete();
            if(isset($request->dispatch_no)){
                for($x=0; $x < count($request->dispatch_no); $x++ ) {
                    $dtl = array(
                        'dispatch_no' => $request->dispatch_no[$x],
                        'dispatch_date'=>$request->dispatch_date[$x],
                        'expense_no'=>$expense_no,
                    );
                    $dtl = ExpenseDtl::create($dtl);
                    // DispatchHdr::where('dispatch_no',$request->dispatch_no[$x])->update(['expense_no' => $expense_no]);
                }

            }

            if(isset($request->particular)){
                for($x=0; $x < count($request->particular); $x++ ) {
                    $charges = array(
                        'particular' => $request->particular[$x],
                        'amount'=>$request->amount[$x],
                        'expense_no'=>$expense_no
                    );
                    ExpenseCharges::create($charges);
                }
            }

            $audit_trail[] = [
                'control_no' => $expense_no,
                'type' => 'EX',
                'status' => $request->status,
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
                'user_id' => Auth::user()->id,
                'data' => null
            ];

            AuditTrail::insert($audit_trail);

            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $expense,
                'id'=> _encode($expense->id)
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => throw new Exception($e->getMessage())
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense = ExpenseHdr::select('expense_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'expense_hdr.created_by')
        ->where('expense_hdr.id', _decode($id))->first();
        $truck_type_list = TruckType::all();
        $plate_no_list =  \App\Models\PlateNoList::select('plate_no_list.vehicle_type','tt.vehicle_code','tt.vehicle_desc','plate_no_list.plate_no','plate_no_list.trucker_id','tl.trucker_name')
                        ->leftJoin('trucker_list as tl','tl.id','plate_no_list.trucker_id')
                        ->leftJoin('truck_type as tt','tt.vehicle_code','plate_no_list.vehicle_type')
                        ->groupBy('plate_no_list.plate_no')
                        ->get();

        return view('expense/view', [
            'expense' => $expense,
            'truck_type_list' => $truck_type_list,
            'plate_no_list' => $plate_no_list,
            'created_by' => Auth::user()->name
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $expense = ExpenseHdr::select('expense_hdr.*', 'u.name')
        ->leftJoin('users as u', 'u.id', '=', 'expense_hdr.created_by')
        ->where('expense_hdr.id', _decode($id))->first();
        $truck_type_list = TruckType::all();
        $plate_no_list =  \App\Models\PlateNoList::select('plate_no_list.vehicle_type','tt.vehicle_code','tt.vehicle_desc','plate_no_list.plate_no','plate_no_list.trucker_id','tl.trucker_name')
                        ->leftJoin('trucker_list as tl','tl.id','plate_no_list.trucker_id')
                        ->leftJoin('truck_type as tt','tt.vehicle_code','plate_no_list.vehicle_type')
                        ->groupBy('plate_no_list.plate_no')
                        ->get();

        return view('expense/edit', [
            'expense' => $expense,
            'truck_type_list' => $truck_type_list,
            'plate_no_list' => $plate_no_list,
            'created_by' => Auth::user()->name
        ]);
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

    public function generateExpenseNo($type,$prefix)
    {
        $data = SeriesModel::where('trans_type', '=', $type)->where('created_at', '>=', date('Y-m-01 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'));
        $count = $data->count();
        $count = $count + 1;
        $date = date('ym');

        $num = str_pad((int)$count, 5, "0", STR_PAD_LEFT);

        $series = "$prefix-" . $date . "-" . $num;

        return $series;
    }
}
