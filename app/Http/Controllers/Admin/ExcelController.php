<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\ImportErrorLogService;
use App\Models\Department;
use App\Models\ImportErrorLog;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends BaseController
{
    public function import(Request $request)
    {
        $from = $request->post('from', '');
        if (!in_array($from, ['students', 'teachers'])) {
            return formatResponse('来路不明的请求～～');
        }

        $data = $request->file($from);
        if (!$data) {
            return formatResponse('未获取到上传的东西～～');
        }
        $ext = $data->getClientOriginalExtension();
        if (!in_array($ext, config('common.excel_ext'))) {
            return formatResponse('请选择正确的文件类型～～');
        }

        $store_path = storage_path() . '/app/public/excels';
        if (!is_dir($store_path)) {
            mkdir($store_path);
            chmod($store_path, 0777);
        }
        $filePath = $data->storeAs('/public/excels', md5(time()) . '.xls');
        $filePath = storage_path() . '/app/' . $filePath;
        chmod($filePath, 0777);
        $success_count = $error_count = $flag = 0;

        Excel::load($filePath, function($reader) use ($filePath, &$success_count, &$error_count, &$flag, &$errorData, $from) {
            $insertData = [];
            $rawCollection = $reader->all()->toArray();
            if (!$rawCollection) {
                return false;
            }

            if ($from == 'students') {
                $user_mapping = Student::get()
                    ->pluck('id', 'stuNo')
                    ->toArray();
                $identity = '学号';
            } else {
                $user_mapping = Teacher::get()
                    ->pluck('id', 'teacherNo')
                    ->toArray();
                $identity = '教师工号';
            }
            $no = ($identity == '教师工号' ? 'teacherNo' : 'stuNo');

            $department_mapping = Department::get()
                ->pluck('id', 'name')
                ->toArray();
            $sex_mapping = array_flip(config('common.sex_mapping'));
//            初筛
            foreach ($rawCollection as $key => $cellCollection) {
                if (array_keys($cellCollection) !== [$identity, '姓名', '性别', '系别']) {
                    return false;
                }
                if (!$cellCollection) {
                    unset($rawCollection[$key]);
                    continue;
                }
                foreach ($cellCollection as $key1 => $item) {
                    switch ($key1) {
                        case $identity:
                            $insertData[$key][$no] = trim($item);
                            break;
                        case '姓名':
                            $insertData[$key]['name'] = trim($item);
                            break;
                        case '系别':
                            $insertData[$key]['department_id'] = trim($item);
                            break;
                        case '性别':
                            $insertData[$key]['sex'] = trim($item);
                            break;
                    }

                }
            }
//            复筛
            $success_count = $error_count = $i = 0;
            $error_list = [];
            $list_order = DB::table('import_error_logs')->max('list');
            $list_order = $list_order ? $list_order + 1 : 1;
            $now = date('Y-m-d H:i:s', time());
            $fields = [
                'created_at' => $now,
                'list' => $list_order,
                'table' => $from,
                'reason' => ''
            ];
            foreach ($insertData as $key => $item) {
                if (!$item['department_id'] || !$item[$no] || !$item['name']) {
                    $fields['reason'] = $identity . '、姓名、系别不能为空';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $no, $fields);
                    $errorData[$error_count]['error_raw'] = $key + 2;
                    $errorData[$error_count]['error_msg'] = '学号、姓名、系别不能为空';
                    $error_count += 1;
                    unset($insertData[$key]);
                    continue;
                }
                if (!in_array($item['department_id'], array_keys($department_mapping))) {
                    $fields['reason'] = '系别填写有误';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $no, $fields);
                    $errorData[$error_count]['error_raw'] = $key + 2;
                    $errorData[$error_count]['error_msg'] = '系别填写有误';
                    $error_count += 1;
                    unset($insertData[$key]);
                    continue;
                }
                if (in_array($item[$no], array_keys($user_mapping))) {
                    $fields['reason'] = $identity . '已存在';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $no, $fields);
                    $errorData[$error_count]['error_raw'] = $key + 2;
                    $errorData[$error_count]['error_msg'] = '学号已存在';
                    $error_count += 1;
                    unset($insertData[$key]);
                    continue;
                }
                if (strlen($item[$no] > 32) || strlen($item['name']) > 20) {
                    $fields['reason'] = $identity . '或姓名过长';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $no, $fields);
                    unset($insertData[$key]);
                    continue;
                }
                if (!in_array($item['sex'], array_keys($sex_mapping))) {
                    $fields['reason'] = '性别填写有误';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $no, $fields);
                    unset($insertData[$key]);
                    continue;
                }
                $insertData[$key]['sex'] = $sex_mapping[$item['sex']];
                $insertData[$key]['department_id'] = $department_mapping[$item['department_id']];
                $insertData[$key]['salt'] = config('common.default_salt');
                $insertData[$key]['password'] = md5(123456 . md5(config('common.default_salt')));
                $insertData[$key]['created_at'] = $now;
                $insertData[$key]['updated_at'] = $now;
            }

            DB::table($from)->insert($insertData);
            DB::table('import_error_logs')->insert($error_list);
            $success_count = count($insertData);
            unlink($filePath);
            $flag = true;
        });

        if ($flag) {
            return formatResponse("导入成功～～成功{$success_count}条，失败{$error_count}条", $errorData, 1);
        } else {
            return formatResponse('导入失败～～');
        }
    }


    private static function makeErrorResult($error_list, $error_count, $item, $no, $fields)
    {
        $error_list[$error_count] = [
            $no => $item[$no],
            'name' => $item['name'],
            'department' => $item['department_id'],
            'sex' => $item['sex'],
            'reason' => $fields['reason'],
            'created_at' => $fields['created_at'],
            'list' => $fields['list'],
            'table' => $fields['table']
        ];

        return $error_list;
    }

    public function export(Request $request)
    {
        $list = $request->get('list', '');
        $table = $request->get('table', '');
        if (!$list) {
            return formatResponse('请选择要下载的错误日志～～');
        }
        if (!in_array($table, ['students', 'teachers'])) {
            return formatResponse('未传递table参数');
        }
        if ($table == 'students') {
            $select = ['stuNo', 'name', 'department', 'sex', 'reason'];
            $error_list = [
                ['学号', '姓名', '系别', '性别', '原因']
            ];
            $no = 'stuNo';
        } else {
            $select = ['teacherNo', 'name', 'department', 'sex', 'reason'];
            $error_list = [
                ['教师工号', '姓名', '系别', '性别', '原因']
            ];
            $no = 'teacherNo';
        }
        $import_error_logs = ImportErrorLog::where('list', $list)
                ->where('table', $table)
                ->select($select)
                ->get()
                ->toArray();
        if (!$import_error_logs) {
            return formatResponse('该日志不存在～～');
        }

        foreach ($import_error_logs as $key => $error_log) {
            $error_list[$key+1] = [
                $error_log[$no],
                $error_log['name'],
                $error_log['department'],
                $error_log['sex'],
                $error_log['reason']
            ];
        }
        Excel::create('学生列表', function($excel) use ($error_list) {
            $excel->sheet('test', function($sheet) use ($error_list) {
                $sheet->setWidth(array(
                    'A'     =>  10,
                    'B'     =>  8,
                    'C'     =>  10,
                    'D'     =>  5,
                    'E'     =>  20,
                ));
                $sheet->fromArray($error_list, null, 'A1', false, false);
            });
        })->download('xls');
    }
}
