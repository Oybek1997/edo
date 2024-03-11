<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use SoftDeletes;
}
