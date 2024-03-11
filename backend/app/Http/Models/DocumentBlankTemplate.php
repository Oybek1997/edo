<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentBlankTemplate extends Model
{
    use SoftDeletes;

    public function documentBlankAttribute()
    {
        return $this->hasMany('App\Http\Models\DocumentBlankAttribute', 'document_blank_id', 'id');
    }

    public function blankTemplate()
    {
        return $this->hasOne('App\Http\Models\BlankTemplate', 'id', 'blank_id');
    }
}
