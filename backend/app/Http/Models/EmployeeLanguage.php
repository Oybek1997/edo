<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLanguage extends Model
{
    use SoftDeletes;
    //

    public function hrLanguage()
    {
        return $this->hasOne('App\Http\Models\HrLanguage', 'id', 'hr_language_id');
    }
}
