<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeMilitary extends Model
{

    protected $table = 'employee_militaries';
    // public $timestamps = false;
    use SoftDeletes;



    public function employee()
    {
        return $this->hasOne(Employee::class,'id','employee_id');
    }

  
    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d H:i',
    //     'updated_at' => 'datetime:Y-m-d H:i',
    //     'deleted_at' => 'datetime:Y-m-d H:i',
    // ];
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}






