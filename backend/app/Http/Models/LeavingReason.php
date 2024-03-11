<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class LeavingReason extends Model
{
    protected $fillable = ['name_uz_latin', 'name_uz_cyril', 'name_ru'];
}
