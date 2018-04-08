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

    public function create()
    {
        return view('admin/student/add');
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

    public function edit($id)
    {
        $student = Student::find($id);

        return [
            'code' => 1,
            'data' => $student
        ];
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'stuNo' => 'required|max:32|unique:students,stuNo,' . $id,
            'name' => 'required|max:20',
            'sex' => 'required|numeric'
        ], [
            'stuNO.required' => '请输入教师工号～～',
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
            'id' => 'required',
            'act' => 'required|in:recover,remove'
        ], [
            'id.required' => '请选择要操作的对象～～',
            'act.*' => '操作有误～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $act = $request->post('act');
        $id = $request->post('id');
        $student = Student::find($id);
        if (!$student) {
            return formatResponse('该教师不存在～～');
        }
        switch ($act) {
            case 'recover':
                $student->status = 1;
                $act = '恢复成功～～';
                break;
            case 'remove':
                $student->status = 0;
                $act = '禁用成功～～';
                break;
        }

        return formatResponse($act, [], 1);
    }
}
