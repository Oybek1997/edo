<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkTaskCategory extends Model
{
    use SoftDeletes;
    protected $connection = 'workflow_worktask';
    protected $table = 'task_categories';
    public $timestamps = true;
}
