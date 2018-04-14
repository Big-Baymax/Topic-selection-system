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

/**
 * 通用化API接口数据输出
 * @param int $status 业务状态码
 * @param string $message 信息提示
 * @param [] $data  数据
 * @param int $httpCode http状态码
 * @return array
 */
function show($status, $message, $data=[], $httpCode=200) {

    $data = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];

    return response($data, $httpCode);
}