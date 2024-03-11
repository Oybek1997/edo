<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributePermission extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function documentTemplate()
    {
        return $this->hasOne('App\Http\Models\DocumentTemplate', 'id', 'document_template_id');
    }

}
