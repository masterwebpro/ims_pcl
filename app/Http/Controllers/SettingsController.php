<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UOM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use DataTables;

class SettingsController extends Controller
{
    public function getStoreByClient(Request $request) {
        $data = \App\Models\Store::where('client_id', $request->client_id)->get();   
        return response()->json($data);
    }

    function getProducts(Request $request) {
        $products = \App\Models\Products::all(); 
        return Datatables::of($products)->addIndexColumn()->make();
    }

    function getProductBySupplier(Request $request, $supplier_id) {
        $products = \App\Models\Products::where('supplier_id', $supplier_id)->get(); 
        return Datatables::of($products)->addIndexColumn()->make();
    }

    function getPostedPo(Request $request) {
        $data = \App\Models\PoHdr::select('po_num')->where('status', 'posted')->get();   
        return response()->json($data);
    }
    
    public function getUom(Request $request) {
       
        $uom_list = UOM::all(); 
        $rand_no = mt_rand(1000,9999)."A";

       // $html = '<select name="uom[]" data-id="'.$rand_no.'" id="uom_'.$rand_no.'" class="uom form-select select2">';
        $html = '<option value="">UOM</option>';
        foreach ($uom_list as $uom) {
            $html .= "<option value='".$uom->uom_id."'>".$uom->code."</option>";
        }
       // $html .= " </select> ";

        return response()->json([
            'success'  => true,
            'message' => 'Saved successfully!',
            'data'    => $html
        ]);
    }

    public function getWarehouseByStore(Request $request) {
        $data = \App\Models\Warehouse::where('store_id', $request->store_id)->get();   
        return response()->json($data);
    }

}
