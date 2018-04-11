<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id')->select(['id', 'name']);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id')->select(['id', 'name']);
    }

    public function category()
    {
        return $this->belongsTo(TopicCategory::class, 'category_id', 'id')->select(['id', 'name']);
    }
}
