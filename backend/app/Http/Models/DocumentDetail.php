<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentDetail extends Model
{
    use SoftDeletes;

    public function document()
    {
        return $this->hasOne('App\Http\Models\Document', 'id', 'document_id');
    }

    public function documentDetailFakt()
    {
        return $this->hasOne('App\Http\Models\DocumentDetailFakt', 'd_d_id', 'id');
    }
    
    public function kpiComments()
    {
        return $this->hasMany('App\Http\Models\KpiComment', 'd_d_id', 'id');
    }  
    public function kpiPlanComissions()
    {
        return $this->hasMany('App\Http\Models\KpiPlanComission', 'd_d_id', 'id');
    }  

    public function documentDetailAttributeValues()
    {
        return $this->hasMany('App\Http\Models\DocumentDetailAttributeValue', 'document_detail_id', 'id')->orderBy('document_detail_attribute_values.id','asc');
    }

    public function documentDetailCoefficients()
    {
        return $this->hasMany('App\Http\Models\DocumentDetailCoefficient', 'document_detail_id', 'id');
    }

    public function documentDetailContents()
    {
        return $this->hasMany('App\Http\Models\DocumentDetailContent', 'document_detail_id', 'id');
    }

    public function documentDetailEmployees()
    {
        return $this->hasMany('App\Http\Models\DocumentDetailEmployee', 'document_detail_id', 'id');
    }

    public function documentDetailSignerAttributes()
    {
        return $this->hasMany('App\Http\Models\DocumentDetailSignerAttribute', 'document_detail_id', 'id')
        ->whereHas('documentDetailAttributes',function($q){
            $q->where('is_active', 1);
        })
        ;
    }

    public function documentSigners()
    {
        return $this->hasMany('App\Http\Models\DocumentSigner', 'document_id', 'document_id');
    }
}
