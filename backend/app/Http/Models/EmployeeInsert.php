<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeInsert extends Model
{
  public $table = 'employees';
  protected $connection= 'mysql_workflow';
  public $timestamps = false;
}
