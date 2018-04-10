<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $hidden = ['password', 'salt', 'updated_at'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')->select(['id', 'name']);
    }
}
