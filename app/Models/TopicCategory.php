<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicCategory extends Model
{
    public function topics()
    {
        return $this->hasMany(Topic::class, 'category_id', 'id');
    }
}
