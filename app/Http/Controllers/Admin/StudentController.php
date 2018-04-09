<?php

namespace App\Http\Controllers\Admin;

use App\Models\ImportErrorLog;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends BaseController
{
    public function __construct()
    {
//        $this->checkPolicy('admin');
    }

    public function list()
    {
        return view('admin/student/index');
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $pageNumber = array_get($input, 'pageNumber', 1);
        $pageSize = array_get($input, 'pageSize', config('common.page_size'));
        $sortName = array_get($input, 'sortName', '');
        $sortOrder = array_get($input, 'sortOrder', 'desc');
        $searchText = array_get($input, 'searchText', '');
        $query = Student::query();
        if ($searchText) {
            $query->where('stuNo', 'like', '%' . $searchText . '%')
                ->orWhere('name', 'like', '%' . $searchText . '%');
        }
        if ($sortName) {
            $query->orderBy($sortName, $sortOrder);
        }
        $total = $query->count();
        $data = $query->offset(($pageNumber - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return [
            'total' => $total,
            'data' => $data,
            'code' => 1
        ];
    }

    public function store(Request $request)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'stuNo' => 'required|max:32|unique:students',
            'name' => 'required|max:20',
            'sex' => 'required',
            'password' => 'required',
        ], [
            'teacherNo.required' => '请输入学号～～',
            'teacherNo.max' => '请输入符合规范的学号～～',
            'teacherNo.unique' => '请输入符合规范的学号～～',
            'name.*' => '请输入符合规范的姓名～～',
            'sex.required' => '请选择性别～～',
            'password.required' => '请输入登录密码～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $student = new Student();
        $input = $request->post();
        $student->stuNo = $input['stuNo'];
        $student->name = $input['name'];
        $student->salt = makeSalt();
        $student->password = md5($input['password'] . md5($student->salt));
        $student->save();

        return formatResponse('操作成功～～', [], 1);
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'stuNo' => 'required|max:32|unique:students,stuNo,' . $id,
            'name' => 'required|max:20',
            'sex' => 'required|numeric'
        ], [
            'stuNo.required' => '请输入教师工号～～',
            'stuNo.max' => '请输入符合规范的教师工号～～',
            'stuNo.unique' => '请输入符合规范的教师工号～～',
            'name.*' => '请输入符合规范的姓名～～',
            'sex.*' => '请选择性别～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $student = Student::findOrFail($id);
        $input = $request->post();
        $student->stuNo = $input['stuNo'];
        $student->name = $input['name'];
        $student->sex = $input['sex'];
        $student->save();

        return formatResponse('操作成功～～', [], 1);
    }

    public function ops(Request $request)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'id' => 'required|array',
        ], [
            'id.required' => '请选择要操作的对象～～',
            'id.array' => '传递的ids有问题～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $ids = $request->post('id');
        $students = Student::findMany($ids);
        if (!$students) {
            return formatResponse('学生不存在～～');
        }
        foreach ($students as $item) {
            if ($item->status == 0) {
                $item->status = 1;
            } else {
                $item->status = 0;
            }
            $item->save();
        }

        return formatResponse('操作成功', [], 1);
    }

    public function resetPwd(Request $request)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'id' => 'required|array',
        ], [
            'id.required' => '请选择要操作的对象～～',
            'id.array' => '传递的ids有问题～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $ids = $request->post('id');
        $students = Student::findMany($ids);
        if (!$students) {
            return formatResponse('管理员不存在～～');
        }
        foreach ($students as $item) {
            $item->salt = config('common.default_salt');
            $item->password = md5(123456 . md5($item->salt));
            $item->save();
        }

        return formatResponse('重置成功～～', [], 1);
    }
}
