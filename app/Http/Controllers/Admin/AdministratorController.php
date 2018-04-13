<?php

namespace App\Http\Controllers\Admin;

use App\Models\Administrator;
use App\Models\Department;
use function foo\func;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdministratorController extends BaseController
{
    public function __construct()
    {
        $this->checkPolicy('admin');
    }

    public function list()
    {
        return view('admin/administrator/index', compact('departments'));
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $pageNumber = array_get($input, 'pageNumber', 1);
        $pageSize = array_get($input, 'pageSize', config('common.page_size'));
        $sortName = array_get($input, 'sortName', '');
        $sortOrder = array_get($input, 'sortOrder', 'desc');
        $searchText = array_get($input, 'searchText', '');
        $query = Administrator::query();
        if ($searchText) {
            $query->where('name', 'like', '%' . $searchText . '%')
                ->orWhere('login_name', 'like', '%' . $searchText . '%');
        }
        if ($sortName) {
            $query->orderBy($sortName, $sortOrder);
        }
        $total = $query->where('is_admin', 0)->count();
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
            'name' => 'required|max:255',
            'mobile' => 'required|max:11|regex:/^1[34578][0-9]{9}$/',
            'login_name' => 'required|max:20|unique:administrators',
            'password' => 'required',
        ], [
            'name.*' => '请输入符合规范的姓名～～',
            'mobile.*' => '请输入符合规范的手机号～～',
            'login_name.required' => '请输入登录名～～',
            'login_name.max' => '请输入符合规范的登录名～～',
            'login_name.unique' => '该登录名已存在～～',
            'password.required' => '请输入登录密码～～'
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $administrator = new Administrator();
        $input = $request->post();
        $administrator->name = $input['name'];
        $administrator->mobile = $input['mobile'];
        $administrator->login_name = $input['login_name'];
        $administrator->salt = makeSalt();
        $administrator->password = md5($input['password'] . md5($administrator->salt));
        $administrator->save();

        return formatResponse('操作成功～～', [], 1);
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateMiddle($request->post(), [
            'name' => 'required|max:255',
            'mobile' => 'required|max:11|regex:/^1[34578][0-9]{9}$/',
            'login_name' => 'required|max:20|unique:administrators,login_name,' . $id,
        ], [
            'name.*' => '请输入符合规范的姓名～～',
            'mobile.*' => '请输入符合规范的手机号～～',
            'login_name.required' => '请输入登录名～～',
            'login_name.max' => '请输入符合规范的登录名～～',
            'login_name.unique' => '该登录名已存在～～',
        ]);
        if ($validateData) {
            return formatResponse($validateData);
        }
        $administrator = Administrator::findOrFail($id);
        $input = $request->post();
        $administrator->name = $input['name'];
        $administrator->mobile = $input['mobile'];
        $administrator->login_name = $input['login_name'];
        $administrator->save();

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
        $administrators = Administrator::findMany($ids);
        if ($administrators->isEmpty()) {
            return formatResponse('管理员不存在～～');
        }
        foreach ($administrators as $item) {
            if ($item->status == 0) {
                $item->status = 1;
            } else {
                $item->status = 0;
            }
            $item->save();
        }

        return formatResponse('操作成功～～', [], 1);
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
        $administrators = Administrator::findMany($ids);
        if (!$administrators) {
            return formatResponse('管理员不存在～～');
        }
        $identity = $this->getCurrentIdentity();
        $current_user = $request->attributes->get('user');
        foreach ($administrators as $item) {
            $item->salt = config('common.default_salt');
            $item->password = md5(123456 . md5($item->salt));
            $item->save();
            if ($item->id == $current_user->id) {
                $this->setLoginStatus($item, $identity);
            }
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
        $administrators = Administrator::findMany($ids);
        if (!$administrators) {
            return formatResponse('管理员不存在～～');
        }
        Administrator::whereIn('id', $ids)->delete();

        return formatResponse('删除成功～～', [], 1);
    }
}
