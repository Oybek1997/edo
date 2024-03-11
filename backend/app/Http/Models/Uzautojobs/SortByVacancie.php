<?php

namespace App\Http\Models\Uzautojobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SortByVacancie extends Model
{
    protected $connection = 'pg_uzautojobs';
    protected $table = 'sort_by_vacancie';

    protected $fillable = ['address_name','deleted_at'];

    public $timestamps = false;

    // use SoftDeletes;
    public function vacancies()
    {
        return $this->hasOne('App\Http\Models\Uzautojobs\Vacancies', 'id', 'vacancies_id')
        ;
    }
    public function message()
    {
        return $this->hasOne('App\Http\Models\Uzautojobs\itemsMessage', 'id', 'status_type')
        ;
    }
   
    public function choice()
    {
        return $this->hasOne('App\Http\Models\Uzautojobs\SelectionStatus', 'uzautoJobs_id', 'uzautoJobs_id')
        ->orderBy('id','DESC')
        ->with('tanlov')
        ->with('staff')
        ->with('staffCoefficients')
        ;
    }
   
}
