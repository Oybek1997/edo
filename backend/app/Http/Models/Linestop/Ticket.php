<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $connection = 'linestop';
    public $table = 'ticket';
    public $timestamps = false;
    protected $fillable = [
        'plcdata_id', 'reason_id', 'provider_id', 'product_id', 'detail_number', 'duration', 'description','status', 'responsible_id'
    ];
    public function plcdata()
    {
        return $this->belongsTo(Plcdata::class, 'plcdata_id');
    }
    public function ticketUser()
    {
        return $this->hasMany(TicketUser::class);
    }

    public function ticketComment()
    {
        return $this->hasMany(TicketComment::class);
    }
    public function ticketFile()
    {
        return $this->hasMany(TicketFile::class);
    }
}
