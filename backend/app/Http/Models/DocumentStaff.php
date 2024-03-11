<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentStaff extends Model
{
    public $timestamps = false;

    public function document()
    {
        return $this->hasOne('App\Http\Models\Document', 'id', 'document_id')->whereIn('status', [3,4,5]);
    }
}
