<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected function validateMiddle($input, $rules, $messages)
    {
        $validator = $this->getValidationFactory()->make($input, $rules, $messages);
        if ($validator->fails()) {
            return response([
                'code' => 0,
                'msg' => $validator->errors()->first()
            ]);
        }
        return false;
    }
}
