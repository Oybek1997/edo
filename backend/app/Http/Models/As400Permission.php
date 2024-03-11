<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class As400Permission extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'query_id',
        'employee_id',
    ];

    /**
     * Get the user associated with the As400Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}
