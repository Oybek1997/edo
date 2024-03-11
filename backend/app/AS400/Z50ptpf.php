<?php

namespace App\AS400;

use Illuminate\Database\Eloquent\Model;

class Z50ptpf extends Model
{
    protected $connection= 'db2';

    protected $table = 'zarlib.z50ptpf';
    public $timestamps = false;
}