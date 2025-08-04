<?php

use Illuminate\Support\Facades\DB;
use App\Models\SeriesModel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

function test() {
    echo 'test';
}

function _pagination($data, $per_page = 10)
{
    if (strtoupper($per_page) != 'ALL') {
        return $data->paginate($per_page ?: 10);
    } else {
        $list = $data->get();
        $total = count($list);
        return [
            'data'         => $list,
            'current_page' => 1,
            'from'         => 1,
            'to'           => $total,
            'first_page'   => 1,
            'last_page'    => 1,
            'per_page'     => $per_page,
            'total'        => $total,
        ];
    }
}

function clean($user_string)
{
    // Removes special chars wothout A to Z and 0 to 9.
    $user_string = preg_replace("/[^a-zA-Z0-9\s]/", "", $user_string);

    // Then changes spaces for unserscores
    $user_string = preg_replace('/\s/', '-', $user_string);

    // Finally encode it ready for use
    $user_string = urlencode($user_string);

    return $user_string;

}

function _encryptKey()
{
    return 'jLDmhhlQBXS7E/ioYM6hw+Uo+PVJHnc='; // 32 chars only for AES-256-CBC
}

function _encrypt(string $plainTextToEncrypt)
{
    try {
        $newEncrypter = new \Illuminate\Encryption\Encrypter(_encryptKey(), Config::get('app.cipher'));
        return $newEncrypter->encrypt($plainTextToEncrypt);
    } catch (Exception $e) {
        return null;
    }
}

function _decrypt(string $plainTextToEncrypt)
{
    try {
        $newEncrypter = new \Illuminate\Encryption\Encrypter(_encryptKey(), Config::get('app.cipher'));
        return $newEncrypter->decrypt($plainTextToEncrypt);
    } catch (Exception $e) {
        return null;
    }
}

function _privateKey() {
    return "iamcrisbacera";
}

function _secretKey() {
    return "tersusNet";
}

function _encryptMethod() {
    return "AES-256-CBC";
}


function _encode(string $string) {
    $key     = hash('sha256', _privateKey());
    $ivalue  = substr(hash('sha256', _secretKey()), 0, 16); // sha256 is hash_hmac_algo
    $result      = openssl_encrypt($string, _encryptMethod(), $key, 0, $ivalue);
    return base64_encode($result);  // output is a encripted value
}

function _decode(string $string) {
    $key    = hash('sha256', _privateKey());
    $ivalue = substr(hash('sha256', _secretKey()), 0, 16); // sha256 is hash_hmac_algo
    return openssl_decrypt(base64_decode($string), _encryptMethod(), $key, 0, $ivalue);

}

function elipsis($string, $limit, $repl = '...')
{
  if(strlen($string) > $limit)
  {
    return substr($string, 0, $limit) . $repl;
  }
  else
  {
    return $string;
  }
}

/**
 * This will parse the money string
 *
 * For example 1, 234, 456.00 will be converted to 123456.00
 *
 * @return
 */
function parseNumber(string $money) : float
{
    $money = preg_replace('/[ ,]+/', '', $money);
    return number_format((float) $money, 2, '.', '');
}

function mod_access($module, $code, $user_id)
{
    $mod_access = DB::table('user_module_access')->select('user_module_access.*', 'm.module_name', 'p.code', 'p.name')->where('user_id', $user_id)
        ->leftJoin('modules as m', 'm.id', '=', 'user_module_access.module_id')
        ->leftJoin('permissions as p', 'p.id', '=', 'user_module_access.permission_id')
        ->where('module_name', $module)
        ->where('p.code', $code)->get();

    if ($mod_access->count() > 0) {
        return true;
    } else {
        return false;
    }
}
function hasMovement($ref_no, $type) {
    $hasMovement = DB::table('masterfiles')->where('ref1_no', $ref_no)
        ->where('ref1_type', $type)
        ->whereNotNull('storage_location_id')
        ->get();

    if ($hasMovement->count() > 0) {
        return true;
    } else {
        return false;
    }
}

function hasPendingMovement($ref_no, $type) {
    $hasPendingMovement = DB::table('mv_dtl')->where('ref1_no', $ref_no)
        ->where('ref1_type', $type)
        ->get();

    if ($hasPendingMovement->count() > 0) {
        return true;
    } else {
        return false;
    }
}

