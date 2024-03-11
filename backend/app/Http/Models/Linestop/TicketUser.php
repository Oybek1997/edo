<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class TicketUser extends Model
{
    protected $connection = 'linestop';
    public $table = 'ticket_user';
    public $timestamps = false;
    protected $fillable = [
        'ticket_id',
        'staff_id',
        'employee_id',
        'parent_employee_id',
        'parent_staff_id',
        'status',
        'task',
        'response_to_task',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function staff()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Staff'::class, 'id', 'staff_id');
    }

    public function employee()
    {
        return $this->setConnection('pgsql')
            ->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    public function parentStaff()
    {
        return $this->setConnection('pgsql')
            ->hasOne('App\Http\Models\Staff', 'id', 'parent_staff_id');
    }
    public function parent_employee()
    {
        return $this->setConnection('pgsql')
            ->hasOne('App\Http\Models\Employee', 'id', 'parent_employee_id');
    }
}
