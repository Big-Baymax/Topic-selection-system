<?php
namespace App\Http\Services;


class IAuth
{
    /**
     * 设置登录的token  - 唯一性的
     * @param string $phone
     * @return string
     */
    public static function setAppLoginToken($stuNo = '') {
        $str = md5(uniqid(md5(microtime(true)), true));
        $str = sha1($str.$stuNo);
        return $str;
    }
}