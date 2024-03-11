<?php

namespace App\Http\Models\ME;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Modelme extends Model
{
    protected $connection = 'pg_man_enginering';
    public $timestamps = false;
    public $table = 'modelme';

    public function createdBy()
    {
        return $this->setConnection('pgsql')->hasOne(User::class, 'id', 'created_by');
    }
}