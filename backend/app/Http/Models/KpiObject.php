<?php

namespace App\http\models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Employee;
use App\Http\Models\Document;
use App\User;
use App\Http\Models\Department;

class KpiObject extends Model
{
    // public function files()
    // {
    //     return $this->hasMany('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', 13);
    // }
    public function departmentResolution()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'dep2_id');
    }
    public function kpiobjektuser()
    {
        return $this->hasOne('App\Http\Models\KpiobjektUser', 'kpi_objects_id', 'id');
    }
    public function dep()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'dep_id');
    }
    public function document()
    {
        return $this->hasOne('App\Http\Models\document', 'id', 'doc_id');
    }
    public function department()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'department_id');
    }

    public static function kpiCreatedObjekt($id)
    {
        $document = Document::where('id', $id)
            ->with(['documentDetails' => function ($q) {
                $q
                    // ->select('id','document_detail_id')
                    // ->with('documentDetailAttributeValues');
                    ->with('documentDetailContents');
            }])
            ->first();
        // var_dump($document); die();
        //   dd($document);
        // dd($document->documentDetails);


        for ($x = 1; $x <= 4; $x++) {
            $kpi_object = new KpiObject();
            $kpi_object->doc_id = $document->id;
            $kpi_object->dep_id = $document->department2_id;
            $kpi_object->quarter = $x;
            $kpi_object->years = date('Y');
            $kpi_object->save();
            echo $kpi_object;
        }
    }

    public static function getDepID($tabel)
    {
        $abs = Employee::parentDepartments($tabel);
        $signer = null;
        foreach ($abs['manager_staff'] as $a) {
            if ($a['department_type_id'] != 2 && $a['department_type_id'] != 1 && $a['manager_staff_id']) {
                $signer = $a;
            }
        }
        
        // dd($signer);
        // dd($abs['manager_staff']);
        return $signer;
    }
}
