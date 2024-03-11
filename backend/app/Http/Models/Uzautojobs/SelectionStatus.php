<?php

namespace App\Http\Models\Uzautojobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelectionStatus extends Model
{
    
    protected $connection = 'pg_uzautojobs';
    protected $table = 'selection_status';
    public $timestamps = false;

    protected $fillable = ['tanlov_id','status','created_by','created_at','uzautoJobs_id','staff_id',];
    public function tanlov()
    {
        return $this->hasOne('App\Http\Models\Uzautojobs\CandidateSubmitted', 'id', 'tanlov_id')
        ;
    }
    public function staffCoefficients()
    {
        return $this->hasMany('App\Http\Models\StaffCoefficient', 'staff_id', 'staff_id')
        ->with('coefficient')
        ;
    }
    public function staff()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Staff', 'id', 'staff_id')
        ->select(
            'id',
            'department_id',
            'position_id',
            'range_id',
            'rate_count',
            'rate_count_bp',
            'rate_count_sv',
            'rate_count_reserved'
        )
        ->with('range')
        ->with([
            'department' => function ($qu) {
                $qu->with('branch:id,name');
                $qu->with('functionalDepartment:id,name_uz_latin');
                $qu->where('is_active', 1);
                $qu->select(
                    'id', 
                    'name_uz_latin', 
                    'branch_id', 
                    'functional_department_id', 
                    'department_code'
                    );
                
            }])
        ->with('position:id,name_uz_latin')
        ;
        
    }
    // use SoftDeletes;
   
}
