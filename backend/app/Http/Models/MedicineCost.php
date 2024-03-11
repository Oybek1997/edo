<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineCost extends Model
{
    use SoftDeletes;
    protected $connection= 'workflow_medpunkt';

    public function medicines(){
        return $this->hasMany('App\Http\Models\Medicine', 'id', 'medicine_id');
    }
}
