<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;
    protected $connection= 'workflow_org_texnika';
    public $timestamps = true;

    public function deviceType()
    {
        return $this->hasOne('App\Http\Models\DeviceType', 'id', 'device_type_id');
    }

    public function deviceBranch()
    {
        return $this->hasOne('App\Http\Models\DeviceBranch', 'id', 'device_branch_id');
    }

    public function createdBy()
    {
        return $this->setConnection('pgsql')->hasOne(User::class, 'id', 'created_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DeviceHistory::class, 'device_id', 'id');
    }

    public function history()
    {
        return $this->hasOne(DeviceHistory::class, 'device_id', 'id')->orderBy('id','desc');
    }
}
