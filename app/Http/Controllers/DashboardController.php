<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

class DashboardController extends Controller
{
    //

    public function __construct()
    {
        // $this->middleware('auth');
    }


    function index() {

        $warehouse_qty = DB::table('masterdata')
            ->select('warehouses.warehouse_name', 'masterdata.warehouse_id', DB::raw("sum(inv_qty) as wh_qty"), DB::raw("sum(reserve_qty) as reserve_qty"))
            ->leftJoin('warehouses','warehouses.id','=','masterdata.warehouse_id')
            ->groupBy('warehouses.warehouse_name','masterdata.warehouse_id')
            ->orderBy('warehouses.warehouse_name','asc')
            ->get();

    //warehouse utilization
        $location_cnt = DB::table('storage_locations')
            ->select('warehouses.warehouse_name','warehouse_id', DB::raw("count(location) as cnt_location"), DB::raw("(SELECT  count(DISTINCT storage_location_id) FROM masterdata WHERE masterdata.warehouse_id = storage_locations.warehouse_id) as occupied") )
            ->leftJoin('warehouses','warehouses.id','=','storage_locations.warehouse_id')
            ->groupBy('warehouses.warehouse_name','storage_locations.warehouse_id')
            ->get();


        return view('dashboard/index', compact('warehouse_qty', 'location_cnt'));
    }

    public function getInboundCount(Request $request){
        $dateRangeParts = explode(" to ", $request->date);
        $from = isset($dateRangeParts[0]) ? $dateRangeParts[0] : Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = isset($dateRangeParts[1]) ? $dateRangeParts[1] : Carbon::now()->endOfMonth()->format('Y-m-d');

        $type = $request->type;
        $year = $request->year ? $request->year : date('Y');
        try {
            $query = "SELECT DATE_FORMAT(rcv_hdr.date_received, '%Y-%m-%d') AS date,
                    COUNT(DISTINCT rcv_dtl.rcv_no) as transaction ,
                    SUM(rcv_dtl.inv_qty) as quantity  from rcv_dtl
                    LEFT JOIN rcv_hdr ON rcv_hdr.rcv_no = rcv_dtl.rcv_no
                    WHERE rcv_hdr.status = 'posted'
                    GROUP BY date";
            $result = DB::select($query);
            return $this->mapData($from,$to, $year, $type, $result);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getOutboundCount(Request $request){
        $dateRangeParts = explode(" to ", $request->date);
        $from = isset($dateRangeParts[0]) ? $dateRangeParts[0] : Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = isset($dateRangeParts[1]) ? $dateRangeParts[1] : Carbon::now()->endOfMonth()->format('Y-m-d');

        $type = $request->type;
        $year = $request->year ? $request->year : date('Y');
        try {
            $query = "SELECT DATE_FORMAT(dispatch_hdr.dispatch_date, '%Y-%m-%d') AS date,
                    COUNT(DISTINCT dispatch_dtl.dispatch_no) as transaction ,
                    SUM(dispatch_dtl.qty) as quantity  from dispatch_dtl
                    LEFT JOIN dispatch_hdr ON dispatch_hdr.dispatch_no = dispatch_dtl.dispatch_no
                    WHERE dispatch_hdr.status = 'posted'
                    GROUP BY date";
            $result = DB::select($query);
            return $this->mapData($from,$to, $year, $type, $result);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function mapData($from,$to, $year, $type, $result){
        $start = $from;
        $count = array();
        $qty = array();
        $labels = array();
        if($type == 'daily'){
            while (strtotime($from) <= strtotime($to)) {
                $transaction = array_column(array_values(array_filter($result,function($v)use($from){
                    return (strtotime($v->date) == strtotime($from));
                })),'transaction');

                $quantity = array_column(array_values(array_filter($result,function($v)use($from){
                    return (strtotime($v->date) == strtotime($from));
                })),'quantity');

                array_push($count, (($transaction) ? array_sum($transaction) : 0));
                array_push($qty, (($quantity) ? array_sum($quantity) : 0));
                array_push($labels, date('M d, Y',strtotime($from)));
                $from = date('Y-m-d', strtotime("+1 day", strtotime($from)));
            }
            $from = $start;
        }
        else{
            for($mon = 1; $mon <= 12 ; $mon++)
            {
                $year_month = date('Y',strtotime($year))."-".$mon;
                $transaction = array_column(array_values(array_filter($result,function($v)use($year_month){
                    return date('Y-m',strtotime($v->date)) == date('Y-m',strtotime($year_month));
                })),'transaction');

                $quantity = array_column(array_values(array_filter($result,function($v)use($year_month){
                    return date('Y-m', strtotime($v->date)) == date('Y-m', strtotime($year_month));
                })),'quantity');

                array_push($count, (($transaction) ? array_sum($transaction) : 0));
                array_push($qty, (($quantity) ? array_sum($quantity) : 0));

                $month = date("M", mktime(0, 0, 0, $mon, 10));
                $labels[] = $month;
            }
        }
        return array(
            "labels" => $labels,
            "transaction" => $count,
            "quantity" => $qty,
            "tot_trans" => array_sum(array_values($count)),
            "tot_qty" => array_sum(array_values($qty)),
        );
    }
}
