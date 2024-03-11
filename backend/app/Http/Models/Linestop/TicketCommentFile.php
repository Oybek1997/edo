<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class TicketCommentFile extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'ticket_comment_file';
    protected $fillable = [
        'ticket_comment_id',
        'pythiscal_name',
        'created_at',
    ];

    public function ticketCommentFile()
    {
        return $this->belongsTo(ticketComment::class);
    }
}
