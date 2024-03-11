<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnersModel extends Model
{
    use SoftDeletes;
    protected $table = "partners";

    public $timestamps = false;

    protected $fillable = [
        'name',
        'adress',
        'bank_name',
        'bank_adress',
        'account',
        'swift_code'
    ];
}
