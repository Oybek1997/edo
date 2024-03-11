<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineTag extends Model
{
    protected $table = 'timeline_tag';

    public $timestamps = false;

    public function tag(){
        return $this->hasOne('App\Http\Models\Tag', 'id', 'tag_id');
    }

}
