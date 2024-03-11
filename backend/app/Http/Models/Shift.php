<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use SoftDeletes;
    public $timestamps = false;


}