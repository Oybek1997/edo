<?php

namespace App\http\models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class KpiComment extends Model
{
    use SoftDeletes;
    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    public function kpiObjekt()
    {
        return $this->hasOne('App\Http\Models\KpiObject', 'id', 'kpi_object_id');
    }
    public function files()
    {
        return $this->hasMany('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', 16);
    }
}
