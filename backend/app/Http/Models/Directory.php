<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    public function directoryType()
    {
        return $this->hasOne('App\Http\Models\DirectoryType', 'id', 'directory_type_id');
    }
}
