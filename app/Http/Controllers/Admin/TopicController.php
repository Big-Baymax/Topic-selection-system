<?php

namespace App\Http\Controllers\Admin;

use App\Models\StudentTopicLogs;
use App\Models\Topic;
use App\Models\TopicCategory;
use Illuminate\Http\Request;

class TopicController extends BaseController
{
    public function __construct()
    {
        $this->checkPolicy('teacher');
    }

    public function list()
    {
        $categories = TopicCategory::all();

        return view('admin/topic/index', compact('categories'));
    }

    public function index(Request $request)
    {
        $user = $request->attributes->get('user');
        $input = $request->all();
        $pageNumber = array_get($input, 'pageNumber', 1);
        $pageSize = array_get($input, 'pageSize', config('common.page_size'));
        $sortName = array_get($input, 'sortName', '');
        $sortOrder = array_get($input, 'sortOrder', 'desc');
        $searchText = array_get($input, 'searchText', '');
        $query = Topic::query();
        if ($searchText) {
            $query->Where('name', 'like', '%' . $searchText . '%');
        }
        if ($sortName) {
            $query->orderBy($sortName, $sortOrder);
        }
        $total = $query->where('department_id', $user->department_id)->count();
        $data = $query->offset(($pageNumber - 1) * $pageSize)
            ->with(['student', 'category', 'teacher'])
            ->take($pageSize)
            ->get()
            ->toArray();
        $topic_status_mapping = config('common.topic_status');
        foreach ($data as $key => $item) {
            $data[$key]['category'] = $item['category']['name'];
            $data[$key]['teacher'] = $item['teacher']['name'];
            if ($item['student']) {
                $data[$key]['student'] = $item['student']['name'];
            }
            $data[$key]['status'] = $topic_status_mapping[$item['status']];
            $data[$key]['current_teacher_id'] = $user->id;
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
            'name' => 'required|max:20|unique:topics',
            'category_id' => 'required|numeric',
            'description' => 'required'
        ], [
            'name.max' => '请输入符合规范的选题名称～～',
            'name.required' => '请输入选题名称～～',
            'name.unique' => '选题名称已存在～～',
            'category.*' => '请选择分类～～',
            'description.required' => '请输入选题描述'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $user = $request->attributes->get('user');
        $current_quantity = Topic::where('teacher_id', $user->id)->count();
        $limit_quantity = $this->getSettingQuantity();
        if ($current_quantity >= $limit_quantity) {
            return formatResponse('每个老师最多只能添加' . $limit_quantity . '个选题～～');
        }
        $teacher = $request->attributes->get('user');
        $topic = new Topic();
        $input = $request->post();
        $topic->name = $input['name'];
        $topic->category_id = $input['category_id'];
        $topic->description = $input['description'];
        $topic->teacher_id = $teacher->id;
        $topic->department_id = $teacher->department_id;
        $topic->status = 1;
        $topic->save();

        return formatResponse('操作成功～～', [], 1);
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'name' => 'required|max:20|unique:topics,name,' . $id,
            'category_id' => 'required|numeric',
            'description' => 'required'
        ], [
            'name.max' => '请输入符合规范的选题名称～～',
            'name.required' => '请输入选题名称～～',
            'name.unique' => '选题名称已存在～～',
            'category.*' => '请选择分类～～',
            'description.required' => '请输入选题描述'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $user = $request->attributes->get('user');
        $topic = Topic::findOrFail($id);
        if ($topic->teacher_id != $user->id) {
            return formatResponse('只能修改自己的选题～～');
        }
        $input = $request->post();
        $topic->name = $input['name'];
        $topic->category_id = $input['category_id'];
        $topic->description = $input['description'];
        $topic->save();

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
        $topics = Topic::findMany($ids);
        $user = $request->attributes->get('user');
        if (!$topics) {
            return formatResponse('选题不存在～～');
        }
        $failed_delete_student = $failed_delete_teacher = '';
        $success_delete = 0;
        $failed_delete_id = [];
        foreach ($topics as $topic) {
            if ($topic->teacher_id != $user->id) {
                $failed_delete_teacher .= $topic->name . ' ';
                continue;
            } else if ($topic->student) {
                $failed_delete_student .= $topic->name . ' ';
                $failed_delete_id[] = $topic->id;
                continue;
            } else {
                $success_delete += 1;
                $topic->delete();
            }
        }
        $response_msg = $success_delete ? '删除成功～～' : '删除失败～～';
        $response_msg .= "<br>";
        if ($failed_delete_teacher) {
            $response_msg .= $failed_delete_teacher . '不是您的选题，无法删除' . "<br>";
        }
        if ($failed_delete_student) {
            $response_msg .= $failed_delete_student . '已被学生选中，无法删除' . "<br>";
        }

        $res = mb_substr($response_msg, 0, 4, 'UTF-8');
        return formatResponse($response_msg, $failed_delete_id, $res == '删除成功' ? 1 : 0);
    }

    public function ops(Request $request)
    {
        $id = $request->post('id', '');
        $act = $request->post('act', '');
        if (!$id) {
            return formatResponse('请选择要操作的选题～～');
        }
        if (!in_array($act, ['pass', 'fail'])) {
            return formatResponse('未知的操作～～');
        }
        $topic = Topic::find($id);
        if (!$topic) {
            return formatResponse('没有该选题～～');
        }
        $student_topic_log = StudentTopicLogs::where('topic_id', $id)
                ->where('student_id', $topic->student_id)
                ->orderBy('create_at', 'desc')
                ->first();
        if ($act == 'pass') {
            $topic->status = 3;
            $student_topic_log->status = 2;
        } else {
            $topic->student_id = 0;
            $topic->status = 1;
            $student_topic_log->status = 3;
        }
        $topic->save();
        $student_topic_log->save();

        return formatResponse('操作成功～～', [], 1);
    }
}
