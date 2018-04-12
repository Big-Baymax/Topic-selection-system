
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>毕业设计选题管理系统- @yield('title')</title>
    <meta name="keywords" content="_KEY_">
    <meta name="description" content="_DES_">
    <link rel="shortcut icon" href="/img/favicon.ico">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/animate.min.css" rel="stylesheet">
    <link href="/css/style.min.css" rel="stylesheet">
    <link href="/css/admin-style.css" rel="stylesheet">
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/plugins/validate/jquery.validate.min.js"></script>
    <script src="/js/plugins/validate/messages_zh.min.js"></script>
    <script src="/js/content.min.js"></script>
    <script src="/js/plugins/layer/layer.js"></script>
    <script src="/js/admin-js.js"></script>

    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    @yield('linkcss')
    @yield('css')
</head>

@yield('body')

@yield('model')


@yield('linkjs')
@yield('js')
<script>
    //验证插件初始化
    $.validator.setDefaults({
        highlight: function (e) {
            $(e).closest(".form-group").removeClass("has-success").addClass("has-error")
        },
        success: function (e) {
            e.closest(".form-group").removeClass("has-error").addClass("has-success")
        },
        errorElement: "span",
        errorPlacement: function (e, r) {
            e.appendTo(r.is(":radio") || r.is(":checkbox") ? r.parent().parent(): r.parent())
        },
        errorClass: "help-block m-b-none",
        validClass: "help-block m-b-none",
    })
</script>
</html>