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
            'id' => 'required|array',
        ], [
            'id.required' => '请选择要操作的对象～～',
            'id.array' => '传递的ids有问题～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $ids = $request->post('id');
        $topic_categories = TopicCategory::findMany($ids);
        if (!$topic_categories) {
            return formatResponse('管理员不存在～～');
        }
        foreach ($topic_categories as $item) {
            if ($item->status == 0) {
                $item->status = 1;
            } else {
                $item->status = 0;
            }
            $item->save();
        }

        return formatResponse('操作成功', [], 1);
    }
}
