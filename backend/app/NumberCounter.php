<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NumberCounter extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.number_counters';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    public static function getNumber($code)
    {
        $counter = Self::where('year', date('Y'))->where('code', $code)->first();
        if ($counter) {
            $counter->counter++;
            $counter->save();
            return $code.date('y').str_pad($counter->counter, 5, "0", STR_PAD_LEFT);
        } else {
            $counter = new Self;
            $counter->counter = 1;
            $counter->year = date('Y');
            $counter->code = $code;
            $counter->save();
            return $code.date('y').str_pad(1, 5, "0", STR_PAD_LEFT);
        }
    }
}
