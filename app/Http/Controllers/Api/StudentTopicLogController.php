<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentTopicLogs;
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
            $data[$key]['teacher'] = $log['teacher']['name'];
            $data[$key]['topic'] = $log['topic']['name'];
            $data[$key]['created_at'] = $log['create_at'];
            $data[$key]['status'] = $student_topic_status[$log['status']];
        }

        return show(1, '请求成功~~', $data);
    }
}
