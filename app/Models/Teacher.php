<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $hidden = ['password', 'salt', 'updated_at'];
}
