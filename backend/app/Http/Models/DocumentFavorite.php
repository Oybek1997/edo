<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentFavorite extends Model
{
  public $timestamps = false;
  protected $table = 'document_favorites';
  //
  public function documentType()
  {
    return $this->hasMany('App\Http\Models\DocumentType', 'id', 'document_tamplate_id ');
  }
  public function userIDs()
  {
    return $this->hasOne('App\User', 'id', 'user_id ');
  }
}
