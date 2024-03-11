<?php

namespace App\Http\Models\SapIntegration;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Workplace extends Model
{
    // public $timestamps = false;
    // use SoftDeletes;
    protected $connection= 'workflow_sap';
    // public $table="workflow_sap.nelekvids";
    // protected $table = 'nelekvids';
    // public function workplace(){
    //     return $this->hasOne('App\Http\Models\SapIntegration\Workplace', 'id', 'workplace_id');
    // }
}
