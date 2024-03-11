<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDetailTemplate extends Model
{
    public function documentDetailAttributes()
	{
		return $this->hasMany('App\Http\Models\DocumentDetailAttribute', 'document_detail_template_id', 'id')
		->where('is_active',1)
		->orderBy('sequence', 'asc')
		;
	}
}