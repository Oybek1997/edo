<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class TicketDepartment extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'category';

    public function reason()
    {
        return $this->hasOne(Reason::class, 'category_id', 'id');
    }
    
}
