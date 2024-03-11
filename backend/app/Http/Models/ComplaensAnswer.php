<?php

namespace App\Http\Models;;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplaensAnswer extends Model
{
    // use SoftDeletes;
    //
    public function document()
    {
        return $this->hasOne('App\Http\Models\Document', 'id', 'document_id');
    }
    public function questions(){
        return $this->hasOne('App\Http\Models\ComplaensQuestion', 'id', 'question_id');

    }
    public function relatives(){
        return $this->hasMany('App\Http\Models\ComplaensRelative', 'answer_id', 'id');

    }
}
