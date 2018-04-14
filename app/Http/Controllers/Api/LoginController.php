<?php

namespace App\Http\Controllers\Api;

use App\Http\Services\AesService;
use App\Http\Services\IAuth;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if (!$request->isMethod('post')) {
            return show(0, '你没有权限～～', [], 403);
        }
        $input = $request->post();
        if (empty($input['stuNo'])) {
            return show(0, '学号不能为空～～', [], 404);
        }
        if (empty($input['password'])) {
            return show(0, '密码不能为空～～', [], 404);
        }
        $token = IAuth::setAppLoginToken($input['stuNo']);
        $student = Student::where('stuNo', $input['stuNo'])->first();
        if (!$student || !$student->status) {
            return show(0, '学号不存在或已被禁用～～', [], 403);
        }
        $tmp_password = md5($input['password'] . md5($student->salt));
        if ($tmp_password != $student->password) {
            return show(0, '密码错误～～', [], 403);
        }
        $student->token = $token;
        $student->expired_at = strtotime("+" . config('common.app_login_time_out_day') . " days");
        if ($student->save()) {
            $res = [
                'token' => AesService::opensslEncrypt($token . '||' . $student->id)
            ];
            return show(1, '登录成功～～', $res);
        } else {
            return show(0, '登录失败～～', [], 403);
        }
    }
}
