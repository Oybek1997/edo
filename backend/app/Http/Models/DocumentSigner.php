<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Auth;

class DocumentSigner extends Model
{
    // use SoftDeletes;
    //
    // protected $appends = ['fio_short'];

    // protected $hidden = ['fio_short'];

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

    public $casts = [
        'updated_at' => 'datetime',
    ];

    // protected $casts = [
    //     'document_date' => 'date:Y-m-d',
    // ];

    protected $appends = ['signed_at', 'taken_at', 'due_at', 'employee_phone'];

    // // protected $guarded = ['base64'];
    // protected $hidden = ['base64', 'pdf', 'b64','eimzoinfo'];

    public function getSignedAtAttribute()
    {
        // 18-Fev. 2021y. 11:40

        if (!$this->signed_date) {
            return '';
        }
        $month = date('M', $this->signed_date);
        return date('d-', $this->signed_date).$this->months[$month].date('. Y\y. H:i', $this->signed_date);
    }

    public function getEmployeePhoneAttribute()
    {
        if($this->signer_employee_id){

            $employee = Employee::where('id', $this->signer_employee_id)->first();
            $employee_phone = Phonebook::where('name', 'like', "%" . $employee->tabel . "%")->first();
            if($employee_phone){

                return $employee_phone->phone;
            }
            else{
                return '';
            }
        }
        else{
            $employee = EmployeeStaff::where('staff_id', $this->staff_id)->where('is_active', 1)->orderBy('is_main_staff', 'desc')->first() && EmployeeStaff::where('staff_id', $this->staff_id)->where('is_active', 1)->orderBy('is_main_staff', 'desc')->first()->employee ? EmployeeStaff::where('staff_id', $this->staff_id)->where('is_active', 1)->orderBy('is_main_staff', 'desc')->first()->employee->tabel : '';
            $employee_phone = Phonebook::where('name', 'like', "%" . $employee . "%")->first();
            if($employee_phone){

                return $employee_phone->phone;
            }
            else{
                return '';
            }
        }
    }
    public function getTakenAtAttribute()
    {
        // 18-Fev. 2021y. 11:40
        $month = date('M', strtotime($this->taken_datetime));
        return date('d-', strtotime($this->taken_datetime)).$this->months[$month].date('. Y\y. H:i', strtotime($this->taken_datetime));
    }

    public function getDueAtAttribute()
    {
        // 18-Fev. 2021y. 11:40

        $month = date('M', strtotime($this->due_date));
        return date('d-', strtotime($this->due_date)).$this->months[$month].date('. Y\y. H:i', strtotime($this->due_date));
    }

    public function staffs()
    {
        return $this->hasMany('App\Http\Models\Staff', 'id', 'staff_id');
    }

    public function staff()
    {
        return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id');
    }

    public function actionTypes()
    {
        return $this->hasMany('App\Http\Models\ActionType', 'id', 'action_type_id');
    }

