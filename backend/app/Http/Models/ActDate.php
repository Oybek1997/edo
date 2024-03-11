<?php

namespace App\Http\Models;;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActDate extends Model
{
    // use SoftDeletes;
    //
    public function document()
    {
        return $this->hasOne('App\Http\Models\Document', 'id', 'document_id');
    }
}
