<?php


namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;


class Timeline extends Model
{
    protected $table = 'timeline';

    public function comments()
    {
        return $this->hasMany('App\Http\Models\Timeline', 'parent', 'id')->orderBy('id', 'Desc');
    }

    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function files()
    {
        return $this->hasMany('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', '=', 9);
    }

    public function likers(){
        return $this->hasMany('App\Http\Models\TimelineUser', 'timeline_id', 'id')->where('likes', true);
    }

    public function dislikers(){
        return $this->hasMany('App\Http\Models\TimelineUser', 'timeline_id', 'id')->where('dislikes', true);
    }

    public function timelineTags(){
        return $this->hasMany('App\Http\Models\TimelineTag', 'timeline_id', 'id');
    }
}
