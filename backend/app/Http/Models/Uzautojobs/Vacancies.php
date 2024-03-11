<?php

namespace App\Http\Models\Uzautojobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacancies extends Model
{
    protected $connection = 'pg_uzautojobs';
    protected $table = 'vacancies';

    // protected $fillable = ['address_name',];

    public $timestamps = false;
    // use SoftDeletes;
   
}
