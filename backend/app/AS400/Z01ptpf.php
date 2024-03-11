<?php

namespace App\AS400;

use Illuminate\Database\Eloquent\Model;

class Z01ptpf extends Model
{
    protected $connection= 'db2';

    protected $table = 'zarlib.z01ptpf';
    public $timestamps = false;
}
