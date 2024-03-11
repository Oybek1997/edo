<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Help extends Model
{
    //
    public function files()
    {
        return $this->hasMany(File::class, 'object_id', 'id')->where('object_type_id',12);
    }
    public function createdBy()
    {
        return $this->setConnection('mysql')->hasOne(User::class, 'id', 'created_by');
    }
}
