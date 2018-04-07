<?php
function formatResponse($msg, $data = [], $code = 0)
{
    return [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
}

function makeSalt($length = 16)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+';
    $salt = '';
    for ($i = 0; $i < $length; $i++) {
        $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
    }

    return $salt;
}

function test()
{
    return response(111);
}