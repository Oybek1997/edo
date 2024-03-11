<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'reason';
    
    public function category()
    {
        return $this->belongsTo(TicketDepartment::class, 'category_id', 'id');
    }
}
