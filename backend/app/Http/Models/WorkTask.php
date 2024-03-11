<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Employee;

class WorkTask extends Model
{
    protected $connection = 'workflow_worktask';
    protected $table = 'tasks';
    public $timestamps = true;

    public function user()
    {
        return $this->hasMany('App\Http\Models\WorkTaskAssignment', 'task_id', 'id');
    }
    public function category()
    {
        return $this->hasOne('App\Http\Models\WorkTaskCategory', 'id', 'category_id');
    }
}
