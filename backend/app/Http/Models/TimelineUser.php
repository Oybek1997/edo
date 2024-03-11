<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TimelineUser extends Model
{
    protected $table = 'timeline_user';

    public $timestamps = false;

    protected $primaryKey = ['timeline_id', 'user_id'];

    protected function setKeysForSaveQuery(Builder $query)
    {
        $query
            ->where('timeline_id', '=', $this->getAttribute('timeline_id'))
            ->where('user_id', '=', $this->getAttribute('user_id'));
        return $query;
    }

    public $incrementing = false;

    public function whoLike()
    {
        return $this->hasOne('App\User', 'id', 'user_id')->select(['id', 'employee_id']);
    }
}
