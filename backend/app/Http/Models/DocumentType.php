<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    public function documentTemplate()
	{
		return $this->hasMany('App\Http\Models\DocumentTemplate', 'document_type_id', 'id');
	}
}