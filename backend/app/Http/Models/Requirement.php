<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requirement extends Model
{
    use SoftDeletes;
    public function requirementType()
    {
	    return $this->hasOne('App\Http\Models\RequirementType', 'id', 'requirement_type_id');

    }
}
