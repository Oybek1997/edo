<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Insert extends Model
{
    public $table = 'reg_computer';
    protected $connection= 'mysql_orgtex_asaka';
    public $timestamps = true;

    public function deviceType()
    {
        return $this->hasOne('App\Http\Models\DeviceType', 'id', 'device_type_id');
    }

    public function createdBy()
    {
        return $this->setConnection('mysql')->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * Get all of the comments for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(DeviceHistory::class, 'device_id', 'id');
    }
    /**
     * Get the user associated with the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastHistory(): HasOne
    {
        return $this->hasOne(DeviceHistory::class, 'device_id', 'id')->latest();
    }
}
