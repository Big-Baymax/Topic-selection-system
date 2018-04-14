<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTopicLogs extends Model
{
    public $timestamps = false;

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id')->select(['id', 'name']);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id')->select(['id', 'name']);
    }
}
