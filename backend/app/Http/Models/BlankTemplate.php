<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BlankTemplate extends Model
{
    public function blank_attribute_template(){
        return $this->hasMany('App\Http\Models\BlankAttributeTemplate','blank_id','id');
    }
}
