<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDetailContent extends Model
{
    //
    public function documentDetailAttribute()
	{
		return $this->hasOne('App\Http\Models\DocumentDetailAttribute', 'id', 'd_d_attribute_id');
    }
}
