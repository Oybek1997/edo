<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

class ChiefHelper extends Model
{
    public $timestamps = false;
    /**
     * Get the user associated with the ChiefHelper
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function chiefEmployee()
    {
        return $this->hasOne(Employee::class, 'id', 'chief_employee_id');
    }

    /**
     * Get the user associated with the ChiefHelper
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function helperEmployee()
    {
        return $this->hasOne(Employee::class, 'id', 'helper_employee_id');
    }
}