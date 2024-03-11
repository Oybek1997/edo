<?php

namespace App\Http\Models\Uzautojobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class itemsMessage extends Model
{
    protected $connection = 'pg_uzautojobs';
    protected $table = 'items_message';
    public $timestamps = false;

    // protected $fillable = ['tanlov_id','status','created_by','created_at','uzautoJobs_id','staff_id',];
    
    // use SoftDeletes;
   
}
