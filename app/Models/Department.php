<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public function students()
    {
        return $this->hasMany(Student::class, 'department_id', 'id');
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'department_id', 'id');
    }
}
