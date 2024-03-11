<?php

namespace App\AS400;

use Illuminate\Database\Eloquent\Model;

class Oyliksts extends Model
{
    protected $connection= 'db2';

    protected $table = 'zarlib.Oyliksts';
    public $timestamps = false;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'yiloy', 'sts'
    ];
}
