<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends BaseController
{
    public function __construct()
    {
//        $this->checkPolicy('admin');
    }

    public function list()
    {
        $departments = Department::all();

        return view('admin/teacher/index', compact('departments'));
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
            ->with('department')
            ->take($pageSize)
            ->get()
            ->toArray();
        foreach ($data as $key => $item) {
            $data[$key]['department'] = $item['department']['name'];
        }

        return [
            'total' => $total,
            'data' => $data,
            'code' => 1
        ];
    }

    public function store(Request $request)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'teacherNo' => 'required|max:32|unique:teachers',
            'name' => 'required|max:20',
            'sex' => 'required',
            'password' => 'required',
            'department_id' => 'required'
        ], [
            'teacherNo.required' => '请输入教师工号～～',
            'teacherNo.max' => '请输入符合规范的教师工号～～',
            'teacherNo.unique' => '请输入符合规范的教师工号～～',
            'name.*' => '请输入符合规范的姓名～～',
            'sex.required' => '请选择性别～～',
            'password.required' => '请输入登录密码～～',
            'department_id.required' => '请选择系别～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $teacher = new Teacher();
        $input = $request->post();
        $teacher->teacherNo = $input['teacher'];
        $teacher->name = $input['name'];
        $teacher->department_id = $input['department_id'];
        $teacher->salt = makeSalt();
        $teacher->password = md5($input['password'] . md5($teacher->salt));
        $teacher->save();

        return formatResponse('操作成功～～', [], 1);
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'teacherNo' => 'required|max:32|unique:teachers,teacherNo,' . $id,
            'name' => 'required|max:20',
            'sex' => 'required|numeric',
            'department_id' => 'required'
        ], [
            'teacherNo.required' => '请输入教师工号～～',
            'teacherNo.max' => '请输入符合规范的教师工号～～',
            'teacherNo.unique' => '请输入符合规范的教师工号～～',
            'name.*' => '请输入符合规范的姓名～～',
            'sex.*' => '请选择性别～～',
            'department_id.requried' => '请选择系别～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $teacher = Teacher::findOrFail($id);
        $input = $request->post();
        $teacher->teacherNo = $input['teacherNo'];
        $teacher->name = $input['name'];
        $teacher->department_id = $input['department_id'];
        $teacher->sex = $input['sex'];
        $teacher->save();

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
        $teachers = Teacher::findMany($ids);
        if (!$teachers) {
            return formatResponse('教师不存在～～');
        }
        foreach ($teachers as $item) {
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
        $teachers = Teacher::findMany($ids);
        if (!$teachers) {
            return formatResponse('管理员不存在～～');
        }
        foreach ($teachers as $item) {
            $item->salt = config('common.default_salt');
            $item->password = md5(123456 . md5($item->salt));
            $item->save();
        }

        return formatResponse('重置成功～～', [], 1);
    }

    public function delete(Request $request)
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
        $teachers = Teacher::findMany($ids);
        if (!$teachers) {
            return formatResponse('管理员不存在～～');
        }
        Teacher::whereIn('id', $ids)->delete();

        return formatResponse('删除成功～～');
    }
}
