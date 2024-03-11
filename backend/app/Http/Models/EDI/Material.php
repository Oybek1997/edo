<?php

namespace App\Http\Models\EDI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.materials';
    // public $timestamps = false;
    use SoftDeletes;
    


    public function stockParameter()
    {
        return $this->hasOne(StockParameter::class,'id','material_id');
    }

    public function unitMeasure()
    {
        return $this->hasOne(UnitMeasure::class, 'id', 'unit_measure_id');
    }

    public function materialType()
    {
        return $this->hasOne(MaterialType::class, 'id', 'material_type_id');
    }

    public function materialGroup()
    {
        return $this->hasOne(MaterialGroup::class, 'id', 'material_group_id');
    }
    public function createdBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
        'deleted_at' => 'datetime:Y-m-d H:i',
    ];
}
