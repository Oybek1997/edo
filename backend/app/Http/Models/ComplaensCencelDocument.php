<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Document;
class ComplaensCencelDocument extends Model
{
    //
    /**
     * Get the user associated with the ComplaensCencelDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function reasonDocument()
    {
        return $this->hasOne(Document::class, 'id', 'reason_document_id');
    }
}