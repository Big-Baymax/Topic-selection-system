<?php

namespace App\Http\Middleware;

use App\Models\Administrator;
use App\Models\Student;
use App\Models\Teacher;
use Closure;

class checkAdminLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $res = $this->checkLogin($request);
        if (!$res) {
            if ($request->ajax()) {
                return response([
                    'msg' => '未登录，请先登录～～',
                    'code' => 1
                ]);
            } else {
                request()->session()->forget(config('common.admin_remember_session'));
                return response("<script>parent.location.href='/admin/login';</script>");
            }
        }

        $request->attributes->add(['user' => $res['user']]);
        \View::composer('admin.index', function($view) use ($res){
            $view->with([
                'user' => $res['user'],
                'identity' => $res['user']->is_admin ? '超级' . config('common.identity_mapping')[$res['identity']] : config('common.identity_mapping')[$res['identity']]
            ]);
        });

        return $next($request);
    }

    public function checkLogin($request)
    {
        $admin_user = $request->session()->get(config('admin_remember_session'));
        if (!isset($admin_user['admin_user']) || !$admin_user['admin_user']) {
            return false;
        }
        $admin_user = $admin_user['admin_user'];
        $id_token_arr = explode('#', $admin_user);
        if (count($id_token_arr) < 3) {
            return false;
        }
        $id = $id_token_arr[0];
        $identity = $id_token_arr[1];
        $auth_token = $id_token_arr[2];
        if (!preg_match("/^\d+$/", $id)) {
            return false;
        }
        switch ($identity) {
            case 1:
                $user = Administrator::find($id);
                break;
            case 2:
                $user = Teacher::with('department')->find($id);
                break;
        }
        if (!$user) {
            return false;
        }
        $tmp_auth_token = md5($user->password . $user->salt);
        if ($tmp_auth_token !== $auth_token) {
            return false;
        }

        return [
            'identity' => $identity,
            'user' => $user
        ];
    }
}
