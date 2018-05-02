<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\StudentTopicLogs;
use App\Models\Teacher;
use App\Models\Topic;
use App\Models\TopicCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        $query = Topic::query();
        $page['page_index'] = array_get($input, 'page', 1);
        $order = array_get($input, 'order_by', 'asc');
        if ($page['page_index'] <= 0) {
            $page['page_index'] = 1;
        }

        $page_size = config('common.page_size');
        $student = $request->attributes->get('student');
        $query->where('status', 1)
            ->where('department_id', $student->department_id);

        $teacher_ids = array_unique($query->pluck('teacher_id')->toArray());
        $category_ids = array_unique($query->pluck('category_id')->toArray());
        if (!empty($input['search_text'])) {
            $query->where('name', 'liek', '%' . $input['search_text'] . '%');
        }
        if (!empty($input['teacher']) && is_array($input['teacher'])) {
            $query->whereIn('teacher_id', $input['teacher']);
        }
        if (!empty($input['topic_category']) && $input['topic_category']) {
            $query->whereIn('category_id', $input['topic_category']);
        }
        $total_count = $query->count();
        $page['page_count'] = ceil($total_count / $page_size);
        $query->select(['id', 'name', 'description', 'teacher_id', 'created_at', 'category_id'])
                ->orderBy('created_at', $order)
                ->offset(($page['page_index'] - 1) * $page_size)
                ->take($page_size);

        $topics = $query->get();
        $page['page_quantity'] = $topics->count();
        $teachers = Teacher::where('department_id', $student->department_id)
            ->whereIn('id', $teacher_ids)
            ->select(['id', 'name'])
            ->where('status', 1)
            ->get();
        $categories = TopicCategory::whereIn('id', $category_ids)
            ->select(['id', 'name'])
            ->get();

        return show(1, '请求成功～～', [
            'topics' => $topics,
            'teachers' => $teachers,
            'categories' => $categories,
            'page' => $page
        ]);
    }

    public function show($id)
    {
        $topic = Topic::with(['category', 'teacher'])
                ->select(['id', 'name', 'description', 'created_at', 'category_id', 'teacher_id'])
                ->find($id)
                ->toArray();
        if (!$topic) {
            return show(0, '该课题不存在～～', [], 404);
        }
        return show(1, '请求成功～～', [
            'id' => $topic['id'],
            'name' => $topic['name'],
            'description' => $topic['description'],
            'created_at' => $topic['created_at'],
            'category' => $topic['category']['name'],
            'teacher' => $topic['teacher']['name']
        ]);
    }

    public function select(Request $request)
    {
        $student_id = $request->post('student_id', '');
        $topic_id = $request->post('topic_id', '');
        if (!$student_id) {
            return show(0, '未知的学生~~', [], 404);
        }
        if (!$topic_id) {
            return show(0, '未知的选题~~', [], 404);
        }
        $topic = Topic::find($topic_id);
        $has_selected = Topic::where('student_id', $student_id)->get();
        if ($has_selected->isNotEmpty()) {
            return show(0, '你已经选过题了～～', [], 400);
        }
        if (!$topic) {
            return show(0, '该选题不存在~~', [], 404);
        }
        if ($topic->student_id || $topic->status != 1) {
            return show(0, '该选题已经被选了~~', [], 400);
        }
        try {
            DB::beginTransaction();
            $topic->student_id = $student_id;
            $topic->status = 2;
            $topic->save();
            $student_topic_log = new StudentTopicLogs();
            $student_topic_log->insert([
                'student_id' => $student_id,
                'teacher_id' => $topic->teacher_id,
                'topic_id' => $topic->id,
                'create_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
            throw new ApiException('选题失败~~');
        }

        return show(1, '选题成功~~', [], 201);
    }
}
