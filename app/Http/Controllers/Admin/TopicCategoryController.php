<?php

namespace App\Http\Controllers\Admin;

use App\Models\Topic;
use App\Models\TopicCategory;
use Illuminate\Http\Request;

class TopicCategoryController extends BaseController
{
    public function __construct()
    {
//        $this->checkPolicy('admin');
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
        $query = TopicCategory::query();
        if ($searchText) {
            $query->Where('name', 'like', '%' . $searchText . '%');
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
        return view('admin/topicCategory/add');
    }

    public function store(Request $request)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'name' => 'required|max:20|unique:topic_categories',
            'weight' => 'required|numeric',
        ], [
            'name.max' => '请输入符合规范的分类名称～～',
            'name.required' => '请输入分类名称～～',
            'name.unique' => '分类已存在～～',
            'weight.*' => '请输入符合规范的权重～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $topic_category = new Topic();
        $input = $request->post();
        $topic_category->name = $input['name'];
        $topic_category->weight = $input['weight'];
        $topic_category->save();

        return formatResponse('操作成功～～', [], 1);
    }

    public function edit($id)
    {
        $teacher = TopicCategory::find($id);

        return [
            'code' => 1,
            'data' => $teacher
        ];
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'name' => 'required|max:20|unique:topic_categories,name,' . $id,
            'weight' => 'required|numeric',
        ], [
            'name.max' => '请输入符合规范的分类名称～～',
            'name.required' => '请输入分类名称～～',
            'name.unique' => '分类已存在～～',
            'weight.*' => '请输入符合规范的权重～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $topic_category = TopicCategory::findOrFail($id);
        $input = $request->post();
        $topic_category->name = $input['name'];
        $topic_category->weight = $input['weight'];
        $topic_category->save();

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
        $topic_category = TopicCategory::find($id);
        if (!$topic_category) {
            return formatResponse('该分类不存在～～');
        }
        switch ($act) {
            case 'recover':
                $topic_category->status = 1;
                $act = '恢复成功～～';
                break;
            case 'remove':
                $topic_category->status = 0;
                $act = '禁用成功～～';
                break;
        }

        return formatResponse($act, [], 1);
    }
}
