<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class As400DownloadHistory extends Model
{
    public $timestamps = false;

    public function queryBy()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function As400Query()
    {
        return $this->hasMany("App\Http\Models\As400Query", 'id', 'as400_query_id');
    }

}
