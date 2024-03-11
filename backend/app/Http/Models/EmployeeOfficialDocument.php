<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOfficialDocument extends Model
{
    use SoftDeletes;
    //
    public function employee()
	{
		return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    
    public function officialDocumentType()
    {
        return $this->hasOne('App\Http\Models\OfficialDocumentType', 'id', 'official_document_type_id');
    }
    
    public function files()
    {
        return $this->hasMany('App\Http\Models\File', 'object_id', 'id');
    }
}
