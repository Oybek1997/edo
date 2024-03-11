<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
/**
 *
 */
class DocumentDetailAttribute extends Model
{

    // public static function create(int $document_detail_template_id, int $data_type_id, string $attribute_name_uz_latin): self
    // {
    //     $doc = new static();
    //     $doc->document_detail_template_id = $document_detail_template_id;
    //     $doc->attribute_name_uz_latin = $attribute_name_uz_latin;
    //     $doc->data_type_id = $data_type_id;

    //     return $doc;
    // }

    public function dataType()
    {
        return $this->hasOne('App\Http\Models\DataType', 'id', 'data_type_id');
    }

	public function tableList()
	{
		return $this->hasOne('App\Http\Models\TableList', 'id', 'table_list_id');
	}

	public function documentDetailTemplate()
	{
		return $this->hasOne('App\Http\Models\DocumentDetailTemplate', 'id', 'document_detail_template_id');
    }
    
    public function signerStaffIds()
    {
        return $this->hasMany('App\Http\Models\AttributeSignerStaff', 'attribute_id', 'id');
    }    

}
