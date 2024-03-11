<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSignerTemplate extends Model
{
    public function staff()
	{
		return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id');
    }
    public function actionType()
	{
		return $this->hasOne('App\Http\Models\ActionType', 'id', 'action_type_id');
    }
}
