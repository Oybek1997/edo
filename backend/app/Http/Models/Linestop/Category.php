<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'category';
}
