<?php

use Illuminate\Support\Facades\DB;

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
            ->where('product_id', $params['product_id']);

            if(isset($params['storage_location_id'])) 
                $updateData->where('storage_location_id', $params['storage_location_id']);
            else
                $updateData->where('storage_location_id', null);

            // if(isset($params['lot_no']))
            //     $updateData->where('lot_no', $params['lot_no']);
        
            // if(isset($params['expiry_date']))
            //     $updateData->where('expiry_date', $params['expiry_date']);

            // if(isset($params['manufacture_date']))
            //     $updateData->where('manufacture_date', $params['manufacture_date']);

            if(isset($params['rcv_dtl_id']))
                $updateData->where('rcv_dtl_id', $params['rcv_dtl_id']);
            
            $record = $updateData->first();

            DB::table('masterdata')
                ->where('id', $record->id)
                ->update([
                    'inv_qty' => DB::raw('inv_qty + '.$params['inv_qty']),
                    'whse_qty' => DB::raw('whse_qty + '.$params['whse_qty']),
                ]);

        } else {
            //insert MASTERDATA
            $insert_data[] = array(
                'company_id'=>$params['company_id'],
                'customer_id'=>$params['customer_id'],
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
                'rcv_dtl_id'=>isset($params['rcv_dtl_id']) ? $params['rcv_dtl_id'] : null
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
                ->where('product_id', $params['product_id']);

            if(isset($params['storage_location_id'])) {
                $updateData->where('storage_location_id', $params['storage_location_id']);
            } else {
                $updateData->where('storage_location_id', null);
            }
                

            // if(isset($params['lot_no']))
            //     $updateData->where('lot_no', $params['lot_no']);
        
            // if(isset($params['expiry_date']))
            //     $updateData->where('expiry_date', $params['expiry_date']);

            // if(isset($params['manufacture_date']))
            //     $updateData->where('manufacture_date', $params['manufacture_date']);

            if(isset($params['rcv_dtl_id']))
                $updateData->where('rcv_dtl_id', $params['rcv_dtl_id']);
            
            $record = $updateData->first();

            
            //update MASTERDATA
            DB::table('masterdata')
                ->where('id', $record->id)
                ->update([
                    'inv_qty' => DB::raw('inv_qty - '.$params['inv_qty']),
                    'whse_qty' => DB::raw('whse_qty - '.$params['whse_qty']),
                    'reserve_qty' => DB::raw('reserve_qty - '.$params['inv_qty']),
                ]);
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