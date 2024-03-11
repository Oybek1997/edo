<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkTaskcomment extends Model
{
    use SoftDeletes;
    protected $connection = 'workflow_worktask';
    protected $table = 'task_assignment_comments';
    public $timestamps = true;

    public function getChildTask()
    {
        return $this->hasMany('App\Http\Models\WorkTaskAssignment', 'id', 'task_assignment_id');
    }
    public function commentedBy()
    {
        return $this->setConnection('mysql')->hasOne(Employee::class, 'id', 'created_by');
    }
    // public function doer()
    // {
    //     return $this->setConnection('mysql')->hasOne(Employee::class, 'id', 'employee_id');
    // }
}
