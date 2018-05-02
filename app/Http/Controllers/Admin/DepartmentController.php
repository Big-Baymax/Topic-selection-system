<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends BaseController
{
    public function __construct()
    {
        $this->checkPolicy('admin');
    }

    public function list()
    {
        return view('admin/department/index');
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $pageNumber = array_get($input, 'pageNumber', 1);
        $pageSize = array_get($input, 'pageSize', config('common.page_size'));
        $sortName = array_get($input, 'sortName', '');
        $sortOrder = array_get($input, 'sortOrder', 'desc');
        $searchText = array_get($input, 'searchText', '');
        $query = Department::query();
        if ($searchText) {
            $query->Where('name', 'like', '%' . $searchText . '%');
        }
        if ($sortName) {
            $query->orderBy($sortName, $sortOrder);
        }
        $total = $query->count();
        $data = $query
            ->offset(($pageNumber - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        foreach ($data as $key => $item) {
            $data[$key] = array_map(function ($v) {
                return htmlspecialchars($v);
            }, $data[$key]);
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
            'name' => 'required|max:20|unique:departments'
        ], [
            'name.max' => '请输入符合规范的系别名称～～',
            'name.required' => '请输入系别名称～～',
            'name.unique' => '系别已存在～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $department = new Department();
        $input = $request->post();
        $department->name = $input['name'];
        $department->save();

        return formatResponse('操作成功～～', [], 1);
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'name' => 'required|max:20|unique:departments,name,' . $id
        ], [
            'name.max' => '请输入符合规范的系别名称～～',
            'name.required' => '请输入系别名称～～',
            'name.unique' => '系别已存在～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $department = Department::findOrFail($id);
        $input = $request->post();
        $department->name = $input['name'];
        $department->save();

        return formatResponse('操作成功～～', [], 1);
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
        $departments = Department::findMany($ids);
        if (!$departments) {
            return formatResponse('系别不存在～～');
        }
        $failed_delete_of_student = $failed_delete_of_teacher = '';
        $success_delete = 0;
        $failed_delete_id = [];
        foreach ($departments as $department) {
            if ($department->students()->count() > 0) {
                $failed_delete_of_student .= $department->name . ' ';
                $failed_delete_id[] = $department->id;
                continue;
            }
            if ($department->teachers()->count() > 0) {
                $failed_delete_of_teacher .= $department->name . ' ';
                $failed_delete_id[] = $department->id;
                continue;
            }

            $success_delete += 1;
            $department->delete();
        }

        $response_msg = $success_delete ? '删除成功～～' : '删除失败～～';
        if ($failed_delete_of_student) {
            $response_msg .= $failed_delete_of_student . '有学生，无法删除';
        } else {
            if ($failed_delete_of_teacher) {
                $response_msg .= $failed_delete_of_teacher . '有老师，无法删除';
            }
        }

        $res = mb_substr($response_msg, 0, 4, 'UTF-8');
        return formatResponse($response_msg, $failed_delete_id, $res == '删除成功' ? 1 : 0);
    }
}
