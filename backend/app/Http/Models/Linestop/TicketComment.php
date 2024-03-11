<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'ticket_comment';
    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'created_at',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    public function ticketCommentFile()
    {
        return $this->hasMany(TicketCommentFile::class);
    }
    public function employee()
    {
        return $this->setConnection('pgsql')
            ->hasOne('App\Http\Models\Employee', 'id', 'created_by');
    }
}
