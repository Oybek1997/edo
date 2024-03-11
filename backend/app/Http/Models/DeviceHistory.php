<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceHistory extends Model
{
    protected $connection= 'workflow_org_texnika';
    public $timestamps = true;

    public function device()
    {
        return $this->hasOne('App\Http\Models\Device', 'id', 'device_id');
    }

    public function employee()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    public function createdBy()
    {
        return $this->setConnection('pgsql')->hasOne('App\User', 'id', 'created_by');
    }
}
