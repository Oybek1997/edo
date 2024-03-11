<?php

namespace App\AS400;

use Illuminate\Database\Eloquent\Model;

class Z500ptpf extends Model
{
    protected $connection= 'db2';

    protected $table = 'zarlib.z500ptpf';
    public $timestamps = false;
}
