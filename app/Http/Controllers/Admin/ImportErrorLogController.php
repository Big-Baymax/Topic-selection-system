<?php

namespace App\Http\Controllers\Admin;

use App\Models\ImportErrorLog;
use Illuminate\Http\Request;

class ImportErrorLogController extends BaseController
{
    public function __construct()
    {
        $this->checkPolicy('admin');
    }

    public function list(Request $request)
    {
        $input = $request->all();
        $pageNumber = array_get($input, 'pageNumber', 1);
        $pageSize = array_get($input, 'pageSize', config('common.page_size'));
        $sortName = array_get($input, 'sortName', '');
        $sortOrder = array_get($input, 'sortOrder', 'desc');
        $query = ImportErrorLog::query();
        $table = array_get($input, 'table', '');
        if (!in_array($table, ['students', 'teachers'])) {
            return formatResponse('请选择要获取学生还是老师的错误导入日志～～');
        }
        if ($sortName) {
            $query->orderBy($sortName, $sortOrder);
        }

        $importErrorLogs = $query->where('table', $table)
                ->select('list', 'created_at')
                ->get()
                ->toArray();
        if (!$importErrorLogs) {
            return formatResponse('无数据', $importErrorLogs, 0);
        }
        $error_logs = $logs_count = $data = [];
        foreach ($importErrorLogs as $item) {
            $error_logs[$item['list']][] = $item;
        }
        foreach ($error_logs as $key => $val) {
            $logs_count[$key]['created_at'] = $val[0]['created_at'];
            $logs_count[$key]['list'] = $val[0]['list'];
            $logs_count[$key]['error_count'] = count($val);
        }
        foreach ($logs_count as $item) {
            $data[] = $item;
        }
        $offset = ($pageNumber - 1) * $pageSize;
        for ($i = $offset; $i < $offset + $pageSize; $i++) {
            if ($i < count($data)) {
                $pageData[] = $data[$i];
            }
        }

        return [
            'code' => 1,
            'data' => $pageData,
            'total' => count($pageData)
        ];
    }

    public function manage(Request $request)
    {
        $table = $request->post('table', '');
        $list = $request->post('list', '');
        if (!in_array($table, ['teachers', 'students'])) {
            return formatResponse('请传递要处理的是学生还是老师～～');
        }
        if (!$list) {
            return formatResponse('未传递list参数～～');
        }

        ImportErrorLog::where('table', $table)
            ->where('list', $list)
            ->delete();

        return formatResponse('操作成功～～', [], 1);
    }
}
