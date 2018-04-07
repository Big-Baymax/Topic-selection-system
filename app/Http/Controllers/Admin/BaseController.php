<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected function validateMiddle($input, $rules, $messages)
    {
        $validator = $this->getValidationFactory()->make($input, $rules, $messages);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return false;
    }
}
