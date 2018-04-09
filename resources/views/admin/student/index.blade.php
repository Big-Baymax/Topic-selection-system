/** * Created by PhpStorm. * User: Baymax * Date: 2018/4/9 * Time: 12:22 */@extends('admin/layouts.table')@section('title', '学生管理')@section('table_title','学生用户信息管理')@section('table')    <!-- Example Card View -->    <div class="example-wrap">        <div id="exampleToolbar">            <form id="filter">                <div class="btn-group hidden-xs">                    <button type="button" class="btn btn-outline btn-default" id="add" data-toggle="tooltip"                            data-animation="false" data-original-title="添加">                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>                    </button>                    <button type="button" class="btn btn-outline btn-default" id="defriend" data-toggle="tooltip"                            data-animation="false" data-original-title="禁用">                        <i class="glyphicon glyphicon-ban-circle" aria-hidden="true"></i>                    </button>                    <button type="button" class="btn btn-outline btn-default" id="del" data-toggle="tooltip"                            data-animation="false" data-original-title="删除">                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>                    </button>                    <button type="button" class="btn btn-outline btn-default" id="reset" data-toggle="tooltip"                            data-animation="false" data-original-title="重置">                        <i class="glyphicon glyphicon-repeat" aria-hidden="true"></i>                    </button>                    <button type="button" class="btn btn-outline btn-default" id="edit" data-toggle="tooltip"                            data-animation="false" data-original-title="修改">                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>                    </button>                    <button type="button" class="btn btn-outline btn-default" id="import" data-toggle="tooltip"                            data-animation="false" data-original-title="导入">                        <i class="glyphicon glyphicon-open" aria-hidden="true"></i>                    </button>                    <button type="button" class="btn btn-outline btn-default" id="info" data-toggle="tooltip"                            data-animation="false" data-original-title="导入记录">                        <i class="glyphicon glyphicon-info-sign" aria-hidden="true"></i>                    </button>                </div>                <div class="btn-group">                    <select class="btn btn-default btn-outline dropdown-toggle" name="dep_id" id="dep">                        <option value="" style="display: none">请选择系别</option>                        <option>2</option>                        <option>3</option>                        <option>4</option>                        <option>5</option>                    </select>                </div>            </form>        </div>        <div class="example">            <div class="btn-group hidden-xs" id="exampleToolbar" role="group">            </div>            <table id="table" data-toggle="table" data-card-view="true" data-mobile-responsive="true"                   data-click-to-select="true" data-unique-id="id" data-show-export="true">                <thead>                <tr>                    <th data-field="state" data-checkbox="true"></th>                    <th data-field="id" data-sortable="true">UID</th>                    <th data-field="stuNo" data-sortable="true">学号</th>                    <th data-field="name" data-sortable="true">姓名</th>                    <th data-field="department_id">系别</th>                    <th data-field="sex" data-formatter="sexFormatter" data-align="center" data-halign="center" data-sortable="true">性别</th>                    <th data-field="created_at" data-sortable="true">注册时间</th>                    <th data-field="updated_at" data-sortable="true">更新时间</th>                    <th data-field="status" data-formatter="statusFormatter" data-align="center" data-halign="center" data-sortable="true">状态</th>                </tr>                </thead>            </table>        </div>    </div>    <!-- End Example Card View -->@endsection@section('model')    <div class="modal fade import-model" tabindex="-1" role="dialog">        <div class="modal-dialog" role="document">            <div class="modal-content">                <div class="modal-header">                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">                        <span aria-hidden="true">&times;</span>                    </button>                    <h4 class="modal-title">选择Excel文件</h4>                </div>                <div class="modal-body">                    <form method="post" id="import-form">                        <div class="form-group">                            {{--<button type="button" class="btn btn-primary" style="width: 50%;padding: 10px;font-size: 15px;line-height: 30px">--}}                            {{--<span class="glyphicon glyphicon-cloud-upload" aria-hidden="true" style="font-size: 30px"></span> 点击选择Excel文件--}}                            {{--</button>--}}                            <label>点击选择Excel文件</label>                            <input type="file" id="file" name="file" placeholder="请选择文件" class="form-control"                                   accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"                                   required="required">                        </div>                        <div class="progress" id="progress" style="display:none;">                            <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">                                0%                            </div>                        </div>                    </form>                </div>                <div class="modal-footer">                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>                    <button id="import-confirm" type="button" class="btn btn-primary">确定</button>                </div>            </div>        </div>    </div>    <div class="modal fade add-model" tabindex="-1" role="dialog">        <div class="modal-dialog" role="document">            <div class="modal-content">                <div class="modal-header">                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">                        <span aria-hidden="true">&times;</span>                    </button>                    <h4 class="modal-title">添加</h4>                </div>                <div class="modal-body">                    <form method="post" id="add-form">                        <div class="form-group">                            <label>姓名</label>                            <input type="text" id="name" name="name" placeholder="请输入姓名" class="form-control" required="required" maxlength="10">                        </div>                        <div class="form-group">                            <label>学号</label>                            <input type="text" id="stuNo" name="stuNo" placeholder="请输入手机号" class="form-control">                        </div>                        <div class="form-group">                            <label>性别</label>                            <select class="btn btn-default btn-outline dropdown-toggle" name="sex" id="sex">                                <option value="" style="display: none">请选择性别</option>                                <option value="1">男</option>                                <option value="0">女</option>                            </select>                        </div>                        <div class="form-group">                            <label>系别</label>                            <select class="btn btn-default btn-outline dropdown-toggle" name="department_id" id="department_id" required="required" >                                <option value="" style="display: none">请选择系别</option>                                <option>2</option>                                <option>3</option>                                <option>4</option>                                <option>5</option>                            </select>                        </div>                    </form>                </div>                <div class="modal-footer">                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>                    <button id="add-confirm" type="button" class="btn btn-primary">确定</button>                </div>            </div>        </div>    </div>    <div class="modal fade edit-model" tabindex="-1" role="dialog">        <div class="modal-dialog" role="document">            <div class="modal-content">                <div class="modal-header">                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">                        <span aria-hidden="true">&times;</span>                    </button>                    <h4 class="modal-title" id="title">修改</h4>                </div>                <div class="modal-body">                    <form method="post" id="edit-form">                        <div class="form-group">                            <label>姓名</label>                            <input type="text" id="B-name" name="name" placeholder="请输入姓名" class="form-control" required="required" maxlength="10">                        </div>                        <div class="form-group">                            <label>学号</label>                            <input type="text" id="B-stuNo" name="stuNo" placeholder="请输入学号" class="form-control">                        </div>                        <div class="form-group">                            <label>性别</label>                            <select class="btn btn-default btn-outline dropdown-toggle" name="sex" id="B-sex">                                <option value="0" style="display: none">请选择性别</option>                                <option value="1">男</option>                                <option value="2">女</option>                            </select>                        </div>                        <div class="form-group">                            <label>系别</label>                            <select class="btn btn-default btn-outline dropdown-toggle" name="department_id" id="B-department_id" required="required" >                                <option value="" style="display: none">请选择系别</option>                                <option>2</option>                                <option>3</option>                                <option>4</option>                                <option>5</option>                            </select>                        </div>                    </form>                </div>                <div class="modal-footer">                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>                    <button id="edit-confirm" type="button" class="btn btn-primary">确定</button>                </div>            </div>        </div>    </div>    <div class="modal fade info-model" tabindex="-1" role="dialog">        <div class="modal-dialog modal-lg" role="document">            <div class="modal-content">                <div class="modal-header">                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">                        <span aria-hidden="true">&times;</span>                    </button>                    <h4 class="modal-title" id="member-title">导入历史记录</h4>                </div>                <div class="modal-body">                    <!--内容-->                    <div class="example-wrap">                        <div class="example">                            <table id="info-table" data-toggle="table" data-card-view="true" data-mobile-responsive="true" data-click-to-select="true" data-unique-id="list">                                <thead>                                <tr>                                    <th data-field="state" data-checkbox="true"></th>                                    <th data-field="list">list</th>                                    <th data-field="group_id">导入状态</th>                                    <th data-field="created_at" data-sortable="true">导入时间</th>                                    <th data-field="id" data-formatter="operateFormatter">操做</th>                                </tr>                                </thead>                            </table>                        </div>                    </div>                </div>            </div>        </div>    </div>@endsection@section('linkjs')@endsection@section('js')    <script>        $.validator.setDefaults({            highlight: function (e) {                $(e).closest(".form-group").removeClass("has-success").addClass("has-error")            },            success: function (e) {                e.closest(".form-group").removeClass("has-error").addClass("has-success")            },            errorElement: "span",            errorPlacement: function (e, r) {                e.appendTo(r.is(":radio") || r.is(":checkbox") ? r.parent().parent().parent() : r.parent())            },            errorClass: "help-block m-b-none",            validClass: "help-block m-b-none",        })        $('#table').bootstrapTable({            search: true,            pagination: true,            url: '/admin/administrators',            showRefresh: true,            showToggle: true,            showColumns: true,            method: 'get',            sortName: 'created_at',            height: 600,            pageSize: 10,            pageNumber: 1,//开始的时候是第几页            // 自定义搜索参数            queryParams: formfilter,            pageList: [5, 10, 15, 25, 'ALL'],            queryParamsType: '',            dataField: 'data',//指定            sidePagination: "server",            toolbar: "#exampleToolbar",            iconSize: "outline",            sortOrder: "desc",            exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel'],            exportOptions: {fileName: '信息导出'},            icons: {                refresh: "glyphicon-repeat",                toggle: "glyphicon-list-alt",                columns: "glyphicon-list",                export: 'glyphicon glyphicon-export'            }        });        //导入记录表格        $('#info-table').bootstrapTable({            pagination: true,            url: '_URL_/admin/access_list/1',            showRefresh: true,            showToggle: true,            showColumns: true,            method: 'post',            height: 350,            pageSize: 10,            pageNumber: 1,//开始的时候是第几页            pageList: [5, 10, 15, 25,'ALL'],            queryParamsType: '',            sidePagination: "server",            dataField:'data',//指定            iconSize: "outline",            sortOrder:"desc",            icons: {                refresh: "glyphicon-repeat",                toggle: "glyphicon-list-alt",                columns: "glyphicon-list",            }        });        function statusFormatter(value, row, index) {            if (value == 1) {                return '<a class="label label-primary defriend" href="#" data_id="' + row.uid + '">已启用</a>'            } else {                return '<a class="label label-danger defriend" href="#"  data_id="' + row.uid + '">已禁用</a>'            }        }        function operateFormatter(value, row, index) {            return '<a class="label label-primary label-btn" href="admin/loginlist/' + row.id + '">' +                '<span class="glyphicon glyphicon-floppy-save label-icon"></span>下载记录</a>' +                '<a class="label label-primary label-btn" href="admin/loginlist/' + row.id + '">' +                '<span class="glyphicon glyphicon-floppy-save label-icon"></span>已处理</a>'        }        function sexFormatter(value, row, index) {            if (value == 1) {                return '<a class="label label-success defriend" href="#">男</a>'            } else if (value == 2) {                return '<a class="label label-warning defriend" href="#">女</a>'            }else {                return '<a class="label label-warning defriend" href="#">未填写</a>'            }        }        //修改上传参数        function formfilter(params) {            var val = $("#filter").serializeArray();            for (var key in val) {                params[val[key]['name']] = val[key]['value'];            }            return params;        }        $(document).ready(function () {            // Tooltip            $('[data-toggle="tooltip"]').tooltip({                trigger: 'hover'            });            $("#dep").change(function () {                $('#table').bootstrapTable('selectPage', 1);            });            //文件导入 参数说明（发送时候的名称，接口地址）            import_file("file","url")            //基本操做 参数说明（表格对象 ， 添加接口 ，删除接口 ，编辑接口 ，重置接口 ，禁用接口）            user_operate($('#table'), 'admin/add', 'admin/del', 'admin/edit/', 'admin/reset', 'admin/defriend');        });    </script>@endsection