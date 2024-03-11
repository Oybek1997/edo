<?php

namespace App\Http\Models\ME;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Status extends Model
{
    protected $connection = 'pg_man_enginering';
    public $timestamps = false;
    public $table = 'status';

    public function createdBy()
    {
        return $this->setConnection('pgsql')->hasOne(User::class, 'id', 'created_by');
    }
}
