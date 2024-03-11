<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class Plcdata extends Model
{
    protected $connection= 'linestop';
    public $timestamps = false;
    public $table = 'plcdata';

    public function line()
    {
        return $this->belongsTo(Line::class, 'lineid', 'id');
    }
    
    public function ticket()
    {
        return $this->hasOne(Ticket::class, 'plcdata_id');
    }
    
}
