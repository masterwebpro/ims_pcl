<?php


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