<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Http\Services\AesService;
use App\Models\Student;
use Closure;

class CheckAppLogin
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
        $res = $this->isLogin($request);
        if (!$res) {
            throw new ApiException('你没有登录～～', 401);
        }
        $request->attributes->add(['student' => $res]);

        return $next($request);
    }

    public function isLogin($request)
    {
        $access_token = $request->headers->get('accessToken');
        if (empty($access_token)) {
            return false;
        }
//        $access_token = AesService::opensslDecrypt($access_token);

        if (empty($access_token)) {
            return false;
        }
//        list($token, $id) = explode('||', $access_token);
        $student = Student::where('token', $access_token)
//                ->where('id', $id)
                ->first();
        if (!$student || !$student->status) {
            return false;
        }
//        判断token是否过期
        if (time() > $student->expired_at) {
            return false;
        }

        return $student;
    }
}
