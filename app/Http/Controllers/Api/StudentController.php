<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function show($id)
    {
        $student = Student::with('department')
                ->find($id)
                ->toArray();
        if (!$student) {
            return show(0, '该学生不存在～～', [], 404);
        }

        $sex_mapping = config('common.sex_mapping');
        return show(1, '请求成功～～', [
            'id' => $student['id'],
            'name' => $student['name'],
            'stuNo' => $student['stuNo'],
            'sex' => $sex_mapping[$student['sex']],
            'department' => $student['department']['name'],
            'created_at' => $student['created_at']
        ]);
    }

    public function editPassword(Request $request)
    {
        $id = $request->post('id', '');
        $password = $request->post('password', '');
        if (!$id) {
            return show(0, '未知的学生～～', [], 404);
        }
        if (!$password) {
            return show(0,'请输入密码~~', [], 404);
        }
        $student = Student::find($id);
        if (!$student) {
            return show(0, '没有该学生~~', [], 404);
        }
        $password = md5($password . md5($student->salt));
        $student->password = $password;
        if ($student->save()) {
            return show(1, '修改成功~~', [], 202);
        }
        return show(0, '修改失败', [], 500);
    }
}
