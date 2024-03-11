<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionType extends Model
{
    use SoftDeletes;
    //
    public $fillable = [
        'code',
        'name_uz_latin',
        'name_uz_cyril',
        'name_ru',
    ];
}
