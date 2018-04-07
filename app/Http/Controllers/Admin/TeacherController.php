<?php

namespace App\Http\Controllers\Admin;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends BaseController
{
    public function __construct()
    {
        $this->checkPolicy('admin');
    }

    public function list()
    {
        return view('admin/teacher/index');
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $pageNumber = array_get($input, 'pageNumber', 1);
        $pageSize = array_get($input, 'pageSize', config('common.page_size'));
        $sortName = array_get($input, 'sortName', '');
        $sortOrder = array_get($input, 'sortOrder', 'desc');
        $searchText = array_get($input, 'searchText', '');
        $query = Teacher::query();
        if ($searchText) {
            $query->where('teacherNo', 'like', '%' . $searchText . '%')
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
        return view('admin/teacher/add');
    }

    public function store(Request $request)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'teacherNo' => 'required|max:32|unique:teachers',
            'name' => 'required|max:20',
            'sex' => 'required',
            'password' => 'required',
        ], [
            'teacherNo.required' => '请输入教师工号～～',
            'teacherNo.max' => '请输入符合规范的教师工号～～',
            'teacherNo.unique' => '请输入符合规范的教师工号～～',
            'name.*' => '请输入符合规范的姓名～～',
            'sex.required' => '请选择性别～～',
            'password.required' => '请输入登录密码～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $teacher = new Teacher();
        $input = $request->post();
        $teacher->teacherNo = $input['teacher'];
        $teacher->name = $input['name'];
        $teacher->salt = makeSalt();
        $teacher->password = md5($input['password'] . md5($teacher->salt));
        $teacher->save();

        return formatResponse('操作成功～～', [], 1);
    }

    public function edit($id)
    {
        $teacher = Teacher::find($id);

        return [
            'code' => 1,
            'data' => $teacher
        ];
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'teacherNo' => 'required|max:32|unique:teachers,teacherNo,' . $id,
            'name' => 'required|max:20',
            'sex' => 'required|numeric'
        ], [
            'teacherNo.required' => '请输入教师工号～～',
            'teacherNo.max' => '请输入符合规范的教师工号～～',
            'teacherNo.unique' => '请输入符合规范的教师工号～～',
            'name.*' => '请输入符合规范的姓名～～',
            'sex.*' => '请选择性别～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $teacher = Teacher::findOrFail($id);
        $input = $request->post();
        $teacher->teacherNo = $input['teacherNo'];
        $teacher->name = $input['name'];
        $teacher->sex = $input['sex'];
        $teacher->save();

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
        $teacher = Teacher::find($id);
        if (!$teacher) {
            return formatResponse('该教师不存在～～');
        }
        switch ($act) {
            case 'recover':
                $teacher->status = 1;
                $act = '恢复成功～～';
                break;
            case 'remove':
                $teacher->status = 0;
                $act = '禁用成功～～';
                break;
        }

        return formatResponse($act, [], 1);
    }
}
