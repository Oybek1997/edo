<?php

namespace App\AS400;

use Illuminate\Database\Eloquent\Model;

class Z08ptpf extends Model
{
    protected $connection= 'db2';

    protected $table = 'zarlib.z08ptpf';
    public $timestamps = false;
}
