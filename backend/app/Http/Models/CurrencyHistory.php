<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyHistory extends Model
{


    public function currency()
    {
        return $this->hasOne('App\Http\Models\Currency', 'id', 'currency_id');
    }
}
