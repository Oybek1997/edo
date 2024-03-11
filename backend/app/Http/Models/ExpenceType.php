<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenceType extends Model
{
    use SoftDeletes;
    //
    public $fillable = [
        'name_uz_latin',
        'name_uz_cyril',
        'name_ru',
    ];
}
