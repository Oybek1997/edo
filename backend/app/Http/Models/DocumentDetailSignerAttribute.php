<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDetailSignerAttribute extends Model
{
    //
    public function documentDetailAttributes()
    {
        return $this->hasOne('App\Http\Models\DocumentDetailAttribute', 'id', 'd_d_attribute_id');
    }
    
    public function attributeSignerStaff()
    {
        return $this->hasMany('App\Http\Models\AttributeSignerStaff', 'attribute_id', 'd_d_attribute_id');
    }
}
