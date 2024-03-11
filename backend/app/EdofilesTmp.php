<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdofilesTmp extends Model
{
    use SoftDeletes;
    public $fillable=[
        'name',
        'number',
        'created_at',
    ];
}
