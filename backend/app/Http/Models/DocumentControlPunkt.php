<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentControlPunkt extends Model
{
    // public function getSigners()
    // {
    //     return DocumentSigner::where('control_punkt_id', $this->id);
    // }

    public function controller()
    {
        return $this->hasOne('App\Http\Models\DocumentSigner', 'id', 'controller_id');
    }

    public function documentSigners()
    {
        return $this->hasMany('App\Http\Models\DocumentSigner', 'control_punkt_id', 'id');
    }
}
