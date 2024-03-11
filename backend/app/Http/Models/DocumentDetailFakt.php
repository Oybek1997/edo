<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDetailFakt extends Model
{
    public function comissions()
    {
        return $this->hasMany(KpiFactComission::class, 'd_d_fakt_id', 'id');
    }
    public function documentDetail()
    {
        return $this->hasOne(DocumentDetail::class, 'id', 'd_d_id');
    }

    // public function kpiObjekt()
    // {
    //     return $this->hasOne('App\Http\Models\KpiObject', 'id' , 'kpi_objects_id');
    // }
}
