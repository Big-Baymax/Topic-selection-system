<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        if (\request()->session()->get(config('common.admin_remember_session'))) {
            $this->middleware('checkAdminLogin');
            return redirect('/admin/home');
        }
        return view('admin/check/login');
    }

    public function login(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->input(), [
            'login_name' => 'required',
            'password' => 'required',
            'identity' => 'required|in:' . implode(',', array_keys(config('common.identity_mapping')))
        ], [
            'login_name.required' => '请输入登录名～～',
            'password.required' => '请输入密码～～',
            'identity.required' => '请选择登录身份～～',
            'identity.in' => '身份错误～～'
        ]);
        if ($validator->fails()) {
            return formatResponse($validator->errors()->first());
        }
        $login_name = $request->post('login_name');
        $identity = $request->post('identity');
        switch ($identity) {
            case 1:
                $tmp_user = Administrator::where('login_name', $login_name)
                    ->where('status', 1)
                    ->first();
                    break;
            case 2:
                $tmp_user = Teacher::where('teacherNo', $login_name)
                    ->where('status', 1)
                    ->first();
                    break;
        }
        if (!$tmp_user) {
            return formatResponse('用户名密码不匹配～～');
        }
        $login_pwd = $request->post('password');
        $tmp_md5_password = md5($login_pwd . md5($tmp_user->salt));
        if ($tmp_md5_password !== $tmp_user->password) {
            return formatResponse('用户名密码不匹配～～');
        }

        session([config('common.admin_remember_session') => $tmp_user->id . '#' . $identity . '#' . md5($tmp_user->password . $tmp_user->salt)]);

        return formatResponse('登录成功～～', [
            'redirect_url' => '/admin/home'
        ], 1);
    }

    public function logout()
    {
        request()->session()->forget(config('common.admin_remember_session'));

        return redirect('admin/login');
    }
}
