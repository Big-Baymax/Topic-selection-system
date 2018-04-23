<?php
return [
    'identity_mapping' => [
        1 => '管理员',
        2 => '教师',
    ],
    'admin_remember_session' => 'admin_user',
    'page_size' => 10,
    'sex_mapping' => [
        0 => '未设置',
        1 => '男',
        2 => '女'
    ],
    'default_salt' => 'HVw~P)cvOwMRQBFM',
    'excel_ext' => ['xls'],
    'topic_status' => [
        1 => '尚未被选',
        2 => '待审核',
        3 => '审核通过',
        4 => '申请重选'
    ],
    'student_topic_status' => [
        1 => '审核中',
        2 => '审核通过',
        3 => '审核失败',
        4 => '申请重选中',
        5 => '重新选题申请通过',
        6 => '取消重选'
    ],
    'default_topic_quantity' => 8,
    'aes_key' => 'sgg45747ss223455',
    'app_login_time_out_day' => 7,// 登录token的失效时间
];