function _stockInMasterData($masterfile) {

    $insert_data = array();

    foreach($masterfile as $key => $params) {

        $masterfile_id = _has_masterfile($params);

        if($masterfile_id) {
            //update MASTERDATA

            $updateData = DB::table('masterdata')->where('product_id', $params['product_id'])
            ->where('company_id', $params['company_id'])
            ->where('store_id', $params['store_id'])
            ->where('warehouse_id', $params['warehouse_id'])
            ->where('product_id', $params['product_id'])
            ->where('inv_qty','>=',0);



            if(isset($params['master_id'])) {
                $updateData->where('id', $params['master_id']);

            } else {
                if(isset($params['storage_location_id'])) {
                    $updateData->where('storage_location_id', $params['storage_location_id']);
                } else {
                    $updateData->where('storage_location_id', null);
                }

                if(isset($params['rcv_dtl_id']))
                    $updateData->where('rcv_dtl_id', $params['rcv_dtl_id']);
            }

            // if(isset($params['lot_no']))
            //     $updateData->where('lot_no', $params['lot_no']);

            // if(isset($params['expiry_date']))
            //     $updateData->where('expiry_date', $params['expiry_date']);

            // if(isset($params['manufacture_date']))
            //     $updateData->where('manufacture_date', $params['manufacture_date']);

            $record = $updateData->first();

            DB::table('masterdata')
                ->where('id', $record->id)
                ->update([
                    'inv_qty' => DB::raw('inv_qty + '.$params['inv_qty']),
                    'whse_qty' => DB::raw('whse_qty + '.$params['whse_qty']),
                    'remarks' => isset($params['remarks']) ? $params['remarks'] : null
                ]);

        } else {
            //insert MASTERDATA
            $insert_data[] = array(
                'company_id'=>$params['company_id'],
                'customer_id'=>isset($params['customer_id']) ? $params['customer_id'] : 0,
                'store_id'=>$params['store_id'],
                'warehouse_id'=>$params['warehouse_id'],
                'product_id'=>$params['product_id'],
                'storage_location_id'=>isset($params['storage_location_id']) ? $params['storage_location_id'] : null ,
                'item_type'=>$params['item_type'],
                'inv_qty'=>$params['inv_qty'],
                'inv_uom'=>$params['inv_uom'],
                'whse_qty'=>$params['whse_qty'],
                'whse_uom'=>$params['whse_uom'],
                // 'expiry_date'=>isset($params['expiry_date']) ? $params['expiry_date'] : null ,
                // 'lot_no'=>isset($params['lot_no']) ? $params['lot_no'] : null,
                // 'received_date'=>isset($params['received_date']) ? $params['received_date'] : null,
                // 'manufacture_date'=>isset($params['manufacture_date']) ? $params['manufacture_date'] : null,
                'rcv_dtl_id'=>isset($params['rcv_dtl_id']) ? $params['rcv_dtl_id'] : null,
                'remarks'=>isset($params['remarks']) ? $params['remarks'] : null
            );
        }
    }

    if($insert_data) {
        DB::table('masterdata')->insert($insert_data);
    }
}

function _stockOutMasterData($masterfile) {
    foreach($masterfile as $key => $params) {
        $masterfile_id = _has_masterfile($params);

        if($masterfile_id) {

            //search on Master
            $updateData = DB::table('masterdata')->where('product_id', $params['product_id'])
                ->where('company_id', $params['company_id'])
                ->where('store_id', $params['store_id'])
                ->where('warehouse_id', $params['warehouse_id'])
                ->where('product_id', $params['product_id'])
                ->where('inv_qty','>=',$params['inv_qty']);

            if(isset($params['master_id'])) {

                $updateData->where('id', $params['master_id']);

            } else {

                if(isset($params['storage_location_id'])) {
                    $updateData->where('storage_location_id', $params['storage_location_id']);
                } else {
                    $updateData->where('storage_location_id', null);
                }

                if(isset($params['rcv_dtl_id']))
                $updateData->where('rcv_dtl_id', $params['rcv_dtl_id']);
            }



            // if(isset($params['lot_no']))
            //     $updateData->where('lot_no', $params['lot_no']);

            // if(isset($params['expiry_date']))
            //     $updateData->where('expiry_date', $params['expiry_date']);

            // if(isset($params['manufacture_date']))
            //     $updateData->where('manufacture_date', $params['manufacture_date']);

            $record = $updateData->first();
            //update MASTERDATA
            if($record) {
                DB::table('masterdata')
                    ->where('id', $record->id)
                    ->update([
                        'inv_qty' => DB::raw('inv_qty - '.$params['inv_qty']),
                        'whse_qty' => DB::raw('whse_qty - '.$params['whse_qty'])
                    ]);
            }
        }
    }
}

function _has_masterfile($params) {
    $result = DB::table('masterdata')->select('*');

    if(isset($params['company_id']))
        $result->where('company_id', $params['company_id']);

    if(isset($params['store_id']))
        $result->where('store_id', $params['store_id']);

    if(isset($params['warehouse_id']))
        $result->where('warehouse_id', $params['warehouse_id']);

    if(isset($params['product_id']))
        $result->where('product_id', $params['product_id']);

    if(isset($params['storage_location_id'])) {
        $result->where('storage_location_id', $params['storage_location_id']);
    } else {
        $result->where('storage_location_id', null);
    }

    // if(isset($params['lot_no']))
    //     $result->where('lot_no', $params['lot_no']);

    // if(isset($params['expiry_date']))
    //     $result->where('expiry_date', $params['expiry_date']);

    // if(isset($params['received_date']))
    //     $result->where('received_date', $params['received_date']);

    // if(isset($params['manufacture_date']))
    //     $result->where('manufacture_date', $params['manufacture_date']);

    if(isset($params['rcv_dtl_id']))
        $result->where('rcv_dtl_id', $params['rcv_dtl_id']);

    if(isset($params['master_id']))
        $result->where('id', $params['master_id']);

    $record = $result->get();

    if ($record->count() > 0) {
        return true;
    } else {
        return false;
    }
}

