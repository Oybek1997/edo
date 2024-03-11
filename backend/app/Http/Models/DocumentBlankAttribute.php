<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentBlankAttribute extends Model
{
    use SoftDeletes;

    public function blankAttributeTemplate()
    {
        return $this->hasOne('App\Http\Models\BlankAttributeTemplate', 'id', 'blank_attribute_id');
    }
}
