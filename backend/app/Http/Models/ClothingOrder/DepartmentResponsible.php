<?php

namespace App\Http\Models\ClothingOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentResponsible extends Model
{
    protected $connection = 'clothing_order';
    protected $table = 'department_responsibles';
    // public $timestamps = false;
    use SoftDeletes;

    public function responsible()
    {
        return $this->hasOne(\App\User::class, 'id', 'responsible_id');
    }

    public function department()
    {
        return $this->hasOne(\App\Http\Models\Department::class, 'id', 'department_id');
    }

    public function createdBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}
