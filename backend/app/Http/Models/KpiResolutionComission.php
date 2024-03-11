<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class KpiResolutionComission extends Model
{
    public function kpiObjektresolution()
    {
        return $this->hasOne('App\Http\Models\KpiObject', 'id', 'kpi_object_id');
    }
    public function resolutionEmployee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'resolution_id');
    }

    /**
     * Get all of the comments for the KpiResolutionComission
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(KpiComment::class, 'kpi_object_id', 'kpi_object_id');
    }
    
}
