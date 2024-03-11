<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Models\Employee;
use App\User;

class UserTemplate extends Model
{
    use SoftDeletes;
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function documentTemplate()
    {
        return $this->hasOne('App\Http\Models\DocumentTemplate', 'id', 'document_template_id');
    }
}
