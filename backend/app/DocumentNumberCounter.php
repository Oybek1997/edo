<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentNumberCounter extends Model
{
    public static function newDocumentNumber(String $code_old, $digital = 4)
    {
        $model = Self::where('code', $code_old)
            ->where('year', date('Y'))
            ->first();
        if ($model) {
            $model->count++;
            $model->save();
        } else {
            $model = new Self();
            $model->count = 1;
            $model->code = $code_old;
            $model->year = date('Y');
            $model->save();
        }
        return str_pad($model->count, $digital, "0", STR_PAD_LEFT);
    }
}
