# Topic-selection-system毕业设计选题管理系统

### 客户端功能：
1. 用户功能：登录、修改密码
1. 查看课题列表（检索方式：全部课题、按指导老师、按课题类型）
1. 选择一个课题并提交选题申请（每个课题只能被一人选择，一旦被选则不会再出现在课题列表中）
1. 查看我选择的课题及状态
### 服务端功能：
1. 系统管理员可以在后台进行用户管理：添加、删除用户、修改用户信息、查看所有用户（用户分为指导老师、系统管理员、学生三个角色），可以设置系统开始选题时间和结束选题时间（不在该时段内学生无法通过APP登录，指导老师和系统管理员可以登录）
1. 指导老师可以登录后台添加课题
1. 指导老师可以查看选题情况，并进行审核，审核不通过课题将会再次出现在课题列表中供学生选择


## 目录说明
~~~
目录
├─database                数据库配置文件夹 
│  ├─factories           
│  │  ├─UserFactory.php   数据库配置信息
│  ├─migrations           数据库crete文件
├─public                  WEB目录（对外访问目录）
│  ├─index.php            入口文件
│  ├─app                  App文件夹
│  │  ├─tss               App源码      
│  │  │  ├─unpackage      安装包文件夹
│  │  │  ├─css            app样式文件
│  │  │  ├─index.html     app主页
│  │  │  └─login.html     app登录页面
│  └─.htaccess            用于apache的重写
├─storage                 本地文件
│  ├─framework           
│  │  ├─views             管理后台页面
├─app               
│  ├─http
└─ └─ └─Controllers       控制器源码

~~~
## 后台项目环境
1. 运行环境   
     - appache + mysql + php   
2. 入口文件
    - public/index
3. 使用框架（Laravel 5.5）
    - PHP >= 7.0.0
    - PHP OpenSSL 扩展
    - PHP PDO 扩展
    - PHP Mbstring 扩展
    - PHP Tokenizer 扩展
    - PHP XML 扩展

## 项目编译
- composer install 安装composer依赖
- php artisan key:generate 项目第一次运行
- php artisan migrate 数据迁移
- php artisan db:seed 生成数据
## 接口使用说明
### 登录接口
```
    地址: (POST) http://~/admin/login
    参数:{
        login_name:登录名,
        login_pwd:登录密码,
        identity:身份（1:管理员 2:老师 3:学生）
    }
    服务端返回json:{
        code:1(1:success 0:failed),
        msg:登录成功,
        data:{
                redirect_url:xxx
        }
    }
```
### 管理员列表
```
    地址: (GET) http://~/admin/administrators
    参数:{
        pageNumber:第几页,
        pageSize:每页几条记录,
        sortName:根据这个字段排序,
        sortOrder:desc/asc,
        searchText:搜索关键字
    }
    服务端返回json:{
        code:1
        data:{},
        total:数据总数
    }
```
### 添加管理员(视图)
```
    地址 (GET) http://~/admin/administratos/create
```
### 添加管理员(逻辑)
```
    地址 (POST) http://~/admin/administrators
    参数:{
        name:姓名,
        mobile:手机,
        login_name:登录名,
        password:密码
    }
```
### 修改管理员数据
```
    地址 (GET) http://~/admin/administrators/{id}/edit
```
### 修改管理员(逻辑)
```
    地址 (POST) http://~/admin/administrators/{id}
    参数:{
        name:姓名,
        mobile:手机,
        login_name:登录名,
        _method:put(方法伪造,必传)
    }
```
### 重置密码
```
    地址 (GET(视图)/POST(逻辑)) http://~/admin/administrators/reset-pwd/{id}
    参数:{
        password:密码
    }
```
### 禁用/恢复管理员
```
    地址 (POST) http://~/admin/administrators/ops
    参数:{
        id:管理员id,
        act:动作 recover(恢复)/remove(禁用)
    }
```

## 数据库配置（自动生成详细在database->migrations目录下查看）

### administrators 管理员用户表

|     名称    |   类型   |     描述    |
|:----------:|:-------:|:-----------:|
| id         | INTEGER | 住宿安排id    |
| name       | string    | 用户名 |
| mobile     | string | 手机号    |
| login_name    | INTEGER | 登录名  |
| password    | INTEGER | 登录密码  |
| salt | string    | 加密盐  |
| status   | tinyInteger   | 1 有效 0无效  |

## 最终效果

### 手机app

### 系统后台

![后台登录页面](https://raw.githubusercontent.com/Big-Baymax/Topic-selection-system/master/public/img/effect/admin_login.png '后台登录页面' )
