<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends BaseController
{
    public function __construct()
    {
        $this->checkPolicy('admin');
    }

    public function timeSet(Request $request)
    {
        $setting = DB::table('setting')->first();

        if ($request->isMethod('get')) {
            return view('admin/setting/time', compact('setting'));
        }
        $begin_time = $request->post('begin_time', '');
        $end_time = $request->post('end_time', '');
        $act = $request->post('act', '');
        if ($act && $act == 'reset') {
            DB::table('setting')->update([
                'begin_time' => null,
                'end_time' => null
            ]);
        } else {
            if (!$begin_time) {
                return formatResponse('请输入开始时间～～');
            }
            if (!$end_time) {
                return formatResponse('请输入结束时间～～');
            }
            if (strtotime($begin_time) >= strtotime($end_time)) {
                return formatResponse('开始时间不能大于结束时间～～');
            }
            DB::table('setting')->update([
                'begin_time' => $begin_time,
                'end_time' => $end_time
            ]);
        }



        return formatResponse('操作成功～～', [], 1);
    }

    public function quantitySet(Request $request)
    {
        $setting = DB::table('setting')->first();
        if ($request->isMethod('get')) {
            return view('admin/setting/topic-set', compact('setting'));
        }
        $quantity = $request->post('quantity', '');
        if (!$quantity) {
            return formatResponse('请输入教师选题限定数量～～');
        }
        if (!is_numeric($quantity) || $quantity <= 0) {
            return formatResponse('请输入符合规范的教师选题限定数量～～');
        }
        DB::table('setting')->update(['quantity' => $quantity]);

        return formatResponse('操作成功～～', [], 1);
    }
}
