<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRelative extends Model
{
    //
    public function employee()
	{
		return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    
    public function familyRelative()
    {
        return $this->hasOne('App\Http\Models\FamilyRelative', 'id', 'family_relative_id');
    } 
    public $timestamps = false;
}
