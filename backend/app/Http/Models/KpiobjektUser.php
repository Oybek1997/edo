<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class KpiobjektUser extends Model
{
    use SoftDeletes;

    public function kpiobjekt()
    {
        return $this->hasOne('App\Http\Models\KpiObject', 'id', 'kpi_objects_id');
    }
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
