<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DocumentDetail;

class DocumentDetailCoefficient extends Model
{
    public $timestamps = false;
    /**
     * Get the user associated with the DocumentDetailCoefficient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function documentDetail()
    {
        return $this->hasOne(DocumentDetail::class, 'id', 'document_detail_id');
    }

    public function tariffScale()
    {
        return $this->hasOne(Coefficient::class, 'id', 'tariff_scale_id');
    }
}
