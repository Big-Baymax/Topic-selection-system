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
        $old_pwd = $request->post('old_pwd', '');
        $new_pwd = $request->post('new_pwd', '');
        if (!$id) {
            return show(0, '未知的学生～～', [], 404);
        }
        if (!$old_pwd) {
            return show(0,'请输入旧密码~~', [], 404);
        }
        if (!$new_pwd) {
            return show(0,'请输入新密码~~', [], 404);
        }
        $student = Student::find($id);
        if (!$student) {
            return show(0, '没有该学生~~', [], 404);
        }
        $tmp_password = md5($old_pwd . md5($student->salt));
        if ($tmp_password != $student->password) {
            return show(0, '旧密码输入错误～～', [], 404);
        }
        $password = md5($new_pwd . md5($student->salt));
        $student->password = $password;
        if ($student->save()) {
            return show(1, '修改成功~~', [], 202);
        }
        return show(0, '修改失败', [], 500);
    }

    public function logout(Request $request)
    {
        $id = $request->post('id');
        if (!$id) {
            return show(0, '未知的学生～～', [], 404);
        }
        $student = Student::find($id);
        if (!$student) {
            return show(0, '该学生不存在～～', [], 404);
        }
        $student->expired_at = time();
        if ($student->save()) {
            return show(1, '注销成功~~', [], 202);
        }
        return show(0, '注销失败~~', [], 500);
    }
}
