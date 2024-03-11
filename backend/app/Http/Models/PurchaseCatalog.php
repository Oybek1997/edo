<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseCatalog extends Model
{
    public  $timestamp = false;
    public function measure()
    {
        return $this->hasOne('App\Http\Models\UnitMeasure', 'id', 'unit_measure_id');
    }

}
