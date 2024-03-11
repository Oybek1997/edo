<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Notsigner extends Model
{

	public function staff()
    {
        return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id');
    }
	public function documentTemplate()
    {
        return $this->hasOne('App\Http\Models\DocumentTemplate', 'id', 'document_template_id');
    }
	public function documentType()
    {
        return $this->hasOne('App\Http\Models\DocumentType', 'id', 'document_type_id');
    }

	// public function signerGroupDetails()
	// {
	// 	return $this->hasMany('App\Http\Models\SignerGroupDetail', 'signer_group_id', 'id');
	// }

}
