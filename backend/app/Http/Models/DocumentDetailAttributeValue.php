<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentDetailAttributeValue extends Model
{
  use SoftDeletes;
  //
  public function documentDetailAttributes()
  {
    return $this->hasOne('App\Http\Models\DocumentDetailAttribute', 'id', 'd_d_attribute_id');
  }

  public function documentDetail()
  {
    return $this->hasOne('App\Http\Models\DocumentDetail', 'id', 'document_detail_id');
  }
}