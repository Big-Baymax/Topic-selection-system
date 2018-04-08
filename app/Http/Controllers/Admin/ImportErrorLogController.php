<?php

namespace App\Http\Controllers\Admin;

use App\Models\ImportErrorLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportErrorLogController extends Controller
{
    public function list(Request $request)
    {
        $table = $request->get('table');
        if (!in_array($table, ['students', 'teachers'])) {
            return formatResponse('请选择要获取学生还是老师的错误导入日志～～');
        }
        $importErrorLogs = ImportErrorLog::where('table', $table)
                ->select('list', 'created_at')
                ->get()
                ->toArray();
        if (!$importErrorLogs) {
            return formatResponse('无数据', $importErrorLogs, 0);
        }
        $error_logs = $logs_count = [];
        foreach ($importErrorLogs as $item) {
            $error_logs[$item['list']][] = $item;
        }
        foreach ($error_logs as $key => $val) {
            $logs_count[$key]['created_at'] = $val[0]['created_at'];
            $logs_count[$key]['list'] = $val[0]['list'];
            $logs_count[$key]['error_count'] = count($val);
        }

        return formatResponse('请求成功～～', $logs_count, 1);
    }
}
