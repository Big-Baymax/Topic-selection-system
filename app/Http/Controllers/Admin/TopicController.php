<?php

namespace App\Http\Controllers\Admin;

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
        $total = $query->count();
        $data = $query->offset(($pageNumber - 1) * $pageSize)
            ->with(['student', 'category'])
            ->take($pageSize)
            ->get()
            ->toArray();
        $topic_status_mapping = config('common.topic_status');
        foreach ($data as $key => $item) {
            $data[$key]['category'] = $item['category']['name'];
            if ($item['student']) {
                $data[$key]['student'] = $item['student']['name'];
            }
            $data[$key]['status'] = $topic_status_mapping[$item['status']];
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
        $topic = Topic::findOrFail($id);
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
        if (!$topics) {
            return formatResponse('选题不存在～～');
        }
        $failed_delete = '';
        $success_delete = 0;
        $failed_delete_id = [];
        foreach ($topics as $topic) {
            if ($topic->student) {
                $failed_delete .= $topic->name . ' ';
                $failed_delete_id[] = $topic->id;
            } else {
                $success_delete += 1;
                $topic->delete();
            }
        }
        $response_msg = $success_delete ? '删除成功～～' : '删除失败～～';
        $response_msg .= $failed_delete ? $failed_delete . '已被学生选中，无法删除' : '';

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
        if ($act == 'pass') {
            $topic->status = 3;
        } else {
            $topic->student_id = 0;
            $topic->status = 1;
        }
        $topic->save();

        return formatResponse('操作成功～～', [], 1);
    }
}
