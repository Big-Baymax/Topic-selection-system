<?php

namespace App\Http\Services;

/**
 * aes 加密 解密类库
 * @by singwa
 * Class Aes
 * @package app\common\lib
 */
class AesService
{
    /**
     * [opensslDecrypt description]
     * 使用openssl库进行加密
     * @param  [type] $sStr
     * @param  [type] $sKey
     * @return [type]
     */
    public static function opensslEncrypt($sStr, $method = 'AES-128-ECB'){
        $str = openssl_encrypt($sStr, $method, config('common.aes_key'));
        return $str;
    }
    /**
     * [opensslDecrypt description]
     * 使用openssl库进行解密
     * @param  [type] $sStr
     * @param  [type] $sKey
     * @return [type]
     */
    public static function opensslDecrypt($sStr, $method = 'AES-128-ECB'){
        $str = openssl_decrypt($sStr, $method, config('common.aes_key'));
        return $str;
    }
}