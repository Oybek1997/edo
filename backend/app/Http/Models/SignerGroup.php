<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SignerGroup extends Model
{


	public function signerGroupDetails()
	{
		return $this->hasMany('App\Http\Models\SignerGroupDetail', 'signer_group_id', 'id');
	}

}