function hasDispatch($wd_no) {
    $hasDispatch = DB::table('dispatch_dtl')->where('wd_no', $wd_no)
        ->get();

    if ($hasDispatch->count() > 0) {
        return true;
    } else {
        return false;
    }
}

function _hasPo($po_num) {
    $hasPo = DB::table('po_hdr')->where('po_num', $po_num)
        ->get();

    if ($hasPo->count() > 0) {
        return true;
    } else {
        return false;
    }
}

function generateSeries($type)
{
    $data = SeriesModel::where('trans_type', '=', $type)->where('created_at', '>=', date('Y-m-01 00:00:00'))->where('created_at', '<=', date('Y-m-d 23:59:59'));
    $count = $data->count();
    $count = $count + 1;
    $date = date('ym');

    $prefix = $type."-";

    if($type == 'RCV') {
        $prefix = 'R-';
    }

    $num = str_pad((int)$count, 5, "0", STR_PAD_LEFT);

    $series = $prefix . $date . "-" . $num;

    return $series;
}

function timeInterval($start,$end){
    $startTime = new DateTime($start);
    $endTime = new DateTime($end);
    $interval = $startTime->diff($endTime);
    $minutes = $interval->i + ($interval->h * 60);
    $days = floor($minutes / (24 * 60));
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;
    $text = '';

    if ($days > 0) {
        $text .= $days . ' day';
        if ($days > 1) {
            $text .= 's '; // Pluralize "day" if it's more than 1 day
        }
        $text .= ' ';
    }

    if ($hours > 0) {
        $text .= $hours . ' hr';
        if ($hours > 1) {
            $text .= 's '; // Pluralize "hour" if it's more than 1 hour
        }
    }

    if ($remainingMinutes > 0){
        $text .= $remainingMinutes . ' min';
        if ($remainingMinutes > 1) {
            $text .= 's'; // Pluralize "minute" if it's more than 1 minute
        }
    }
    return $text;
}

function paginate($data, $perPage = 10, $page = null, $options = [])
{
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    //Create a new Laravel collection from the array data
    $collection = new Collection($data);

    //Define how many items we want to be visible in each page

    //Slice the collection to get the items to display in current page
    $currentPageResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

    //Create our paginator and add it to the data array
    $data['results'] = new LengthAwarePaginator($currentPageResults, count($collection), $perPage);

    //Set base url for pagination links to follow e.g custom/url?page=6
    return $data['results']->setPath(request()->url());
}


function getWeeksInMonth($year, $month) {
    $weeksInMonth = [];

    // Create Carbon instances for the first and last day of the specified month
    $firstDayOfMonth = Carbon::create($year, $month, 1);
    $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

    // Calculate the number of weeks in the month
    $totalWeeks = $firstDayOfMonth->diffInWeeks($lastDayOfMonth) + 1;

    // Iterate through each week and get start and end dates
    $currentDate = $firstDayOfMonth->copy();
    for ($weekNumber = 1; $weekNumber <= $totalWeeks; $weekNumber++) {
        $startOfWeek = $currentDate->copy();
        $endOfWeek = $startOfWeek->copy()->endOfWeek()->subDay();

        $end_date = $endOfWeek->addWeek()->toDateString();
        if ($end_date > $lastDayOfMonth) {
            $end_date = $lastDayOfMonth->toDateString();
        }
        $weeksInMonth[] = [
            'week_number' => $weekNumber,
            'start_date' => $startOfWeek->toDateString(),
            'end_date' => $end_date,
        ];

        $currentDate->addWeek();
    }

    return $weeksInMonth;
}

function _getWarehouseDtl($warehouse_id) {
    $warehouse = DB::table('warehouses')->where('id', $warehouse_id)->first();
    if ($warehouse) {
        return $warehouse;
    } else {
        return false;
    }
}

function getStorageLocation($location_id) {
    $location = DB::table('storage_locations')->where('storage_location_id', $location_id)->first();

    $storage_location = '';
    if($location) {
        $storage_location = $location->location;
    }
    return $storage_location;

}
function _version() {
    return "v-240901-1.0";
}

function getItemType($code) {
    $item_type = DB::table('item_type')->where('code', $code)->first();

    if ($item_type) {
        return $item_type->name;
    } else {
        return '';
    }
}
