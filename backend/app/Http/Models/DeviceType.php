<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    protected $connection= 'workflow_org_texnika';
    public $timestamps = false;

    public function deviceBranch()
    {
        return $this->hasOne('App\Http\Models\DeviceBranch', 'id', 'device_branch_id');
    }
}
