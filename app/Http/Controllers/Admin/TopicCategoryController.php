<?php

namespace App\Http\Controllers\Admin;

use App\Models\TopicCategory;
use Illuminate\Http\Request;

class TopicCategoryController extends BaseController
{
    public function __construct()
    {
        $this->checkPolicy('admin');
    }

    public function list()
    {
        return view('admin/topic/category');
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
        $data = $query
            ->offset(($pageNumber - 1) * $pageSize)
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
        $topic_category = new TopicCategory();
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
        $topic_categories = TopicCategory::findMany($ids);
        if (!$topic_categories) {
            return formatResponse('分类不存在～～');
        }
        $failed_delete = '';
        $success_delete = 0;
        $failed_delete_id = [];
        foreach ($topic_categories as $category) {
            if ($category->topics()->count() > 0) {
                $failed_delete .= $category->name . ' ';
                $failed_delete_id[] = $category->id;
            } else {
                $success_delete += 1;
                $category->delete();
            }
        }
        $response_msg = $success_delete ? '删除成功～～' : '删除失败～～';
        $response_msg .= $failed_delete ? $failed_delete . '分类下有选题，无法删除' : '';

        $res = mb_substr($response_msg, 0, 4, 'UTF-8');
        return formatResponse($response_msg, $failed_delete_id, $res == '删除成功' ? 1 : 0);
    }
}
