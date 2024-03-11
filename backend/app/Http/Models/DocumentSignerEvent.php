<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentSignerEvent extends Model
{
    use SoftDeletes;

    public $months = [
        'Jan' => 'Yan',
        'Feb' => 'Fev',
        'Mar' => 'Mar',
        'Apr' => 'Apr',
        'May' => 'May',
        'Jun' => 'Iyun',
        'Jul' => 'Iyul',
        'Aug' => 'Avg',
        'Sep' => 'Sen',
        'Oct' => 'Okt',
        'Nov' => 'Noy',
        'Dec' => 'Dek',
    ];


    protected $appends = ['signed_at'];

    // // protected $guarded = ['base64'];
    // protected $hidden = ['base64', 'pdf', 'b64','eimzoinfo'];

    public function getSignedAtAttribute(){
        // 18-Fev. 2021y. 11:40
        $month = date('M', strtotime($this->created_at));
        return date('d-', strtotime($this->created_at)).$this->months[$month].date('. Y\y. H:i', strtotime($this->created_at));
    }

    public function documentSigner()
    {
        return $this->hasOne('App\Http\Models\DocumentSigner', 'id', 'document_signer_id');
    }
    public function document()
    {
        return $this->hasMany('App\Http\Models\Document', 'id', 'document_id');
    }

    public function files()
    {
        return $this->hasMany('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', 6);
    }
}
