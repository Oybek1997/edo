<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SignerGroupDetail extends Model
{


	public function signerGroup()
	{
		return $this->hasOne('App\Http\Models\SignerGroup', 'id', 'signer_group_id');
	}



	public function staff()
	{
		return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id');
	}

	public function actionType()
	{
		return $this->hasOne('App\Http\Models\ActionType', 'id', 'action_type_id');
	}

}
