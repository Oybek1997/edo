<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportTemplate extends Model
{
	use SoftDeletes;
    public function reportColumns()
	{
		return $this->hasMany('App\Http\Models\ReportColumnTemplate', 'report_template_id', 'id');
	}
}
