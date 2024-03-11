<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class As400Query extends Model
{
    use SoftDeletes;

    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
    public function As400Permissions()
    {
        return $this->hasMany("App\Http\Models\As400Permission", 'query_id', 'id');
    }
}
