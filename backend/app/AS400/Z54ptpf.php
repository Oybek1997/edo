<?php

namespace App\AS400;

use Illuminate\Database\Eloquent\Model;

class Z54ptpf extends Model
{
    protected $connection= 'db2';

    protected $table = 'zarlib.z54ptpf';
    public $timestamps = false;

    protected $primaryKey = null;
    public $incrementing = false;
}
