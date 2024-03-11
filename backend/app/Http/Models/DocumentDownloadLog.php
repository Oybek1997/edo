<?php

namespace App\Http\Models;;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentDownloadLog extends Model
{
    // use SoftDeletes;
    public $timestamps = false;
    //
    
    public function document()
    {
        return $this->hasOne('App\Http\Models\Document', 'id', 'document_id');
    }
}
