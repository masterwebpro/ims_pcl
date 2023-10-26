<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
class DashboardController extends Controller
{
    //

    public function __construct()
    {
        // $this->middleware('auth');
    }


    function index() {

       // Auth::user()->avatar


        return view('dashboard/index');
    }

    public function getDispatchCount(Request $request){
        $year = 2023;
        $month = 10; // October
        $workWeeks = getWorkWeeksInCurrentMonth($year, $month);
        print_r($workWeeks);
        die();

        foreach ($workWeeks as $week) {
            echo "Start Date: {$week['start_date']}\n";
            echo "End Date: {$week['end_date']}\n\n";
        }
        die();
        // $weeksInMonth = getWeeksInMonth($year, $month);
        // // print_r($weeksInMonth);die();
        // foreach ($weeksInMonth as $week) {
        //     echo "Week Number: {$week['week_number']}\n <br>";
        //     echo "Start Date: {$week['start_date']}\n  <br>";
        //     echo "End Date: {$week['end_date']}\n\n <br>";
        // }
        // die();
    }
}
