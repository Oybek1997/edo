<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailQueue extends Model
{
    public $timestamps = false;
    protected $connection= 'mysql_workflow_log';
    // protected $table = 'zarlib.z08ptpf';
    // public $timestamps = false;
    //
    public $fillable = [
        'address',
        'content',
        'title',
        'status',
        'try_count',
    ];
}
