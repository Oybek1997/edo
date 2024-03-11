<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Models\Employee;
use App\User;

class UnblockedUser extends Model
{
    use SoftDeletes;
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
