<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class TicketFile extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'ticket_file';
    protected $fillable = [
        'ticket_id',
        'pythiscal_name',
        'created_at',
    ];

    public function ticketFiles()
    {
        return $this->belongsTo(Ticket::class);
    }
}
