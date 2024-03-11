<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SelectedTemplates extends Model
{
    public function documentTemplate()
    {
        return $this->hasOne('App\Http\Models\DocumentTemplate', 'id', 'document_template_id');
    }
}
