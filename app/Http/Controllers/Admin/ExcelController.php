<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\ImportErrorLogService;
use App\Models\Department;
use App\Models\ImportErrorLog;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends BaseController
{
    public function import(Request $request)
    {
        $students_data = $request->file('students');
        if (!$students_data) {
            return formatResponse('上传失败～～');
        }
        $ext = $students_data->getClientOriginalExtension();
        if (!in_array($ext, config('common.excel_ext'))) {
            return formatResponse('请选择正确的文件类型～～');
        }

        $store_path = storage_path() . '/app/public/excels';
        if (!is_dir($store_path)) {
            mkdir($store_path);
            chmod($store_path, 0777);
        }
        $filePath = $students_data->storeAs('/public/excels', md5(time()) . '.xls');
        $filePath = storage_path() . '/app/' . $filePath;
        chmod($filePath, 0777);
        $success_count = $error_count = $flag = 0;
        Excel::load($filePath, function($reader) use ($filePath, &$success_count, &$error_count, &$flag, &$errorData) {
            $insertData = [];
            $rawCollection = $reader->all()->toArray();
            if (!$rawCollection) {
                return false;
            }
            $student_mapping = Student::get()
                    ->pluck('id', 'stuNo')
                    ->toArray();
            $department_mapping = Department::get()
                    ->pluck('id', 'name')
                    ->toArray();
            $sex_mapping = array_flip(config('common.sex_mapping'));
//            初筛
            foreach ($rawCollection as $key => $cellCollection) {
                if (!$cellCollection) {
                    unset($rawCollection[$key]);
                    continue;
                }
                foreach ($cellCollection as $key1 => $item) {
                    switch ($key1) {
                        case '学号':
                            $insertData[$key]['stuNo'] = trim($item);
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
                'reason' => ''
            ];
            foreach ($insertData as $key => $item) {
                if (!$item['department_id'] || !$item['stuNo'] || !$item['name']) {
                    $fields['reason'] = '学号、姓名、系别不能为空';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $fields);
                    $errorData[$error_count]['error_raw'] = $key + 2;
                    $errorData[$error_count]['error_msg'] = '学号、姓名、系别不能为空';
                    $error_count += 1;
                    unset($insertData[$key]);
                    continue;
                }
                if (!in_array($item['department_id'], array_keys($department_mapping))) {
                    $fields['reason'] = '系别填写有误';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $fields);
                    $errorData[$error_count]['error_raw'] = $key + 2;
                    $errorData[$error_count]['error_msg'] = '系别填写有误';
                    $error_count += 1;
                    unset($insertData[$key]);
                    continue;
                }
                if (in_array($item['stuNo'], array_keys($student_mapping))) {
                    $fields['reason'] = '学号已存在';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $fields);
                    $errorData[$error_count]['error_raw'] = $key + 2;
                    $errorData[$error_count]['error_msg'] = '学号已存在';
                    $error_count += 1;
                    unset($insertData[$key]);
                    continue;
                }
                if (strlen($item['stuNo'] > 32) || strlen($item['name']) > 20) {
                    $fields['reason'] = '学号或姓名过长';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $fields);
                    unset($insertData[$key]);
                    continue;
                }
                if (!in_array($item['sex'], array_keys($sex_mapping))) {
                    $fields['reason'] = '性别填写有误';
                    $error_list = self::makeErrorResult($error_list, $error_count, $item, $fields);
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

            DB::table('students')->insert($insertData);
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

    public function studentImport()
    {

    }

    public function teacherImport()
    {

    }

    private static function makeErrorResult($error_list, $error_count, $item, $fields)
    {
        $error_list[$error_count] = [
            'stuNo' => $item['stuNo'],
            'table' => 'students',
            'name' => $item['name'],
            'department' => $item['department_id'],
            'sex' => $item['sex'],
            'reason' => $fields['reason'],
            'created_at' => $fields['created_at'],
            'list' => $fields['list']
        ];

        return $error_list;
    }

    public function export(Request $request)
    {
        $list = $request->get('list', '');
        if (!$list) {
            return formatResponse('请选择要下载的错误日志～～');
        }
        $import_error_logs = ImportErrorLog::where('list', $list)
                ->select(['stuNo', 'name', 'department', 'sex', 'reason'])
                ->get()
                ->toArray();
        if (!$import_error_logs) {
            return formatResponse('该日志不存在～～');
        }
        $error_list = [
            ['学号', '姓名', '系别', '性别', '原因']
        ];
        foreach ($import_error_logs as $key => $error_log) {
            $error_list[$key+1] = [
                $error_log['stuNo'],
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
