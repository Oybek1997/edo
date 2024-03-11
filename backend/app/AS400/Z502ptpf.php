<?php

namespace App\AS400;

use Illuminate\Database\Eloquent\Model;

class Z502ptpf extends Model
{
    protected $connection= 'db2';

    protected $table = 'zarlib.z502ptpf';
    public $timestamps = false;
}
