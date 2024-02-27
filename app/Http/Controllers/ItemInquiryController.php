<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\RcvDtl;
use App\Models\RcvHdr;
use Illuminate\Http\Request;
class ItemInquiryController extends Controller
{

    public function index(Request $request)
    {
        $data = RcvDtl::select('rcv_dtl.*','rcv_hdr.date_received','rcv_hdr.status')
                ->leftJoin('rcv_hdr', 'rcv_hdr.rcv_no', '=', 'rcv_dtl.rcv_no')
                ->with('withdraw')
                ->where('rcv_hdr.status','posted')
                ->where('rcv_dtl.product_id',$request->product_id)
                ->get()->toArray();
        $result = array_map(function ($item) {
            $item['date_received'] = date('M d, Y', strtotime($item['date_received'])); // Change the format as needed
            return $item;
        }, $data);

        return view('inquiry/index', [
            'data'=> $result,
            'request'=> $request,
        ]);
    }
}