    /**
     * Get the user associated with the DocumentSigner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function actionType()
    {
        return $this->hasOne('App\Http\Models\ActionType', 'id', 'action_type_id');
    }
    
    public function employeeStaffs()
    {
        return $this->hasOne('App\Http\Models\EmployeeStaff', 'staff_id', 'staff_id')->where('is_active', 1)->orderBy('is_main_staff', 'asc');
    }

    public function parentEmployee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'parent_employee_id');
    }

    public function parentNazorat()
    {
        return $this->hasOne('App\Http\Models\DocumentSigner', 'document_id', 'document_id')->where('action_type_id',11);
    }

    public function signerEmployee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'signer_employee_id');
    }

    public function documents()
    {
        return $this->hasOne('App\Http\Models\Document', 'id', 'document_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Http\Models\DocumentSignerEvent', 'document_signer_id', 'id');
    }

    public function files()
    {
        return $this->hasMany('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', 6);
    }

    public static function tree($id, $docId)
    {
        $document_signers = DocumentSigner::where('parent_employee_id', $id)
                                ->where('document_id', $docId)
                                ->where('signer_employee_id', '!=', 'null')
                                ->with('signerEmployee')
                                ->with('staffs.position')
                                ->with('staffs.department')
                                ->orderBy('updated_at', 'asc')
                                ->get();
        $arr = [];
        foreach ($document_signers as $key => $value) {
            $arr[$key] = $value;
            if ($value->signer_employee_id) {
                $arr[$key]['children'] = DocumentSigner::tree($value->signer_employee_id, $docId);
            } else {
                $arr[$key]['children'] = [];
            }
        }
        return $arr;
    }

    public static function getFromTo($staff, $locale){
        
        $staff = Staff::find($staff);
        return
        [
            $department = $staff->department['name_' . $locale],
            $employee = $staff->employeeStaff[0]->employee['lastname_'.$locale] . ' '. $staff->employeeStaff[0]->employee['firstname_'.$locale]. ' ' . $staff->employeeStaff[0]->employee['middlename_'.$locale],
            $position = $staff->position['name_' . $locale],
        ];
    }
    public static function getTo($document_signers, $locale){

        // $depStaff = Department::where('parent_id', 1421)->pluck('manager_staff_id')->toArray();
        $depStaff = Department::whereHas('departmentType', function($q) {
            $q->where('sequence', '<', 6);
        })->pluck('manager_staff_id')->reject(function ($value) {
            return is_null($value);
        })->toArray();
        $collection = collect($document_signers);
        $filteredCollection = $collection->filter(function ($item, $key) use ($depStaff) {
            return in_array($item['staff_id'], $depStaff) && !in_array($item['action_type_id'], [3, 6]) 
                   || $item['sequence'] != 0 && !in_array($item['action_type_id'], [3, 6]);
        });
        // if(in_array(Auth::user()->employee_id, [11385])){
        //     $filteredCollection = $collection->filter(function ($item, $key) use ($depStaff) {
        //         return in_array($item['staff_id'], $depStaff) && !in_array($item['action_type_id'], [3, 6]) 
        //                || $item['sequence'] != 0 && !in_array($item['action_type_id'], [3, 6]);
        //     });           
        // }else{
        //     $filteredCollection = $collection->where('sequence', '!=', 0)->whereNotIn('action_type_id', [3,6]);
        // }
        if(count($filteredCollection)){
            $sequence = $filteredCollection->min('sequence');
            $document_signer = $collection->where('sequence', $sequence)->first();
            
            // return
    
            $document_signer['staff_id'];
            $staff = Staff::find($document_signer['staff_id']);
            // dd($staff);
            return
            [
                $department = $staff->department['name_' . $locale],
                $employee = $staff->employeeStaff[0]->employee['lastname_'.$locale] . ' '. $staff->employeeStaff[0]->employee['firstname_'.$locale]. ' ' . $staff->employeeStaff[0]->employee['middlename_'.$locale],
                $position = $staff->position['name_' . $locale],
            ];
        }
        else{
            return
            [
                $department =  false,
                $employee = false,
                $position = false,
            ];
            
        }
    }
    public static function getFromS($created_employee_id ,$document_signers, $locale){

        $document_signers = collect($document_signers);
        $document_signers_from = $document_signers->whereIn('action_type_id', [3])
        ->where('sequence', 100)
        ;
        $min = 100;
        $minStaff = null;
        foreach($document_signers_from as $document_signer){
            $staff = Staff::find($document_signer['staff_id']);
            $dep = $staff->department;
            if($staff->department->departmentType->sequence <= $min){
                $min = $staff->department->departmentType->sequence;
                $minStaff = $staff;
            }
        }
        
        $signer =  $document_signers->whereIn('action_type_id', [6])->first();
        $stafff = Staff::find($signer['staff_id']);
        $emp = Employee::find($created_employee_id);


        return
        [
            $department = $minStaff && $minStaff->department ? $minStaff->department['name_' . $locale] : $stafff->department['name_' . $locale],
            $employee = $minStaff ? 
            $minStaff->employeeStaff[0]->employee['lastname_'.$locale] . ' '. $minStaff->employeeStaff[0]->employee['firstname_'.$locale]. ' ' . $minStaff->employeeStaff[0]->employee['middlename_'.$locale]: 
            // $stafff->employeeStaff[0]->employee['lastname_'.$locale] . ' '. $stafff->employeeStaff[0]->employee['firstname_'.$locale]. ' ' . $stafff->employeeStaff[0]->employee['middlename_'.$locale],
            $emp['lastname_'.$locale] . ' '. $emp['firstname_'.$locale]. ' ' . $emp['middlename_'.$locale],
            $position = $minStaff && $minStaff->position ? $minStaff->position['name_' . $locale] : $stafff->position['name_' . $locale],
        ];
    }
    public static function getFromsss($created_employee_id, $locale){
       
        $array = [];

        // $tabel = $request->input('tabel'); mainStaff

        $employee = Employee::find($created_employee_id);
        $array[] = $employee;
        if ($employee->staff[0]->department->manager_staff_id != $employee->staff[0]->id) {
            $id = $employee->staff[0]->department->manager_staff_id;
            $employee1 = Employee::whereHas('employeeStaff', function ($q) use ($id) {
                $q->where('staff_id', $id);
                $q->where('is_active', 1);
            })
                ->with(['staff' => function ($q) {
                    $q->with('position');
                    $q->with(['department' => function ($q) {
                        $q->with('departmentType');
                    }]);
                }])->first();
            if (isset($employee1)) {
                $array[] = $employee1;
            }
        }

        for ($x = 0; $employee->staff[0]->department->departmentType->sequence > 3; $x++) {
            $employee =  Self::empdep($employee->staff[0]->department->parent_id);
            if (!isset($employee->staff[0])) {
                break;
            } else {
                if ($employee->staff[0]->department->manager_staff_id != $employee->staff[0]->id) {
                    $id = $employee->staff[0]->department->manager_staff_id;
                    $employee1 = Employee::whereHas('employeeStaff', function ($q) use ($id) {
                        $q->where('staff_id', $id);
                        $q->where('is_active', 1);
                    })
                        ->whereHas('staff', function ($q) {
                            $q->whereHas('department', function ($q) {
                                $q->whereHas('departmentType', function ($q) {
                                    $q->where('sequence', '>', 3);
                                });
                            });
                        })
                        ->with(['staff' => function ($q) {
                            $q->with('position');
                            $q->with(['department' => function ($q) {
                                $q->with('departmentType');
                            }]);
                        }])->first();
                    if (isset($employee1)) {
                        $array[] = $employee1;
                    }
                }
            }
            $array[] = $employee;
        }
        $array = array_reverse($array);
        $emp = Employee::find($array[0]->id);       
        $staff = $emp->staff[0];
        return
        [
            $department = $staff->department['name_' . $locale],
            $employee = $staff->employeeStaff[0]->employee['lastname_'.$locale] . ' '. $staff->employeeStaff[0]->employee['firstname_'.$locale]. ' ' . $staff->employeeStaff[0]->employee['middlename_'.$locale],
            $position = $staff->position['name_' . $locale],
        ];

    }

    public static function empdep($id)
    {
        return
            $employee = Employee::whereHas('staff', function ($q) use ($id) {
                $q->whereHas('department', function ($q) use ($id) {
                    $q->where('id', $id);
                });
            })
            ->whereHas('staff', function ($q) {
                $q->whereHas('department', function ($q) {
                    $q->whereHas('departmentType', function ($q) {
                        $q->where('sequence', '>', 3);
                    });
                });
            })
            ->with(['staff' => function ($q) {
                $q->with('position');
                $q->with(['department' => function ($q) {
                    $q->with('departmentType');
                }]);
            }])->first();
    }


    // public static function getFromTo($isFromStaff, $isToStaff, $locale){
    //     $fromStaff = Staff::find($isFromStaff);
    //     $toStaff = Staff::find($isToStaff);
    //     return
    //     [
    //         $fromDepartment = $fromStaff->department['name_' . $locale],
    //         $fromEmployee = $fromStaff->employeeStaff[0]->employee['lastname_'.$locale] . ' '. $fromStaff->employeeStaff[0]->employee['firstname_'.$locale]. ' ' . $fromStaff->employeeStaff[0]->employee['middlename_'.$locale],
    //         $fromPosition = $fromStaff->position['name_' . $locale],
    //         $toDepartment = $toStaff->department['name_' . $locale],
    //         $toEmployee = $toStaff->employeeStaff[0]->employee['lastname_'.$locale] . ' '. $toStaff->employeeStaff[0]->employee['firstname_'.$locale]. ' ' . $toStaff->employeeStaff[0]->employee['middlename_'.$locale],
    //         $toPosition = $toStaff->position['name_' . $locale]
    //     ];

    // }
    // public function getFioShortAttribute()
    // {
    //     if($this->fio){
    //         return $this->fio;
    //     }
    //     else {
    //         $locale = isset(Document::find($this->document_id)->locale) ? Document::find($this->document_id)->locale : 'uz_cyril';
    //         $employee = EmployeeStaff::where('staff_id', $this->staff_id)->where('is_active', 1)->orderBy('is_main_staff', 'desc')->first() ? EmployeeStaff::where('staff_id', $this->staff_id)->where('is_active', 1)->orderBy('is_main_staff', 'desc')->first()->employee->getShortname($locale) : '';
    //         return $employee;
    //     }
    // }
}
