<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentTopicLogs;
use App\Models\Topic;
use Illuminate\Http\Request;

class StudentTopicLogController extends Controller
{
    public function index(Request $request)
    {
        $student_id = $request->get('student_id', '');
        if (!$student_id) {
            return show(0, '未知的学生~~', [], 404);
        }
        $student_topic_logs = StudentTopicLogs::where('student_id', $student_id)
                ->with(['teacher', 'topic'])
                ->get()
                ->toArray();

        $data = [];
        $student_topic_status = config('common.student_topic_status');
        foreach ($student_topic_logs as $key => $log) {
            $data[$key]['id'] = $log['id'];
            $data[$key]['teacher'] = $log['teacher']['name'];
            $data[$key]['topic'] = $log['topic']['name'];
            $data[$key]['created_at'] = $log['create_at'];
            $data[$key]['status'] = $student_topic_status[$log['status']];
            $data[$key]['topic_id'] = $log['topic_id'];
        }

        return show(1, '请求成功~~', $data);
    }

    public function resetApply(Request $request)
    {
        $id = $request->post('id', '');
        if (!$id) {
            return show(0, '未知的记录~~', [], 404);
        }
        $log = StudentTopicLogs::find($id);
        if (!$log) {
            return show(0, '没有该记录~~', [], 404);
        }
        if ($log->status != 2) {
            return show(0, '只有审核通过的记录才可申请重选～～', [] , 404);
        }

        $topic = Topic::find($log->topic_id);
        $topic->status = 4;
        $log->status = 4;
        if ($topic->save() && $log->save()) {
            return show(1, '申请成功～～', [], 202);
        }

        return show(0, '申请失败~~', [], 404);
    }

    public function cancel(Request $request)
    {
        $id = $request->post('id');
        if (!$id) {
            return show(0, '未知的选题记录~~', [], 404);
        }
        $log = StudentTopicLogs::find($id);
        if (!$log) {
            return show(0, '该选题记录不存在～～', [], 404);
        }
        if ($log->status != 1) {
            return show(0, '只有审核中的记录才可取消重选～～', [] , 404);
        }
        $log->status = 6;
        $topic = Topic::find($log->topic_id);
        $topic->status = 1;
        $topic->student_id = 0;
        if ($log->save() && $topic->save()) {
            return show(1, '取消成功～～', [], 202);
        }

        return show(0, '取消失败～～', [], 500);
    }
}
