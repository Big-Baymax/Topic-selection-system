<?php

namespace App\Http\Controllers\Admin;

use App\Models\Administrator;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdministratorController extends BaseController
{
    public function __construct()
    {
//        $this->checkPolicy('admin');
    }

    public function list()
    {
        return view('admin/administrator/index');
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
        return view('admin/administrator/add');
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

    public function edit($id)
    {
        $administrator = Administrator::find($id);

        return [
            'code' => 1,
            'data' => $administrator
        ];
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
        $administrator = Administrator::find($id);
        if (!$administrator) {
            return formatResponse('该管理员不存在～～');
        }
        switch ($act) {
            case 'recover':
                $administrator->status = 1;
                $act = '恢复成功～～';
                break;
            case 'remove':
                $administrator->status = 0;
                $act = '禁用成功～～';
                break;
        }

        return formatResponse($act, [], 1);
    }

    public function resetPwd(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            return view('admin/administrators/reset-pwd', compact('id'));
        }
        $input = $request->post();
        $password = array_get($input, 'password', '');
        if (!$id) {
            return formatResponse('请选择要操作的对象～～');
        }
        $administrator = Administrator::find($id);
        if (!$administrator) {
            return formatResponse('该管理员不存在～～');
        }
        if (!$password) {
            return formatResponse('请输入密码～～');
        }
        $administrator->password = md5($password . md5($administrator->salt));
        $administrator->save();

        return formatResponse('重置成功～～', [], 1);
    }
}
