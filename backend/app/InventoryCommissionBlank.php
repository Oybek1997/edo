<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryCommissionBlank extends Model
{
    protected $connection = 'workflow_log';
    public $timestamps = false;

    public function commission()
    {
        return $this->hasOne('App\InventoryCommission', 'id', 'commission_id');
    }

}
