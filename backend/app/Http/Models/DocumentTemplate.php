<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentTemplate extends Model
{

	public function userTemplates()
    {
        return $this->hasMany('App\Http\Models\UserTemplate', 'document_template_id', 'id');
    }

	public function documentType()
	{
		return $this->hasOne('App\Http\Models\DocumentType', 'id', 'document_type_id');
    }
	public function favorites(){
		return $this->hasOne('App\Http\Models\DocumentFavorite', 'document_template_id', 'id')->where('user_id', Auth::id());
	}
	public function department()
	{
		return $this->hasOne('App\Http\Models\Department', 'id', 'department_id');
	}

	public function documentSignerTemplates()
	{
		return $this->hasMany('App\Http\Models\DocumentSignerTemplate', 'document_template_id', 'id');
	}

	public function documentDetailTemplates()
	{
		return $this->hasMany('App\Http\Models\DocumentDetailTemplate', 'document_template_id', 'id');
	}
	
	public function documents()
	{
		return $this->hasMany('App\Http\Models\Document', 'document_template_id', 'id');
	}

    public function signerGroups()
    {
        return $this->belongsToMany('App\Http\Models\SignerGroup', 'document_template_signer_group', 'document_template_id', 'signer_group_id');
    }
}
