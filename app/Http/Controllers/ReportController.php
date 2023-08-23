<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\MasterfileModel;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\Store;

use DataTables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function getStockLedger(Request $request)
    {
        $supplier_list = Supplier::all();
        $client_list = Client::where('is_enabled', '1')->get();

        return view('report/stock_ledger', [
            'request'=>$request,
            'supplier_list'=>$supplier_list,
            'client_list'=>$client_list
        ]);
    }

}
