<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkTaskAssignment extends Model
{
    use SoftDeletes;
    protected $connection = 'workflow_worktask';
    protected $table = 'workflow_worktask.task_assignments';
    public $timestamps = true;

    public function getTask()
    {
        return $this->hasOne('App\Http\Models\WorkTask', 'id', 'task_id');
    }
    public function comments()
    {
        return $this->hasMany('App\Http\Models\WorkTaskComment', 'task_assignment_id', 'id');
    }
    public function taskFiles()
    {
        return $this->hasMany('App\Http\Models\WorkTaskFile', 'object_id', 'id');
    }

    public function doer()
    {
        return $this->setConnection('mysql')->hasOne(Employee::class, 'id', 'employee_id');
    }
}
