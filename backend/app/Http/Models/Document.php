<?php

namespace App\Http\Models;

use App\Base64;
use App\DocumentNumberCounter;
use App\LastPdfTable;
use App\Companies\Invest;
use App\Companies\Vw;
use App\Companies\Usf;
use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Http;

class Document extends Model
{
    use SoftDeletes;

    protected $casts = [
        'document_date' => 'date:Y-m-d',
    ];

    protected $appends = ['base64', 'pdf', 'eimzoinfo'];

    // protected $guarded = ['base64'];
    protected $hidden = ['base64', 'pdf', 'b64', 'eimzoinfo'];

    public function getBase64Attribute()
    {
        if ($this->id) {
            $pdf = DB::connection('workflow_pdf')->select('select * from ' . $this->pdf_table . ' where document_id=' . $this->id);
        }
        // dd($pdf[0]->eimzoBase64);
        if ($pdf && $pdf[0]) {
            return $pdf[0]->eimzoBase64;
        } else {
            return null;
        }
    }

    public function getEimzoInfoAttribute()
    {
        if ($this->base64) {
            $body = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/"><Body><verifyPkcs7 xmlns="http://v1.pkcs7.plugin.server.dsv.eimzo.yt.uz/"><pkcs7B64 xmlns="">' . $this->base64 . '</pkcs7B64></verifyPkcs7></Body></Envelope>';
            $headers = ['Content-Type' => 'text/xml'];
            $url = 'http://127.0.0.1:9090/dsvs/pkcs7/v1';

            $client = new Client();
            $res = $client->request('POST', $url, [
                'body' => $body,
                'header' => $headers,
            ]);
            $xml = $res->getBody()->getContents();

            $doc = new DOMDocument();
            $doc->loadXML($xml);
            $json = $doc->getElementsByTagName('return')[0]->nodeValue;

            return json_decode($json)->pkcs7Info;
        }
    }

    // public function getBase64Attribute()
    // {
    //     $pdf = $this->pdf_table ? DB::connection('workflow_pdf')->select('select eimzoBase64 from '.$this->pdf_table.' where document_id='.$this->id) : 0;
    //     // dd($pdf);
    //     if ($pdf && $pdf[0]) {
    //         return $pdf[0]->eimzoBase64;
    //     } else {
    //         return null;
    //     }
    // }

    public function getPdfAttribute()
    {
        if (!$this->pdf_table) {
            $this->pdf_table = self::savePdf($this->id);
        }
        if ($this->id) {
            $pdf = DB::connection('workflow_pdf')->select('select "pdfBase64" from ' . $this->pdf_table . ' where document_id=' . $this->id);
        }
        // dd($pdf);
        if ($pdf && $pdf[0]) {
            return $pdf[0]->pdfBase64;
        } else {
            return null;
        }
    }

    public function graphics()
    {
        return $this->hasMany('App\Http\Models\Graphic', 'document_id', 'id');
    }
    public function compleansAnswer()
    {
        return $this->hasMany('App\Http\Models\ComplaensAnswer', 'document_id', 'id');
    }

    public function documentType()
    {
        return $this->hasOne('App\Http\Models\DocumentType', 'id', 'document_type_id');
    }
    public function staff()
    {
        return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id');
    }
    public function department2()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'department2_id');
    }
    public function CancelledDocument()
    {
        return $this->hasMany('App\Http\Models\CancelledDocument', 'document_id', 'id');
    }
    public function files()
    {
        return $this->hasMany('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', 5);
    }
    public function department()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'department_id');
    }
    public function documentTemplate()
    {
        return $this->hasOne('App\Http\Models\DocumentTemplate', 'id', 'document_template_id');
    }
    public function documentDetails()
    {
        return $this->hasMany('App\Http\Models\DocumentDetail', 'document_id', 'id')->orderBy('document_details.id', 'asc');
    }
    public function documentSigners()
    {
        return $this->hasMany('App\Http\Models\DocumentSigner', 'document_id', 'id');
    }
    public function documentSignerTemplates()
    {
        return $this->hasMany('App\Http\Models\DocumentSignerTemplate', 'document_template_id', 'document_template_id');
    }

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'created_employee_id');
    }

    public function complaensCencelDocument()
    {
        return $this->hasOne('App\Http\Models\ComplaensCencelDocument', 'document_id', 'id');
    }

    public function documentRelation()
    {
        return $this->belongsToMany('App\Http\Models\Document', 'document_relations', 'document_id', 'parent_document_id')->whereNotIn('documents.status', [0, 6]);
    }

    public function documentStaff()
    {
        return $this->belongsToMany('App\Http\Models\Staff', 'document_staff', 'document_id', 'staff_id');
    }

    public function documentChildren()
    {
        return $this->belongsToMany('App\Http\Models\Document', 'document_relations', 'parent_document_id', 'document_id')->where('status', '>', 0);
    }

    public function previousVersion()
    {
        return $this->hasOne('App\Http\Models\Document', 'id', 'old_document_id');
    }
    public function actDate()
    {
        return $this->hasOne('App\Http\Models\ActDate', 'document_id', 'id');
    }

    public static function getSignerDepartmentInfo($from_employee_id, $to_employee_id, $locale)
    {
        $locale = $locale ? $locale : 'ru';
        // $employee = Employee::find($from_employee_id);
        $employee_locale = $locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
        //----------------------- from department
        $from_employee = Employee::employeeDepartments($from_employee_id, $employee_locale);
        $to_employee = Employee::employeeDepartments($to_employee_id, $employee_locale);
        $from_department = $from_employee['from_department'];
        $from_manager = $from_employee['from_manager'];
        $from_position = $from_employee['from_position'];

        $to_department = $to_employee['from_department'];
        $to_manager = $to_employee['from_manager'];
        $to_position = $to_employee['from_position'];
        // return $from_department;


        return [
            $from_department,
            $from_manager,
            $from_position,
            $to_department,
            $to_manager,
            $to_position,
        ];
    }
    public static function getDocumentDepartmentInfo($from_employee_id, $to_department_id, $locale)
    {
        $locale = $locale ? $locale : 'ru';
        $employee = Employee::find($from_employee_id);
        $employee_locale = $locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
        //----------------------- from department
        $employee_parent = Employee::parentDepartments($employee->tabel);
        $from_department = $employee_parent['main_department'] ? $employee_parent['main_department']['name_' . $locale] : '';
        $from_manager = '';
        if ($employee_parent['main_department'] && $employee_parent['main_department']['managerStaff'] && $employee_parent['main_department']['managerStaff']['employeeOldStaff'] && $employee_parent['main_department']['managerStaff']['employeeOldStaff']['employee']) {
            $from_manager_employee = $employee_parent['main_department']['managerStaff']['employeeOldStaff']['employee'];
            $from_manager = $from_manager_employee['lastname_' . $employee_locale] . ' ' . $from_manager_employee['firstname_' . $employee_locale] . ' ' . $from_manager_employee['middlename_' . $employee_locale];
        }
        $from_position = '';
        if ($employee_parent['main_department'] && $employee_parent['main_department']['managerStaff'] && $employee_parent['main_department']['managerStaff']['position']) {
            $from_position = $employee_parent['main_department']['managerStaff']['position']['name_' . $locale];
        }

        //----------------------- to department
        $to_department_model = Department::with('managerStaff.employeeMainStaff.employee')
            ->with('managerStaff.position')
            ->find($to_department_id);
        $to_department = $to_department_model ? $to_department_model['name_' . $locale] : '';
        $to_manager = '';
        if ($to_department_model && $to_department_model['managerStaff'] && $to_department_model['managerStaff']['employeeMainStaff'] && $to_department_model['managerStaff']['employeeMainStaff']['employee']) {
            $to_manager_employee = $to_department_model['managerStaff']['employeeMainStaff']['employee'];
            $to_manager = $to_manager_employee['lastname_' . $employee_locale] . ' ' . $to_manager_employee['firstname_' . $employee_locale] . ' ' . $to_manager_employee['middlename_' . $employee_locale];
        } elseif ($to_department_model && $to_department_model['managerStaff'] && count($to_department_model['managerStaff']['employeeStaff']) > 0 && $to_department_model['managerStaff']['employeeStaff'][0]['employee']) {
            $to_manager_employee = $to_department_model['managerStaff']['employeeStaff'][0]['employee'];
            $to_manager = $to_manager_employee['lastname_' . $employee_locale] . ' ' . $to_manager_employee['firstname_' . $employee_locale] . ' ' . $to_manager_employee['middlename_' . $employee_locale];
        }
        $to_position = '';
        if ($to_department_model && $to_department_model['managerStaff'] && $to_department_model['managerStaff']['position']) {
            $to_position = $to_department_model['managerStaff']['position']['name_' . $locale];
        }
        // if(Auth::user()->username == 'qg9592'){
        //     return [
        //         $from_department,
        //         $from_manager,
        //         $from_position,
        //         $to_department,
        //         $to_manager,
        //         $to_position,
        //     ];
        // }
        return [
            $from_department,
            $from_manager,
            $from_position,
            $to_department,
            $to_manager,
            $to_position,
        ];
    }

    public static function generateDocumentNumberOld($id)
    {
        try {
            $document = Document::find($id);
            if (substr($document->document_number, 0, 1) != 2) {
                $document_type = DocumentType::where('id', $document->document_type_id)->first();
                $employee = $document->employee;
                $departmentCode = '-';
                $branchCode = '';
                if (isset($employee->mainStaff[0])) {
                    $staff = $employee->mainStaff[0];
                    $branchCode = $staff->department && $staff->department->branch ? $staff->department->branch->code : '';
                    $departmentCode .= $staff->department ? substr($staff->department->department_code, 0, 4) : '';
                } else {
                    $staff = Auth::user()->employee->staff[0];
                    $branchCode = $staff->department && $staff->department->branch ? $staff->department->branch->code : '';
                    $departmentCode .= $staff->department ? substr($staff->department->department_code, 0, 4) : '';
                }
                if ($document_type->type == 'A' || $document_type->type == 'D') {
                    $departmentCode = '';
                }
                $code = date('y') . $branchCode . $document_type->code;

                if ($document->document_template_id != 218) {
                    if ($document_type->code == 'FB') {
                        if ($document->document_template_id == 264 && config('app.APP_COMPANY_ID') == 1) {
                            $code = date('y') . 'TFB';
                            $document_number = DocumentNumberCounter::newDocumentNumber($code, null);
                            $document->document_number = $code . $departmentCode . substr($document_number, 5, 5);
                        } else {
                            $document_number = DocumentNumberCounter::newDocumentNumber($code, null);
                            $document->document_number = $code . $departmentCode . substr($document_number, 5, 5);
                        }
                    } else {
                        $document->document_number = DocumentNumberCounter::newDocumentNumber($code . $departmentCode, null);
                    }
                }
                $document->document_date = date('Y-m-d H:i:s');
                $document->save();
                // dd(DocumentNumberCounter::newDocumentNumber($code.$departmentCode));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function generateDocumentNumberNew2022($id)
    {
        try {
            $document = Document::find($id);
            if ($document->document_number == "YYXX-0000-0000" || $document->document_number == "000000" || $document->document_number == "YYAXX-0000-0000") {
                $document_type = DocumentType::where('id', $document->document_type_id)->first();
                $employee = $document->employee;
                $departmentCode = in_array($document->documentTemplate->numeration_type, [1, 9]) ? '' : '-'; // -4501
                $code = ""; // 21FB
                if (in_array($document->document_template_id, [317, 318, 319, 322, 323, 324, 364, 365, 366, 429, 430, 320, 325, 367]) && $document->staff) {
                    $staff = $document->staff;
                    $departmentCode .= $staff->department ? substr($staff->department->department_code, 0, 4) : '';
                } elseif (in_array($document->document_template_id, [316, 321, 363]) && $document->department2) {
                    $departmentCode .= $document->department2 ? substr($document->department2->department_code, 0, 4) : '';
                } elseif (isset($employee->mainStaff[0])) {
                    $staff = $employee->mainStaff[0];
                    $departmentCode .= $staff->department ? substr($staff->department->department_code, 0, 4) : '';
                } else {
                    $staff = Auth::user()->employee->staff[0];
                    $departmentCode .= $staff->department ? substr($staff->department->department_code, 0, 4) : '';
                }
                if ($document->documentTemplate->numeration_type == 4) {
                    if ($document->documentTemplate->folder_code) {
                        $code = date('y');
                        $tmp = DocumentNumberCounter::newDocumentNumber($code, 5);
                        $document->document_number = $tmp;
                    } else {
                        $code = date('y') . $document->documentTemplate->template_code;
                        $tmp = DocumentNumberCounter::newDocumentNumber($code, 5);
                        $document->document_number = str_replace($code . '-', "", $tmp) . "-" . $document->documentTemplate->template_code;
                    }
                } elseif ($document->documentTemplate->numeration_type == 7) {
                    $code = date('y') . $document_type->code;
                    $document->document_number = $code . '-' . DocumentNumberCounter::newDocumentNumber($code . "7", 5);
                    // sardor
                } elseif ($document->documentTemplate->numeration_type == 8) {
                    $code = $document->documentTemplate->template_code;
                    $document->document_number = $code . '-' . DocumentNumberCounter::newDocumentNumber($code, 5);
                    // sardor
                } elseif ($document->documentTemplate->numeration_type == 1) {
                    $code = date('y') . $document_type->code;
                    $counter = DocumentNumberCounter::newDocumentNumber($code, 5);

                    $document->document_number = $departmentCode . '-' . $counter;

                    // if($document->document_details[0]->documentDetailAttributeValues)
                    try {
                        $jurnal = collect($document->documentDetails[0]->documentDetailAttributeValues)->first(function ($value, $key) {
                            //
                            return $value->d_d_attribute_id == 1048 || $value->d_d_attribute_id == 1144 || $value->d_d_attribute_id == 1114 || $value->d_d_attribute_id == 712;
                        });
                        if ($jurnal) {
                            $directory = Directory::find($jurnal['attribute_value']);
                            if ($directory) {
                                $document->document_number = $directory->code . '-' . $counter;
                            }
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    // sardor
                } elseif ($document->documentTemplate->numeration_type == 9) {
                    $code = date('y') . 'buyruq';
                    $counter = DocumentNumberCounter::newDocumentNumber($code, 5);
                    if($document->documentTemplate->folder_code){
                        $document->document_number = $document->documentTemplate->folder_code. '-' . $counter;
                        // $document->document_number = $departmentCode . '-' . $document->documentTemplate->folder_code. '-' . $counter;
                    }
                    else{

                        $document->document_number = $departmentCode . '-' . $counter;
                    }

                    // if($document->document_details[0]->documentDetailAttributeValues)
                    try {
                        $jurnal = collect($document->documentDetails[0]->documentDetailAttributeValues)->first(function ($value, $key) {
                            //
                            return $value->d_d_attribute_id == 1048 || $value->d_d_attribute_id == 1144 || $value->d_d_attribute_id == 1114 || $value->d_d_attribute_id == 712;
                        });
                        if ($jurnal) {
                            $directory = Directory::find($jurnal['attribute_value']);
                            if ($directory) {
                                $document->document_number = $directory->code . '-' . $counter;
                            }
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    // sardor
                } else {
                    $code = date('y') . $document_type->code;
                    $counter = DocumentNumberCounter::newDocumentNumber($code, 5);
                    if($document->documentTemplate->folder_code){
                        $document->document_number = $code . $departmentCode . '-' . $document->documentTemplate->folder_code. '-' . $counter;
                    }
                    else{

                        // $document->document_number = $departmentCode . '-' . $counter;
                        $document->document_number = $code . $departmentCode . '-' . $counter;
                    }

                    // if($document->document_details[0]->documentDetailAttributeValues)
                    try {
                        $jurnal = collect($document->documentDetails[0]->documentDetailAttributeValues)->first(function ($value, $key) {
                            //
                            return $value->d_d_attribute_id == 1048 || $value->d_d_attribute_id == 1144 || $value->d_d_attribute_id == 1114 || $value->d_d_attribute_id == 712;
                        });
                        if ($jurnal) {
                            $directory = Directory::find($jurnal['attribute_value']);
                            if ($directory) {
                                $document->document_number = $code . '-' . $directory->code . '-' . $counter;
                            }
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
                $document->document_date = date('Y-m-d H:i:s');

                // if($id == 2757565){
                //     dd($document->document_number);
                // }
                $document->save();
                if (in_array($document->document_template_id, [71, 305, 357])) {
                    try {
                        Document::stampDocument($id);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
                // else {
                //     Document::stampUploaddedPdf($id);
                // }
                $document->document_date = date('Y-m-d H:i:s');
                $document->save();
            }
            // 2-versiyadagi hujjatlar datasi eski datani ovolmaslik uchun bu qator pastga olindi.

            //yuklangan fayllarga stamp bosiladi
            // try {
            //     Document::stampUploaddedPdf($document->id); 
            // } catch (\Throwable $th) {

            // }
            //yuklangan fayllarga stamp bosiladi

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function generateDocumentNumberUzavtosanoat($id)
    {
        try {
            $document = Document::find($id);
            $toDepCode = $document->department->department_code;

            if ($document->document_number == '000000') {
                $document_type = DocumentType::where('id', $document->document_type_id)->first();
                $employee = $document->employee;
                $departmentCode = '';
                if (isset($employee->mainStaff[0])) {
                    $staff = $employee->mainStaff[0];
                    $departmentCode = $staff->department ? $staff->department->department_code : '';
                } else {
                    $staff = Auth::user()->employee->staff[0];
                    $departmentCode = $staff->department ? $staff->department->department_code : '';
                }

                // if ($document->document_template_id != 218) {
                //     if ($document_type->code == 'FB') {
                //         $document_number = DocumentNumberCounter::newDocumentNumber($code);
                //         $document->document_number = $code . $departmentCode . substr($document_number, 5, 5);
                //     } else {
                //     }
                // }
                // dd(DocumentNumberCounter::newDocumentNumber($code));

                $document_number = '000000';
                $digital = $document->documentTemplate->digital ? $document->documentTemplate->digital : 4;
                $code = DocumentNumberCounter::newDocumentNumber($document->documentTemplate->folder_code, $digital);

                if ($document->documentTemplate->numeration_type == 1) {
                    $document_number = $departmentCode . '-' . $code;
                } elseif ($document->documentTemplate->numeration_type == 4) {
                    $document_number = $code;
                } elseif ($document->documentTemplate->numeration_type == 3) {
                    $document_number = $departmentCode . '-' . $toDepCode . '-' . $code;
                } else {
                    $document_number = $departmentCode . '/' . $toDepCode . '-' . $code;
                }
                $document->document_number = $document->documentTemplate->template_code ? $document_number . '-' . $document->documentTemplate->template_code : $document_number;
                $document->document_date = date('Y-m-d H:i:s');
                if (in_array($document->document_template_id, [78, 79]) && config('app.APP_COMPANY_ID') == 3) {
                    $document->document_number = 'D-' . $document->document_number;
                }
                $document->save();
                // dd(DocumentNumberCounter::newDocumentNumber($code.$departmentCode));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function generateDocumentNumberUsf($id)
    {
        try {
            $document = Document::find($id);
            $toDepCode = $document->department->department_code;
            // dd($document->document_number);

            if ($document->document_number == '000000' || $document->document_number == 'YYAXX-0000-0000') {
                $document_type = DocumentType::where('id', $document->document_type_id)->first();
                $employee = $document->employee;
                $departmentCode = '';
                if (isset($employee->mainStaff[0])) {
                    $staff = $employee->mainStaff[0];
                    $departmentCode = $staff->department ? $staff->department->department_code : '';
                } else {
                    $staff = Auth::user()->employee->staff[0];
                    $departmentCode = $staff->department ? $staff->department->department_code : '';
                }

                // if ($document->document_template_id != 218) {
                //     if ($document_type->code == 'FB') {
                //         $document_number = DocumentNumberCounter::newDocumentNumber($code);
                //         $document->document_number = $code . $departmentCode . substr($document_number, 5, 5);
                //     } else {
                //     }
                // }
                // dd(DocumentNumberCounter::newDocumentNumber($code));

                $document_number = '000000';
                $digital = $document->documentTemplate->digital ? $document->documentTemplate->digital : 4;
                $code = DocumentNumberCounter::newDocumentNumber($document->documentTemplate->folder_code, $digital);

                // dd($document->documentTemplate->numeration_type);
                if ($document->documentTemplate->numeration_type == 1) {
                    // dd(substr($departmentCode,0,2) . '...' . substr($code,3));
                    $document_number = substr($departmentCode, 0, 3) . '-' . $code;
                } elseif ($document->documentTemplate->numeration_type == 4) {
                    $document_number = $code;
                } elseif ($document->documentTemplate->numeration_type == 3) {
                    $document_number = substr($departmentCode, 0, 3) . '-' . substr($toDepCode, 0, 2) . '-' . $code;
                } else {
                    $document_number = substr($departmentCode, 0, 3) . '/' . substr($toDepCode, 0, 2) . '-' . $code;
                }
                $document->document_number = $document->documentTemplate->template_code ? $document_number . '-' . $document->documentTemplate->template_code : $document_number;
                $document->document_date = date('Y-m-d H:i:s');

                $document->save();
                // dd(DocumentNumberCounter::newDocumentNumber($code.$departmentCode));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function fromTo($id)
    {
        $document = self::find($id);
        $document_number = $document->document_number_reg ? $document->document_number_reg : $document->document_number;
        $document_date = $document->document_date_reg ? $document->document_date_reg : $document->document_date;

        $version = $document->version ? 0 : 0;
        $doc_version = $version ? 'v' . $version : '';

        if ($document->documentTemplate->is_from_to_department_show) {
            // if($id == 2200675){
            //     return $document->to_position;
            //     // dd(1);die;
            // }

            $from = '<div style="margin-top:7px;">' . ['ru' => '<b>Отправитель:</b><br>', 'uz_cyril' => '<b>Юборувчи:</b><br>', 'uz_latin' => '<b>Yuboruvchi:</b><br>',][$document->locale] . '</div>';
            $from .= '<div style="margin-top:7px;">' . $document->from_department . '</div>';
            $from .= '<div style="margin-top:7px;">' . $document->from_manager . '</div>';
            $from .= '<div style="margin-top:7px;">' . $document->from_position . '</div>';
            $to = '<div style="margin-top:7px;">' . ['ru' => '<b>Получатель:</b><br>', 'uz_cyril' => '<b>Қабул қилувчи:</b><br>', 'uz_latin' => '<b>Qabul qiluvchi:</b><br>',][$document->locale] . '</div>';
            $to .= '<div style="margin-top:7px;">' . $document->to_department . '</div>';
            $to .= '<div style="margin-top:7px;">' . $document->to_manager . '</div>';
            $to .= '<div style="margin-top:7px;">' . $document->to_position . '</div>';

            // $content = '<div style="width:100%;letter-spacing:1px;margin-top:-10px;display: -webkit-box; display: flex;-webkit-box-pack: center;justify-content: center;">';
            // $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;">';
            // $content .= $document_number . ' ' . $doc_version;
            // $content .= $from;
            // $content .= '</div>';
            // $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;text-align:right;">';
            // $content .=  substr($document_date, 0, 10);
            // $content .= $to;
            // $content .= '</div>';
            // $content .= '</div>';
            $content = '<table style="width:100%;margin-top:-15px;"><tr><td style="width:50%;">';
            $content .= $document_number . ' ' . $doc_version . '<br>';
            $content .= '</td><td style="width:50%; text-align:right;">';
            $content .= substr($document_date, 0, 10) . '<br>';
            $content .= '</td></tr><tr><td style="vertical-align:top;">';
            $content .= $from;
            $content .= '</td><td style="width:50%; text-align:right;vertical-align:top;">';
            $content .= $to;
            $content .= '</td><tr></table>';
            return $content;
        }

        $rassilki = DocumentSigner::whereIn('sequence', [0, 97, 98])
            ->where('action_type_id', 4)
            ->where('document_id', $id)
            ->orWhere('action_type_id', 4)
            ->where('document_id', $id)
            ->where('parent_employee_id', [10772, 109, 978])
            // ->where('status', 1)
            ->count();
        // if($id == 2541938){
        //     dd($rassilki);
        // }
        // dd($rassilki);
        $content = '';
        if ($document->document_template_id != 70 || $document->document_date < '2023-01-01 00:00:00' || $rassilki > 1 || in_array($document->id, [2440687, 2301947, 2541938, 2560174])) {
            $document_number = $document_number . ($doc_version ? ' ' . $doc_version : '');
            $document_date = substr($document_date, 0, 10);
            if ($document->document_template_id == 70) {
                $document_number = self::getSonliName($document->locale, $document_number);
                $document_date = substr($document->document_date, 0, 4) . self::getYearhName($document->locale) . ' «' . (substr($document_date, 8, 2) * 1) . '» ' . self::getMonthName(substr($document_date, 5, 2), $document->locale);
            }

            $content = '<div style="letter-spacing:1px;display: -webkit-box; display: flex;-webkit-box-pack: center;justify-content: center;font-size:20px !important;">';
            $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;">';
            $content .= $document_number;
            $content .= '</div>';
            $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;text-align:right;">';
            $content .= $document_date;
            $content .= '</div>';
            $content .= '</div>';
        }

        return $content;
    }


    public static function fromToUzavtosanoat($id)
    {
        $document = self::find($id);
        $document_number = $document->document_number_reg ? $document->document_number_reg : $document->document_number;
        $document_date = $document->document_date_reg ? $document->document_date_reg : $document->document_date;
        $document_number = substr($document_number, 0, 2) == 'QN' ? '____-__-______' : $document_number;

        $version = $document->version ? $document->version + 1 : 0;
        if ($document->documentTemplate->is_from_to_department_show) {
            $from = ['ru' => '<b>Отправитель:</b>', 'uz_cyril' => '<b>Юборувчи:</b>', 'uz_latin' => '<b>Yuboruvchi:</b>',][$document->locale] . '<br>';
            $from .= $document->from_department . '<br>';
            $from .= $document->from_manager . '<br>';
            $from .= $document->from_position;
            $to = ['ru' => '<b>Получатель:</b>', 'uz_cyril' => '<b>Қабул қилувчи:</b>', 'uz_latin' => '<b>Qabul qiluvchi:</b>',][$document->locale] . '<br>';
            $to .= $document->to_department . '<br>';
            $to .= $document->to_manager . '<br>';
            $to .= $document->to_position;
        }
        $content = '<table style="width:100%;margin-top:0px;font-size:16pt;font-weight:bold;">';
        $content .= '<tr>';
        $content .= '<td style="vertical-align:top;width:40%;">';
        $content .= $document_number . '<br>';
        if ($document->documentTemplate->is_from_to_department_show) {
            $content .= $from;
        }
        $content .= '</td>';
        $content .= '<td style="text-align:right; vertical-align:top;width:40%;">';
        $content .= substr($document_date, 0, 10) . '<br>';
        if ($document->documentTemplate->is_from_to_department_show) {
            $content .= $to;
        }
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</table>';
        return $content;
    }

    public static function fromToUsf($id)
    {
        $document = self::find($id);
        $document_number = substr($document->document_number, 0, 2) == 'QN' ? '____-__-______' : $document->document_number;

        $version = $document->version ? $document->version + 1 : 0;
        $doc_version = $version ? 'v' . $version : '';
        $from = '<div style="margin-top:7px;">' . ['ru' => '<b>Отправитель:</b><br>', 'uz_cyril' => '<b>Юборувчи:</b><br>', 'uz_latin' => '<b>Yuboruvchi:</b><br>',][$document->locale] . '</div>';
        $from .= '<div style="margin-top:7px;">' . $document->from_department . '</div>';
        $from .= '<div style="margin-top:7px;">' . $document->from_manager . '</div>';
        $from .= '<div style="margin-top:7px;">' . $document->from_position . '</div>';
        $to = '<div style="margin-top:7px;">' . ['ru' => '<b>Получатель:</b><br>', 'uz_cyril' => '<b>Қабул қилувчи:</b><br>', 'uz_latin' => '<b>Qabul qiluvchi:</b><br>',][$document->locale] . '</div>';
        $to .= '<div style="margin-top:7px;">' . $document->to_department . '</div>';
        $to .= '<div style="margin-top:7px;">' . $document->to_manager . '</div>';
        $to .= '<div style="margin-top:7px;">' . $document->to_position . '</div>';

        $content = '<div style="width:100%;letter-spacing:1px;margin-top:-10px;display: -webkit-box; display: flex;-webkit-box-pack: center;justify-content: center;">';
        $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;">';
        // $content .= $document_number . ' ' . $doc_version;
        $content .= $from;
        $content .= '</div>';
        $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;text-align:right;">';
        // $content .=  substr($document->document_date, 0, 10);
        $content .= $to;
        $content .= '</div>';
        $content .= '</div>';
        return $content;
    }

    public static function actionTypesSort()
    {
        return [
            [
                'id' => 2,
                'name_uz_latin' => "Tasdiq",
                'name_uz_cyril' => "Тасдиқ",
                'name_ru' => "Утверждение",
            ],
            [
                'id' => 9,
                'name_uz_latin' => "Komissiya raisi",
                'name_uz_cyril' => "Комиссия раиси",
                'name_ru' => "Председатель комиссии",
            ],
            [
                'id' => 17,
                'name_uz_latin' => "Komissiya raisi o'rinbosari",
                'name_uz_cyril' => "Комиссия раиси ўринбосари",
                'name_ru' => "Заместитель председателя комиссии",
            ],
            // [
            //     'id' => 12,
            //     'name_uz_latin' => "Kuzatuvchi",
            //     'name_uz_cyril' => "Кузатувчи",
            //     'name_ru' => "Наблюдатель",
            // ],
            [
                'id' => 8,
                'name_uz_latin' => "Komissiya a'zolari",
                'name_uz_cyril' => "Комиссия аъзолари",
                'name_ru' => "Члены комиссии",
            ],
            [
                'id' => 10,
                'name_uz_latin' => "Komissiya kotibi",
                'name_uz_cyril' => "Комиссия котиби",
                'name_ru' => "Секретарь комиссии",
            ],
            [
                'id' => 1,
                'name_uz_latin' => "Rozilik",
                'name_uz_cyril' => "Розилик",
                'name_ru' => "Согласование",
            ],
            [
                'id' => 3,
                'name_uz_latin' => "Bo'lim ichida rozilik",
                'name_uz_cyril' => "Бўлим ичида розилик",
                'name_ru' => "Согласование внутри подразделение",
            ],
            [
                'id' => 6,
                'name_uz_latin' => "Mas'ul",
                'name_uz_cyril' => "масъул",
                'name_ru' => "Ответственный",
            ]
        ];
    }

    public static function actionTypesSortNew()
    {
        return [
            [
                'id' => 2,
                'name_uz_latin' => "Tasdiq",
                'name_uz_cyril' => "Тасдиқ",
                'name_ru' => "Утверждение",
            ],
            [
                'id' => 9,
                'name_uz_latin' => "Komissiya raisi",
                'name_uz_cyril' => "Комиссия раиси",
                'name_ru' => "Председатель комиссии",
            ],
            // [
            //     'id' => 12,
            //     'name_uz_latin' => "Kuzatuvchi",
            //     'name_uz_cyril' => "Кузатувчи",
            //     'name_ru' => "Наблюдатель",
            // ],
            [
                'id' => 8,
                'name_uz_latin' => "Komissiya a'zolari",
                'name_uz_cyril' => "Комиссия аъзолари",
                'name_ru' => "Члены комиссии",
            ],
            [
                'id' => 10,
                'name_uz_latin' => "Komissiya kotibi",
                'name_uz_cyril' => "Комиссия котиби",
                'name_ru' => "Секретарь комиссии",
            ],
            [
                'id' => 1,
                'name_uz_latin' => "Rozilik",
                'name_uz_cyril' => "Розилик",
                'name_ru' => "Согласование",
            ],
            [
                'id' => 13,
                'name_uz_latin' => "Topshiriq uchun masul",
                'name_uz_cyril' => "Топширик учун маъсул",
                'name_ru' => "Ответственный за резолюцию",
            ],
            [
                'id' => 3,
                'name_uz_latin' => "Bo'lim ichida rozilik",
                'name_uz_cyril' => "Бўлим ичида розилик",
                'name_ru' => "Согласование внутри подразделение",
            ],
            [
                'id' => 6,
                'name_uz_latin' => "Mas'ul",
                'name_uz_cyril' => "масъул",
                'name_ru' => "Ответственный",
            ]
        ];
    }

    public static function getSignerTable($id, $position = null)
    {
        $document = self::find($id);
        $count = $document->locale == 'uz_latin' ? 1 : 2;
        if ($document->document_template_id == 558) {
            $content = '<table style="border-collapse: collapse;width: 100%;margin-top:0px;" >';
        } else {
            $content = '<table style="border-collapse: collapse;width: 100%;margin-top:10px;" >';
        }
        $actionTypesSort = self::actionTypesSort();
        if ($document->document_template_id == 564) {
            $actionTypesSort = collect(self::actionTypesSort())->where('id', '!=', 6);
        }
        foreach (in_array($document->document_template_id, [71, 305, 214, 357, 373]) ? self::actionTypesSortNew() : $actionTypesSort as $action) {
            $signers = DocumentSigner::select('document_signers.*')
                ->where('document_id', $id)
                ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                ->where('document_signers.action_type_id', $action['id'])
                ->orderBy('document_signers.sequence')
                ->orderBy('departments.department_code', 'asc')
                ->get();
            if ($document->document_type_id == 10) {
                $signers = DocumentSigner::where('document_id', $id)
                    ->select('document_signers.*')
                    ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                    ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                    ->where('document_signers.action_type_id', $action['id'])
                    ->where('document_signers.action_type_id', '!=', 6)
                    ->orderBy('document_signers.sequence')
                    ->orderBy('departments.department_code')
                    ->get();
            }
            if ($document->documentType->code == 'FB' || $document->document_template_id == 434 || $document->document_template_id == 438) {
                $signers = DocumentSigner::where('document_id', $id)
                    ->select('document_signers.*')
                    ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                    ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                    ->where('document_signers.action_type_id', $action['id'])
                    // ->whereIn('document_signers.sequence', [1, 2])
                    ->orderBy('departments.department_code')
                    ->get();
            }
            if ($document->document_template_id == 305 || $document->document_template_id == 357) {
                $signers = DocumentSigner::where('document_id', $id)
                    ->select('document_signers.*')
                    ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                    ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                    ->where('document_signers.action_type_id', $action['id'])
                    ->where('document_signers.sequence', 1)
                    ->orderBy('departments.department_code')
                    ->get();
            }
            if ($document->document_template_id == 558) {
                $signers = DocumentSigner::where('document_id', $id)
                    ->select('document_signers.*')
                    ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                    ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                    ->where('document_signers.action_type_id', $action['id'])
                    ->where('document_signers.staff_id', 2351)
                    ->orderBy('departments.department_code')
                    ->get();
            }
            if ($signers && count($signers) > 0 && $document->documentType->code != 'FB' && $document->document_template_id != 434 && $document->document_template_id != 438 && $document->document_template_id != 305 && $document->document_template_id != 357 && $document->document_template_id != 558) {
                $content .= '<tr><td style="border-bottom:2px solid #000;" colspan="3"><b>' . $action['name_' . $document->locale] . '</b></td></tr>';
            }
            $vvb =
                [
                    'ru' => 'Вр.и.о',
                    'uz_latin' => 'v.v.b',
                    'uz_cyril' => 'в.в.б'

                ];
            foreach ($signers as $key => $value) {
                if (($document->documentType->code == 'FB' || $document->document_template_id == 434 || $document->document_template_id == 438) && $value->action_type_id != 2) {
                } else {
                    $sign = null;
                    if ($value->signer_employee_id) {
                        $sign = DocumentSigner::where('document_id', $id)
                            ->where('sequence', $value->sequence)
                            ->where('signer_employee_id', $value->signer_employee_id)
                            ->leftJoin('employee_staff', function ($join) {
                                $join->on('document_signers.signer_employee_id', '=', 'employee_staff.employee_id')
                                    ->on('document_signers.staff_id', '=', 'employee_staff.staff_id');
                            })
                            ->first();
                        // if($document->id ==2774651){
                        //     dd($sign);
                        // }
                    }
                    $content .= '<tr>';
                    $content .= '<td style="width:400px;font-size:18pt;padding:20px 0 20px;">';

                    if ($document->id == 2774651) {

                        if ($position) {
                            $p = (isset($sign) && $sign->is_main_staff == 0) ?
                                ($document->locale == 'ru' ? $vvb[$document->locale] . ' ' . $position : $position . ' ' . $vvb[$document->locale]) :
                                $position;
                            $content .= '<b>' . $p . '</b>';
                            $count = 1;
                        } else {

                            $p = (($sign) && $sign->is_main_staff == 0) ?
                                ($document->locale == 'ru' ? $vvb[$document->locale] . ' ' . $value->staff->position['name_' . $document->locale] :
                                    $value->staff->position['name_' . $document->locale] . ' ' . $vvb[$document->locale]) :
                                $value->staff->position['name_' . $document->locale];
                            $content .= '<b>' . $p . '</b>';
                        }
                    } else {

                        if ($position) {
                            $content .= '<b>' . $position . '</b>';
                            $count = 1;
                        } else {
                            $content .= '<b>' . $value->staff->position['name_' . $document->locale] . '</b>';
                        }
                    }

                    if ($value->staff && $value->staff->department) {
                        if (config("app.APP_COMPANY_ID") == 3 || $value->action_type_id == 2) {
                        } else {
                            // if(Auth::id() == 1){
                            //     $content .= '<br><i>' . $value->staff->department['department_code'] . '</i>';
                            // }
                            // else
                            {
                                $content .= '<br><i>' . $value->staff->department['name_' . $document->locale] . '</i>';
                            }
                        }
                    }
                    $content .= '</td>';
                    $content .= '<td style="padding:5px; text-align:center; vertical-align: middle;">';

                    if (($document->document_template_id == 157) && $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues[3]) {
                        $locale = 'uz_latin';
                    }
                    $locale = $document->locale == 'ru' ? 'uz_cyril' : $document->locale;
                    if ($position) {
                        $locale = 'uz_latin';
                    }
                    $fio = '';
                    $fioLatin = '';
                    if ($value->signerEmployee) {
                        if (substr($value->signerEmployee['firstname_' . $locale], 0, 2) == 'Sh') {
                            $fio = '<b>' . substr($value->signerEmployee['firstname_' . $locale], 0, 2) . '. ' . '</b>';
                        } else {
                            $fio = '<b>' . substr($value->signerEmployee['firstname_' . $locale], 0, $count) . '. ' . '</b>';
                        }
                        if (substr($value->signerEmployee['middlename_' . $locale], 0, 2) == 'Sh') {
                            $fio .= !in_array($value->signerEmployee['middlename_' . $locale], ['', ' ']) ? '<b>Sh. ' . '</b>' : '';
                        } else {
                            $fio .= !in_array($value->signerEmployee['middlename_' . $locale], ['', ' ']) ? '<b>' . substr($value->signerEmployee['middlename_' . $locale], 0, $count) . '. ' . '</b>' : '';
                        }
                        $fio .= '<b>' . $value->signerEmployee['lastname_' . $locale] . '</b>';
                        $fioLatin = substr($value->signerEmployee['firstname_uz_latin'], 0, 1) . '. ';
                        $fioLatin .= !in_array($value->signerEmployee['middlename_uz_latin'], ['', ' ']) ? substr($value->signerEmployee['middlename_uz_latin'], 0, 1) . '. ' : '';
                        $fioLatin .= $value->signerEmployee['lastname_uz_latin'];
                    } elseif ($value->employeeStaffs && $value->employeeStaffs->employee) {
                        if (substr($value->employeeStaffs->employee['firstname_' . $locale], 0, 2) == 'Sh') {
                            $fio = '<b>' . substr($value->employeeStaffs->employee['firstname_' . $locale], 0, 2) . '. ' . '</b>';
                        } else {
                            $fio = '<b>' . substr($value->employeeStaffs->employee['firstname_' . $locale], 0, $count) . '. ' . '</b>';
                        }
                        if (substr($value->employeeStaffs->employee['middlename_' . $locale], 0, 2) == 'Sh') {
                            $fio .= !in_array($value->employeeStaffs->employee['middlename_' . $locale], ['', ' ']) ? '<b>Sh. ' . '</b>' : '';
                        } else {
                            $fio .= !in_array($value->employeeStaffs->employee['middlename_' . $locale], ['', ' ']) ? '<b>' . substr($value->employeeStaffs->employee['middlename_' . $locale], 0, $count) . '. ' . '</b>' : '';
                        }
                        // $fio .= '<b>' . substr($value->employeeStaffs->employee['firstname_' . $locale], 0, $count) . '. ' . '</b>';
                        $fio .= '<b>' . $value->employeeStaffs->employee['lastname_' . $locale] . '</b>';
                        $fioLatin = substr($value->employeeStaffs->employee['firstname_uz_latin'], 0, 1) . '. ';
                        $fioLatin .= !in_array($value->signerEmployee['middlename_uz_latin'], ['', ' ']) ? substr($value->employeeStaffs->employee['middlename_uz_latin'], 0, 1) . '. ' : '';
                        $fioLatin .= $value->employeeStaffs->employee['lastname_uz_latin'];
                    }
                    if ($value->status == 1) {
                        // if(Auth::id()==1){
                        //     dd($signers->toArray());
                        // }
                        if ($value->description) {
                            // dd($value->description);
                            $content .= '<div style="">';
                            $content .= '<span style="color:red;padding-right:5px;position:relative;top:-25px;">';
                            $content .= '*';
                            $content .= '</span>';
                            $content .= '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)->generate($fioLatin . ' ' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)))) . '"/>';
                            $content .= '</div>';
                        } elseif ($document->document_template_id == 558) {
                            $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                            $content .= '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';

                            // $content .= '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)->generate($fioLatin . ' ' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)))) . '"/>';
                        } else {
                            $content .= '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)->generate($fioLatin . ' ' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)))) . '"/>';
                        }
                        $content .= '<span style="display:block;margin-top:5px; font-size:12px;">' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)) . '</span>';
                    } elseif ($value->status == 2) {
                        if ($value->description) {
                            $content .= '<div style="">';
                            $content .= '<span style="color:red;padding-right:5px;position:relative;top:-30px;">';
                            $content .= '*';
                            $content .= '</span>';
                            $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
                            $content .= '</div>';
                        } else {
                            $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
                        }
                        $content .= '<span style="display:block;margin-top:5px; font-size:12px;">' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : '') . '</span>';
                    } else {
                        $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
                    }
                    $content .= '</td>';
                    $content .= '<td style="width:220px;text-align:right;font-size:18pt;">';


                    $content .= $fio;
                    if ($value->taken_datetime) {
                        $content .= '<br><span style="font-size:14pt;">' . date('Y-m-d H:i', strtotime($value->taken_datetime)) . '</span>';
                    }
                    $content .= '</td>';
                    $content .= '</tr>';
                }
            }
        }
        return $content .= '</tbody></table>';
    }

    public static function getSignerTableUzavtosanoat($id, $position = null)
    {
        $document = self::find($id);
        $count = $document->locale == 'uz_latin' ? 1 : 2;
        $content = '<table style="border-collapse: collapse;width: 100%;margin-top:20px;">';
        foreach (self::actionTypesSort() as $action) {
            $signers = DocumentSigner::where('document_id', $id)
                ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                ->where('document_signers.action_type_id', $action['id'])
                ->orderBy('document_signers.sequence')
                ->orderBy('departments.department_code')
                ->get();
            if ($document->document_type_id == 10) {
                $signers = DocumentSigner::where('document_id', $id)
                    ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                    ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                    ->where('document_signers.action_type_id', $action['id'])
                    ->where('document_signers.action_type_id', '!=', 6)
                    ->orderBy('document_signers.sequence')
                    ->orderBy('departments.department_code')
                    ->get();
            }
            // PDF da faqat sequence si 1 ga teng bo'lgan tasdiqlovchilarni chiqaradi
            if ($document->documentType->code == 'FB' || (in_array($document->document_template_id, [11, 53, 64, 57]) && config("app.APP_COMPANY_ID") == 3)) {
                $signers = DocumentSigner::where('document_id', $id)
                    ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                    ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                    ->where('document_signers.action_type_id', $action['id'])
                    ->where('document_signers.sequence', 1)
                    ->orderBy('departments.department_code')
                    ->get();
            }
            if ($signers && count($signers) > 0 && $document->document_template_id != 11 && $document->documentType->code != 'ID' && !$position) {
                $content .= '<tr><td style="border-bottom:2px solid #000;" colspan="3"><b>' . $action['name_' . $document->locale] . '</b></td></tr>';
            }
            foreach ($signers as $key => $value) {
                $content .= '<tr>';
                $content .= '<td style="width:400px;font-size:18pt;padding:20px 0 20px;">';
                if ($position) {
                    $content .= '<b>' . $position . '</b>';
                    $count = 1;
                } else {
                    $content .= '<b>' . $value->staff->position['name_' . $document->locale] . '</b>';
                }
                // if ($value->staff && $value->staff->department) {
                //     $content .= '<br><i><b>' . $value->staff->department['name_' . $document->locale] . '</i></b>';
                // }
                $content .= '</td>';
                $content .= '<td style="padding:5px; text-align:center;">';
                if ($document->document_template_id == 157 && $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues[3]) {
                    $locale = 'uz_latin';
                }
                $locale = $document->locale == 'ru' ? 'uz_cyril' : $document->locale;
                if ($position) {
                    $locale = 'uz_latin';
                }
                if ($value->signerEmployee) {
                    $fio = '<b>' . substr($value->signerEmployee['firstname_' . $locale], 0, $count) . '. ' . '</b>';
                    $fio .= '<b>' . $value->signerEmployee['middlename_' . $locale] . '<b>' != '' ? '<b>' . substr($value->signerEmployee['middlename_' . $locale], 0, $count) . '. ' . '</b>' : '';
                    $fio .= '<b>' . $value->signerEmployee['lastname_' . $locale] . '</b>';
                    $fioLatin = substr($value->signerEmployee['firstname_uz_latin'], 0, 1) . '. ';
                    $fioLatin .= $value->signerEmployee['middlename_uz_latin'] != '' ? substr($value->signerEmployee['middlename_uz_latin'], 0, 1) . '. ' : '';
                    $fioLatin .= $value->signerEmployee['lastname_uz_latin'];
                } elseif ($value->employeeStaffs && $value->employeeStaffs->employee) {
                    $fio = '<b>' . substr($value->employeeStaffs->employee['firstname_' . $locale], 0, $count) . '. ' . '</b>';
                    $fio .= '<b>' . $value->employeeStaffs->employee['middlename_' . $locale] . '<b>' != '' ? '<b>' . substr($value->employeeStaffs->employee['middlename_' . $locale], 0, $count) . '. ' . '</b>' : '';
                    $fio .= '<b>' . $value->employeeStaffs->employee['lastname_' . $locale] . '</b>';
                    $fioLatin = substr($value->employeeStaffs->employee['firstname_uz_latin'], 0, 1) . '. ';
                    $fioLatin .= $value->employeeStaffs->employee['middlename_uz_latin'] != '' ? substr($value->employeeStaffs->employee['middlename_uz_latin'], 0, 1) . '. ' : '';
                    $fioLatin .= $value->employeeStaffs->employee['lastname_uz_latin'];
                } else {
                    $fio = '';
                    $fioLatin = '';
                }
                if ($value->status == 1) {
                    $signType = $value->sign_type ? 'EIMZO' : 'LDAP';
                    // if(!$value->signed_date)
                    // {
                    //     $content .= '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0,90,169)->format('png')->encoding('UTF-8')->size(80)->generate($fioLatin . ' ' .date('Y-m-d H:i', $value->signed_date))) . '"/>';
                    //     $content .= '<span style="display:block;margin-top:5px; font-size:12px;">'.($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)).'</span>';
                    // }
                    $content .= '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)->generate($fioLatin . ' ' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)))) . '"/>';
                    $content .= '<span style="display:block;margin-top:5px; font-size:12px;">' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)) . '</span>';
                } elseif ($value->status == 2) {
                    $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
                    $content .= '<span style="display:block;margin-top:5px; font-size:12px;">' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : '') . '</span>';
                } else {
                    $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
                }
                $content .= '</td>';
                $content .= '<td style="width:200px;text-align:right;font-size:18pt;">';
                $content .= $fio;
                if ($value->taken_datetime) {
                    $content .= '<br><span style="font-size:14pt;">' . date('Y-m-d H:i', strtotime($value->taken_datetime)) . '</span>';
                }
                $content .= '</td>';
                $content .= '</tr>';
            }
        }
        return $content .= '</tbody></table>';
    }

    public static function getSignerTableUsf($id, $position = null)
    {
        $document = self::find($id);
        $count = $document->locale == 'uz_latin' ? 1 : 2;
        $content = '<table style="border-collapse: collapse;width: 100%;margin-top:20px;">';
        foreach (self::actionTypesSort() as $action) {
            $signers = DocumentSigner::where('document_id', $id)
                ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                ->where('document_signers.action_type_id', $action['id'])
                ->orderBy('document_signers.sequence')
                ->orderBy('departments.department_code')
                ->get();

            // if ($document->document_type_id == 10) {
            //     $signers = DocumentSigner::where('document_id', $id)
            //     ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
            //     ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
            //     ->where('document_signers.action_type_id', $action['id'])
            //     ->where('document_signers.action_type_id', '!=', 6)
            //     ->orderBy('document_signers.sequence')
            //     ->orderBy('departments.department_code')
            //     ->get();
            // }
            // PDF da faqat sequence si 1 ga teng bo'lgan tasdiqlovchilarni chiqaradi
            if (in_array($document->document_template_id, [2, 56, 58])) {
                $signers = DocumentSigner::where('document_id', $id)
                    ->leftJoin('staff', 'document_signers.staff_id', '=', 'staff.id')
                    ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
                    ->where('document_signers.action_type_id', $action['id'])
                    ->where('document_signers.sequence', 1)
                    ->orderBy('departments.department_code')
                    ->get();
            }
            if ($signers && count($signers) > 0 && !in_array($document->document_template_id, [2, 58, 64]) && !$position) {
                $content .= '<tr><td style="border-bottom:2px solid #000;" colspan="3"><b>' . $action['name_' . $document->locale] . '</b></td></tr>';
            }




            foreach ($signers as $key => $value) {
                if (in_array($document->document_template_id, [64]) && $action['id'] != 2) {
                    continue;
                }
                $content .= '<tr>';
                $content .= '<td style="width:400px;font-size:18pt;padding:20px 0 20px;">';
                if ($position) {
                    $content .= '<b>' . $position . '</b>';
                    $count = 1;
                } else {
                    $content .= '<b>' . $value->staff->position['name_' . $document->locale] . '</b>';
                }
                // if ($value->staff && $value->staff->department) {
                //     $content .= '<br><i><b>' . $value->staff->department['name_' . $document->locale] . '</i></b>';
                // }
                $content .= '</td>';
                $content .= '<td style="padding:5px; text-align:center;">';
                if ($document->document_template_id == 157 && $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues[3]) {
                    $locale = 'uz_latin';
                }
                $locale = $document->locale == 'ru' ? 'uz_cyril' : $document->locale;
                if ($position) {
                    $locale = 'uz_latin';
                }
                if ($value->signerEmployee) {
                    $fio = '<b>' . substr($value->signerEmployee['firstname_' . $locale], 0, $count) . '. ' . '</b>';
                    $fio .= '<b>' . $value->signerEmployee['middlename_' . $locale] . '<b>' != '' ? '<b>' . substr($value->signerEmployee['middlename_' . $locale], 0, $count) . '. ' . '</b>' : '';
                    $fio .= '<b>' . $value->signerEmployee['lastname_' . $locale] . '</b>';
                    $fioLatin = substr($value->signerEmployee['firstname_uz_latin'], 0, 1) . '. ';
                    $fioLatin .= $value->signerEmployee['middlename_uz_latin'] != '' ? substr($value->signerEmployee['middlename_uz_latin'], 0, 1) . '. ' : '';
                    $fioLatin .= $value->signerEmployee['lastname_uz_latin'];
                } elseif ($value->employeeStaffs && $value->employeeStaffs->employee) {
                    $fio = '<b>' . substr($value->employeeStaffs->employee['firstname_' . $locale], 0, $count) . '. ' . '</b>';
                    $fio .= '<b>' . $value->employeeStaffs->employee['middlename_' . $locale] . '<b>' != '' ? '<b>' . substr($value->employeeStaffs->employee['middlename_' . $locale], 0, $count) . '. ' . '</b>' : '';
                    $fio .= '<b>' . $value->employeeStaffs->employee['lastname_' . $locale] . '</b>';
                    $fioLatin = substr($value->employeeStaffs->employee['firstname_uz_latin'], 0, 1) . '. ';
                    $fioLatin .= $value->employeeStaffs->employee['middlename_uz_latin'] != '' ? substr($value->employeeStaffs->employee['middlename_uz_latin'], 0, 1) . '. ' : '';
                    $fioLatin .= $value->employeeStaffs->employee['lastname_uz_latin'];
                } else {
                    $fio = '';
                    $fioLatin = '';
                }
                if ($value->status == 1) {
                    $signType = $value->sign_type ? 'EIMZO' : 'LDAP';
                    // if(!$value->signed_date)
                    // {
                    //     $content .= '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0,90,169)->format('png')->encoding('UTF-8')->size(80)->generate($fioLatin . ' ' .date('Y-m-d H:i', $value->signed_date))) . '"/>';
                    //     $content .= '<span style="display:block;margin-top:5px; font-size:12px;">'.($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)).'</span>';
                    // }
                    $content .= '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)->generate($fioLatin . ' ' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)))) . '"/>';
                    // $content .= '<span style="display:block;margin-top:5px; font-size:12px;">' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : substr($value->taken_datetime, 0, 16)) . '</span>';
                } elseif ($value->status == 2) {
                    $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
                    // $content .= '<span style="display:block;margin-top:5px; font-size:12px;">' . ($value->signed_date ? date('Y-m-d H:i', $value->signed_date) : '') . '</span>';
                } else {
                    $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
                }
                $content .= '</td>';
                $content .= '<td style="width:200px;text-align:right;font-size:18pt;">';
                $content .= $fio;
                if ($value->taken_datetime) {
                    // $content .= '<br><span style="font-size:14pt;">' . date('Y-m-d H:i', strtotime($value->taken_datetime)) . '</span>';
                }
                $content .= '</td>';
                $content .= '</tr>';
            }
        }
        return $content .= '</tbody></table>';
    }

    public static function generatePdf($document_id, $withComment = false)
    {
        $document = self::find($document_id);
        // $doc_temp = DocumentTemplate::find($document->document_template_id);
        // $language = [
        //     'uzauto' => [
        //         'ru' => 'АО «UzAuto Motors»',
        //         'uz_cyril' => '«UzAuto Motors» АЖ',
        //         'uz_latin' => '«UzAuto Motors» AJ',
        //     ],
        // ];
        $language = [
            'uzauto' => [
                'ru' => config("app.APP_COMPANY_NAME_RU"),
                'uz_cyril' => config("app.APP_COMPANY_NAME_UZ_CYRIL"),
                'uz_latin' => config("app.APP_COMPANY_NAME_UZ_LATIN"),
            ],
        ];
        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "freeserif";
                letter-spacing:1px;
            }
            @page {
                padding: 0px 0px 0px;
            }
            body{
                 font-family: "times";
                //font-family: "freeserif";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }
            #line1 {
                border-bottom:1px solid black;
                margin-top:30px;
            }
            #line10 {border-top:5px solid black;}
            .dynamic_table{
                font-size:' . $document->documentTemplate->table_font_size . 'px;
            }
        </style>';
        if ($document->status == 8) {
            $content .= '<img style="position:fixed; left:10px; top:300px;" height="575" src="/var/www/workflow/backend/storage/app/blanks/annulirovano.png">';
        }
        if ($document->document_template_id == 234) {
            $content .= '<img style="position:fixed; left:0px; top:0px;" height="180" src="https://edo.uzautomotors.com/img/address_t.jpg">';
            $content .= '<br>';
            $content .= '<br>';
            $content .= '<br>';
            $content .= '<br>';
            $content .= '<br>';
        }
        if ($document->document_template_id == 439) {
            $content .= '<img style="margin-bottom:-100px;" height="140" src="' . public_path('img/tracker.png') . '">';
        }
        if ($document->document_template_id == 434) {
            if (in_array($document->status, [3, 4, 5, 8])) {
                $content .= '<img style="width:250px;" src="' . public_path() . '/img/burchak.png">';
                $content .= '<p style="position:fixed; left:25px; top:155px;font-size:14pt;width:170px; text-align:center;">';
                $content .= $document->document_number_reg ? $document->document_number_reg : $document->document_number;
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:45px; top:190px;font-size:14pt;">';
                $content .= date('d', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date));
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:88px; top:190px;font-size:14pt;width:77px; text-align:center;">';
                $m = self::getMonthName(date('m', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date)), $document->locale);
                $content .= $m;
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:175px; top:190px;font-size:14pt;">';
                $content .= date('Y', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date));
                $content .= '</p>';
                $content .= '<p style="position:fixed; right:50px;">';
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                $content .= '<img style="position:fixed; right:15px; top:15px;" width="80" height="80" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';
                $content .= '</p>';
            }
        } elseif ($document->document_template_id == 438) {

            if (in_array($document->status, [3, 4, 5, 8])) {
                $content .= '<img style="width:250px;" src="' . public_path() . '/img/burchak-shtamp-asaka.png">';
                $content .= '<p style="position:fixed; left:48px; top:140px;font-size:12pt;width:135px; text-align:center;">';
                $content .= $document->document_number_reg ? $document->document_number_reg : $document->document_number;
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:50px; top:185px;font-size:12pt;">';
                $content .= date('d', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date));
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:88px; top:183px;font-size:12pt;width:77px; text-align:center;">';
                $m = self::getMonthName(date('m', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date)), $document->locale);
                $content .= $m;
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:185px; top:185px;font-size:12pt;">';
                $content .= date('Y', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date));
                $content .= '</p>';
                $content .= '<p style="position:fixed; right:50px;">';
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                $content .= '<img style="position:fixed; right:15px; top:15px;" width="80" height="80" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';
                $content .= '</p>';
            }
        } elseif ($document->document_template_id == 625) {

            //  $content .= '<img style="width:250px;" src="' . public_path() . '/img/burchak-shtamp-asaka.png">';

            // if (in_array($document->status, [3, 4, 5, 8])) {
            $content .= '<p style="position:fixed; right:50px;">';
            $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
            $content .= '<img style="position:fixed; right:15px; top:0" width="80" height="80" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';
            $content .= '</p>';
            // }
        } elseif ($document->documentTemplate->is_manual_file) {
            $content .= self::manualDocument($document_id);
            if (in_array($document->status, [3, 4, 5, 8])) {
                $content .= '<p style="position:fixed; left:100px; top:5px;font-size:16pt;width:200px; text-align:center;">';
                $content .= $document->document_number_reg ? $document->document_number_reg : $document->document_number;
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:115px; top:30px;font-size:16pt;">';
                $content .= date('d', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date));
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:145; top:30px;font-size:16pt;width:77px; text-align:center;">';
                $m = self::getMonthName(date('m', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date)), $document->locale);
                $content .= $m;
                $content .= '</p>';
                $content .= '<p style="position:fixed; left:225px; top:30px;font-size:16pt;">';
                $content .= date('Y', strtotime($document->document_date_reg ? $document->document_date_reg : $document->document_date));
                $content .= '</p>';
                $content .= '<p style="position:fixed; right:50px;">';
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                $content .= '<img style="position:fixed; left:15px; top:0px;" width="80" height="80" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';
                $content .= '</p>';
            }
        } else {
            if ($document->document_template_id == 305 || $document->document_template_id == 357) {
            } else {

                if (in_array($document->status, [3, 4, 5, 8]) && $document->document_template_id != 558) {
                    $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                    $content .= '<img style="position:fixed; right:10px; top:12px;" width="80" height="80" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';
                }
                if ($document->document_template_id == 558) {
                } else {
                    $content .= '<center style="height:10px;"></center>';
                    $content .= '<center style="font-size: 20pt; font-weight:bold;">' . $language['uzauto'][$document->locale] . '</center>';
                }

                if ($document->document_template_id == 479) {
                    $content .= '<center style="font-size: 20pt;margin-top:15px; font-weight:bold; text-transform: uppercase;">' . $document->documentType['name_' . $document->locale] . '</center><hr id="line10"><hr id="line1" style="margin-bottom:20px;margin-top:-7px;">';
                } elseif ($document->document_template_id == 558) {
                } elseif ($document->document_template_id == 619) {
                    $content .= '<center style="font-size: 24pt;margin-top:15px; font-weight:bold; text-transform: uppercase;"></center><hr id="line10"><hr id="line1" style="margin-bottom:20px;margin-top:-7px;">';
                } else {
                    $content .= '<center style="font-size: 24pt;margin-top:15px; font-weight:bold; text-transform: uppercase;">' . $document->documentType['name_' . $document->locale] . '</center><hr id="line10"><hr id="line1" style="margin-bottom:20px;margin-top:-7px;">';
                }
                $content .= self::fromTo($document_id);

                if (in_array($document->document_template_id, [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543, 545, 546, 547, 548, 583, 588, 595, 609, 618])) {
                    $content .= '<div style="text-align:center;font-size:14pt;position:fixed;top:115px; width:100%;">№: ';
                    $content .= $document->responsible_contact;
                    $content .= '</div>';
                }

                if ($document->documentTemplate->is_from_to_department_show) {
                    $content .= '<div style="letter-spacing:0.7px;text-align:center;"><h2>';
                    $content .= $document->documentTemplate["name_" . $document->locale];
                    $content .= '</h2></div>';
                }
                if ($document->document_template_id == 620) {
                    $content .= Graphic::graficTable($document->id);
                }
            }
        }

        $content .= '<div style="">';
        $content .= self::normalizeDocumentContent(self::documentContent($document_id));
        $content .= '</div>';
        $content .= self::getSignerTable($document_id);

        //Tenge Arizachi qo'shish
        if ($document->document_template_id == 564) {
            $content .= '<table style="border-collapse: collapse;width: 100%;margin-top:20px;">';
            $content .= '<tr>';
            $content .= '<td style="width:400px;font-size:18pt;padding:20px 0 20px;">';
            $content .= '<b> Fuqaro </b>';
            $content .= '</td>';
            $content .= '<td style="padding:5px; text-align:center; vertical-align: middle;">';
            $content .= '<div style="">';
            $content .= '<span style="font-size:14pt;">' . date('Y-m-d H:i', strtotime($document->created_at)) . '</span>';
            $content .= '</div>';
            $content .= '</td>';
            $content .= '<td style="width:220px;text-align:right;font-size:18pt;">';

            foreach ($document->documentDetails[0]->documentDetailAttributeValues as $key => $value) {
                if ($value->d_d_attribute_id == 2424) {
                    $fio = $value->attribute_value;
                }
            }
            $fullname = explode(" ", $fio);
            $middlename = null;
            if (count($fullname) == 3) {
                $lastname = $fullname[0];
                $firstname = $fullname[1];
                $middlename = $fullname[2];
            } else if (count($fullname) == 2) {
                $lastname = $fullname[1];
                $firstname = $fullname[0];
            } else if (count($fullname) == 4) {
                $lastname = $fullname[0];
                $firstname = $fullname[1];
                $middlename = $fullname[2] . $fullname[3];
            } else {
                $lastname = isset($fullname[0]) ? $fullname[0] : '';
                $firstname = isset($fullname[1]) ? $fullname[1] : '';
                $middlename = isset($fullname[2]) ? $fullname[2] : '';
            }
            // dd(count($surname));
            // $firstname = explode(" ", $surname[0], 2); // Agar ismi birinchi kelsa, Kamol Faxriddinov
            $content .= '<b>';
            if (preg_match('/[А-Яа-яЁё]/u', $firstname)) {
                $content .= substr($firstname, 0, 2);
            } else {
                $content .= substr($firstname, 0, 1);
            }
            $content .= '. ';
            if ($middlename) {
                if (preg_match('/[А-Яа-яЁё]/u', $middlename)) {

                    $content .= substr($middlename, 0, 2);
                    $content .= '. ';
                } else {

                    $content .= substr($middlename, 0, 1);
                    $content .= '. ';
                }
            }
            // $content .= substr($surname[0], 0, 1);
            $content .= ucfirst(strtolower($lastname)); // Agar ismi birinchi kelsa, Kamol Faxriddinov
            $content .= '</b>';
            $content .= '<br><span style="font-size:14pt;">' . date('Y-m-d H:i', strtotime($document->created_at)) . '</span>';
            $content .= '</td>';
            $content .= '</tr>';
            $content .= '</tbody></table>';
        }
        //Tenge Arizachi qo'shish


        if ($withComment) {
            $content .= '<div style = "display:block; clear:both; page-break-after:always;"></div>';
            $content .= self::getComments($document_id);
        }

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        if ($document->document_template_id == 305 || $document->document_template_id == 357) {
            // $pdf->setOption('header-html', 'https://edo.uzautomotors.com/header_fb.html?data1=' . 'a' . '&data4=' . 'b' . '&data5=' . 'c' . '&data6=' . 'd');
            $pdf->setPaper('a5');
            $date = $document->document_date_reg ? $document->document_date_reg : $document->document_date;
            if ($date) {
                $content .= '<div style="font-weight:bold;">';
                // $content .= '№'; 2023-yil 16 may, 0202-PA-son
                $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
                $content .= substr($date, 0, 4) . '-yil ' . substr($date, 8, 2) . '-' . $monthList[substr($date, 5, 2) * 1 - 1] . ', ';
                $content .= $document->document_number_reg ? $document->document_number_reg : $document->document_number;
                $content .= '-son<br>';
                $content .= '</div>';
            }
        }
        //Kiruvchi hujjatlar uchun pdf dizaynini o'zgartirish
        if ($document->document_template_id == 558) {
            $pdf->setOption('margin-top', 5);
        } else {
            $pdf->setOption('margin-top', 15);
        }
        $pdf->setOption('images', true)
            ->setOption('footer-right', '[page] / [topage]')
            ->setOption('footer-font-name', 'times')
            ->setOption('footer-font-size', '10')
            ->setPaper('a4')
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 20)
            ->setOption('margin-right', 15)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);

        //Kiruvchi hujjatlar uchun pdf dizaynini o'zgartirish
        if ($document->document_template_id == 305 || $document->document_template_id == 357) {
            // $pdf->setOption('header-html', 'https://edo.uzautomotors.com/header_fb.html?data1=' . 'a' . '&data4=' . 'b' . '&data5=' . 'c' . '&data6=' . 'd');
            $pdf->setPaper('a5');
        }
        //Kiruvchi hujjatlar uchun pdf dizaynini o'zgartirish

        try {
            $filename = public_path('temp/' . (microtime(true) * 10000) . rand(0, 10000) . '.pdf');
            $pdf->save($filename);
            $base64 = base64_encode(file_get_contents($filename));
            unlink($filename);
            return $base64;
            // $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public static function getComments($id)
    {
        $document = self::find($id);
        $count = $document->locale == 'uz_latin' ? 1 : 2;
        $locale = $document->locale == 'ru' ? 'uz_cyril' : $document->locale;
        $signers = DocumentSigner::where('document_id', $id)
            ->orderBy('sequence', 'ASC')
            ->orderBy('taken_datetime', 'DESC')
            ->get();
        $content = '<table border="1" style="border-collapse:collapse;width:100%;">';
        $content .= '<tr>';
        $content .= '<th style="padding:4px;width:20%;">';
        $content .= 'Резолюция';
        $content .= '</th>';
        $content .= '<th style="padding:4px;width:20%;">';
        $content .= 'Сотрудник';
        $content .= '</th>';
        $content .= '<th style="padding:4px;">';
        $content .= 'Тип действия';
        $content .= '</th>';
        $content .= '<th style="padding:4px;">';
        $content .= 'Комментарий';
        $content .= '</th>';
        $content .= '</tr>';
        foreach ($signers as $key => $value) {
            $fio = '';
            $parentFio = '';
            if ($value->signerEmployee) {
                if (substr($value->signerEmployee['firstname_' . $locale], 0, 2) == 'Sh') {
                    $fio = substr($value->signerEmployee['firstname_' . $locale], 0, 2) . '. ';
                } else {
                    $fio = substr($value->signerEmployee['firstname_' . $locale], 0, $count) . '. ';
                }
                if (substr($value->signerEmployee['middlename_' . $locale], 0, 2) == 'Sh') {
                    $fio .= !in_array($value->signerEmployee['middlename_' . $locale], ['', ' ']) ? substr($value->signerEmployee['middlename_' . $locale], 0, $count) . '. ' : '';
                } else {
                    $fio .= !in_array($value->signerEmployee['middlename_' . $locale], ['', ' ']) ? substr($value->signerEmployee['middlename_' . $locale], 0, $count) . '. ' : '';
                }
                $fio .= $value->signerEmployee['lastname_' . $locale];
                //------------------------------------------------------------
                if ($value->parent_employee_id) {
                    $parentEmployee = Employee::find($value->parent_employee_id);
                    if (substr($parentEmployee['firstname_' . $locale], 0, 2) == 'Sh') {
                        $parentFio = substr($parentEmployee['firstname_' . $locale], 0, 2) . '. ';
                    } else {
                        $parentFio = substr($parentEmployee['firstname_' . $locale], 0, $count) . '. ';
                    }
                    if (substr($parentEmployee['middlename_' . $locale], 0, 2) == 'Sh') {
                        $parentFio .= !in_array($parentEmployee['middlename_' . $locale], ['', ' ']) ? substr($parentEmployee['middlename_' . $locale], 0, $count) . '. ' : '';
                    } else {
                        $parentFio .= !in_array($parentEmployee['middlename_' . $locale], ['', ' ']) ? substr($parentEmployee['middlename_' . $locale], 0, $count) . '. ' : '';
                    }
                    $parentFio .= $parentEmployee['lastname_' . $locale];
                }
            } elseif ($value->employeeStaffs && $value->employeeStaffs->employee) {
                if (substr($value->employeeStaffs->employee['firstname_' . $locale], 0, 2) == 'Sh') {
                    $fio = substr($value->employeeStaffs->employee['firstname_' . $locale], 0, 2) . '. ';
                } else {
                    $fio = substr($value->employeeStaffs->employee['firstname_' . $locale], 0, $count) . '. ';
                }
                if (substr($value->employeeStaffs->employee['middlename_' . $locale], 0, 2) == 'Sh') {
                    $fio .= !in_array($value->employeeStaffs->employee['middlename_' . $locale], ['', ' ']) ? substr($value->employeeStaffs->employee['middlename_' . $locale], 0, 2) . '. ' : '';
                } else {
                    $fio .= !in_array($value->employeeStaffs->employee['middlename_' . $locale], ['', ' ']) ? substr($value->employeeStaffs->employee['middlename_' . $locale], 0, $count) . '. ' : '';
                }
                // $fio .= '<b>' . substr($value->employeeStaffs->employee['firstname_' . $locale], 0, $count) . '. ' . '</b>';
                $fio .= $value->employeeStaffs->employee['lastname_' . $locale];
            }

            $actionType = collect(self::actionTypes())->first(function ($v, $k) use ($value) {
                return $v['id'] == $value->action_type_id;
            });

            $content .= '<tr>';
            $content .= '<td style="padding:4px;">';
            $content .= $parentFio;
            $content .= '</td>';
            $content .= '<td style="padding:4px;">';
            $content .= $fio;
            $content .= '<br>';
            $content .= '<span style="font-size:12px;font-style: italic;">' . $value->department . '</span>';
            $content .= '<br>';
            $content .= '<span style="font-size:12px;font-weight:bold;">' . $value->position . '</span>';
            $content .= '</td>';
            $content .= '<td style="padding:4px;">';
            $content .= $actionType['name_ru'];
            $content .= '</td>';
            $content .= '<td style="padding:4px;">';
            // $content .= $value->description;
            foreach ($value->comments as $k => $comment) {
                if (!in_array($comment->comment, ['created', 'changed', 'published', 'processing', 'ok', '', null])) {
                    $content .= '<div style="text-align:justify;">' . $comment->comment . '</div>';
                    $content .= '<div style="text-align:right;font-size:12px;color:#0D47A1;">' . substr($comment->created_at, 0, 16) . '</div>';
                }
            }
            $content .= '</td>';
            $content .= '</tr>';
        }
        $content .= '</table>';
        return $content;
    }

    public static function actionTypes()
    {
        return [
            [
                'id' => 2,
                'name_uz_latin' => "Tasdiq",
                'name_uz_cyril' => "Тасдиқ",
                'name_ru' => "Утверждение"
            ],
            [
                'id' => 9,
                'name_uz_latin' => "Komissiya raisi",
                'name_uz_cyril' => "Комиссия раиси",
                'name_ru' => "Председатель комиссии"
            ],
            [
                'id' => 8,
                'name_uz_latin' => "Komissiya a'zolari",
                'name_uz_cyril' => "Комиссия аъзолари",
                'name_ru' => "Члены комиссии"
            ],
            [
                'id' => 12,
                'name_uz_latin' => "Kuzatuvchi",
                'name_uz_cyril' => "Кузатувчи",
                'name_ru' => "Наблюдатель"
            ],
            [
                'id' => 10,
                'name_uz_latin' => "Komissiya kotibi",
                'name_uz_cyril' => "Комиссия котиби",
                'name_ru' => "Секретарь комиссии"
            ],
            [
                'id' => 1,
                'name_uz_latin' => "Rozilik",
                'name_uz_cyril' => "Розилик",
                'name_ru' => "Согласование"
            ],
            [
                'id' => 3,
                'name_uz_latin' => "Bo'lim ichida rozilik",
                'name_uz_cyril' => "Бўлим ичида розилик",
                'name_ru' => "Согласование внутри подразделение"
            ],
            [
                'id' => 4,
                'name_uz_latin' => "Bajaruvchilar",
                'name_uz_cyril' => "Бажарувчилар",
                'name_ru' => "Исполнители"
            ],
            [
                'id' => 11,
                'name_uz_latin' => "Nazoratchi",
                'name_uz_cyril' => "Назоратчи",
                'name_ru' => "Контрольщик"
            ],
            [
                'id' => 5,
                'name_uz_latin' => "Ma'lumot uchun",
                'name_uz_cyril' => "Маълумот учун",
                'name_ru' => "Для информации"
            ],
            [
                'id' => 13,
                'name_uz_latin' => "Hujjat yaratuvchisi",
                'name_uz_cyril' => "Ҳужжат яратувчиси",
                'name_ru' => "Создатель документа",
            ],
            [
                'id' => 14,
                'name_uz_latin' => "Taqatuvchi",
                'name_uz_cyril' => "Тарқатувчи",
                'name_ru' => "Рассылки"
            ]
        ];
    }

    public static function generateCommentPdf($document_id)
    {
        $document = self::with('documentDetails')->find($document_id);
        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "freeserif";
                letter-spacing:1px;
            }
            @page {
                padding: 0px 0px 0px;
            }
            body{
                 font-family: "times";
                //font-family: "freeserif";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }
            #line1 {
                border-bottom:1px solid black;
                margin-top:30px;
            }
            #line10 {border-top:5px solid black;}
        </style>';

        // $content .= '<div style="">';
        // $content .= Self::normalizeDocumentContent(Self::documentContent($document_id));
        // $content .= '</div>';

        $ds = DocumentSigner::where('document_id', $document_id)
            ->with([
                'comments' => function ($q) {
                    $q->orderBy('created_at', 'asc');
                }
            ])
            ->with('signerEmployee')
            ->orderBy('sequence', 'asc')
            ->get()->toArray();

        $content .= '<table>';
        foreach ($ds as $key => $value) {
            foreach ($ds->comments as $kc => $comment) {
                # code...
            }
            $content .= '<tr>';
            $content .= '<td>';
            $content .= '</td>';
            $content .= '</tr>';
        }
        $content .= '</table>';

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        $pdf->setOption('images', true)
            ->setOption('footer-right', '[page] / [topage]')
            ->setOption('footer-font-name', 'times')
            ->setOption('footer-font-size', '10')
            ->setPaper('a4')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 20)
            ->setOption('margin-right', 15)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public static function getYearhName($locale)
    {
        // $locale = 'uz_latin';
        switch ($locale) {
            case 'uz_latin':
                return ' yil';
            case 'uz_cyril':
                return 'йил';
            default:
                return ' год';
        }
    }

    public static function getSonliName($locale, $document_number)
    {
        // $locale = 'uz_latin';
        switch ($locale) {
            case 'uz_latin':
                return $document_number . '-sonli';
            case 'uz_cyril':
                return $document_number . '-cонли';
            default:
                return '№ ' . $document_number;
        }
    }

    public static function getMonthName($month, $locale)
    {
        // $locale = 'uz_latin';
        $months = [
            'ru' => [
                '01' => 'январь‎',
                '02' => 'февраль‎',
                '03' => 'март‎',
                '04' => 'апрель‎',
                '05' => 'май‎',
                '06' => 'июнь‎',
                '07' => 'июль‎',
                '08' => 'август‎',
                '09' => 'сентябрь‎',
                '10' => 'октябрь‎',
                '11' => 'ноябрь‎',
                '12' => 'декабрь‎',
            ],
            'uz_cyril' => [
                '01' => 'январ‎',
                '02' => 'феврал‎',
                '03' => 'март‎',
                '04' => 'апрел‎',
                '05' => 'май‎',
                '06' => 'июнь',
                '07' => 'июл‎',
                '08' => 'август‎',
                '09' => 'сентябр‎',
                '10' => 'октябр‎',
                '11' => 'ноябр‎',
                '12' => 'декабр‎',
            ],
            'uz_latin' => [
                '01' => 'yanvar',
                '02' => 'fevral',
                '03' => 'mart',
                '04' => 'aprel',
                '05' => 'may',
                '06' => 'iyun',
                '07' => 'iyul',
                '08' => 'avgust',
                '09' => 'sentabr',
                '10' => 'oktabr',
                '11' => 'noyabr',
                '12' => 'dekabr',
            ]
        ];
        return $months[$locale][$month];
    }

    public static function generatePdfUsf_old($document_id)
    {
        $document = self::with('documentDetails')->find($document_id);
        $language = [
            'uzauto' => [
                'ru' => config("app.APP_COMPANY_NAME_RU"),
                'uz_cyril' => config("app.APP_COMPANY_NAME_UZ_CYRIL"),
                'uz_latin' => config("app.APP_COMPANY_NAME_UZ_LATIN"),
            ],
        ];
        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "freeserif";
                letter-spacing:1px;
            }
            body{
                margin-right: 80px;
                margin-left: 120px;
                font-family: "times";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }
        </style>';

        // if($document->document_date_reg == ''){
        //     $day = date('d', strtotime($document->document_date));
        //     $year = date('y', strtotime($document->document_date));
        //     $month = $monthList[date('m', strtotime($document->document_date)) - 1];
        // }
        // else {
        //     $day = date('d', strtotime($document->document_date_reg));
        //     $year = date('y', strtotime($document->document_date_reg));
        //     $month = $monthList[date('m', strtotime($document->document_date_reg)) - 1];
        // }
        $document_date = $document->document_date_reg == '' ? $document->document_date : $document->document_date_reg;
        $document_number = $document->document_number_reg == '' ? $document->document_number : $document->document_number_reg;

        if ($document->documentType->code == 'US' || in_array($document->document_template_id, [57])) {
            $attribute = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
            $attr0 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 && $document->documentDetails[0]->documentDetailAttributeValues[0] ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
            $attr1 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 1 && $document->documentDetails[0]->documentDetailAttributeValues[1] ? $document->documentDetails[0]->documentDetailAttributeValues[1]->attribute_value : "";
            $attr2 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 2 && $document->documentDetails[0]->documentDetailAttributeValues[2] ? $document->documentDetails[0]->documentDetailAttributeValues[2]->attribute_value : "";
            $attr3 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 3 ? $document->documentDetails[0]->documentDetailAttributeValues[3]->attribute_value : "";
            $attr5 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 4 ? $document->documentDetails[0]->documentDetailAttributeValues[4]->attribute_value : "";

            $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
            $day = date('d', strtotime($document_date));
            $year = date('y', strtotime($document_date));
            $month = $monthList[date('m', strtotime($document_date)) - 1];
            $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/pechat3.png" width="250">';
            $content .= '<p style="position:absolute;top:136px;left:161px;">' . $year . '</p>';
            $content .= '<p style="position:absolute;top:136px;left:215px;">' . $day . '</p>';
            $content .= '<p style="position:absolute;top:135px;left:250px;text-align:center;width:100px;">' . $month . '</p>';
            $content .= '<p style="position:absolute;top:160px;left:143px;text-align:center;width:170px;">' . $document_number . '</p>';

            $content .= '<table style="font-size:16pt;width:100%;">';
            if ($attr0) {
                $content .= '<tr>';
                $content .= '<td style="width:40%;font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="width:15%;font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;vertical-align: bottom;">';
                $content .= $attr2;
                $content .= '<br>';
                $content .= $attr3;
                $content .= '</td>';
                $content .= '</tr>';
                $content .= '<tr>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '<br>';
                // $content .= $attr6;
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';
        }

        if (!(in_array($document->document_template_id, [1, 6, 33, 43, 44, 54, 64, 57]) || $document->documentType->code == 'PM' || $document->documentType->code == 'US' || $document->documentType->code == 'PG' || $document->documentType->code == 'TZ' || $document->documentType->code == 'XX' || $document->documentType->code == 'PP')) {
            $content .= '<table style="width:100%; color:#44609c;">';
            $content .= '<tr>';
            $content .= '<td style="width:50%;vertical-align:top;" rowspan="2">';
            $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/uzautojsc.jpg" width="240">';
            $content .= '</td>';
            $content .= '<td style="text-align:right;width:50%;vertical-align:top;padding-top:30;">';
            if ($document->documentType['name_uz_latin'] == 'Buyruq') {
                $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/buyruq.jpg" height="35">';
            } elseif ($document->documentType['name_uz_latin'] == 'Farmoyish') {
                $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/farmoyish.jpg" height="35">';
            }
            $content .= '</td>';
            $content .= '</tr>';
            $content .= '<tr>';
            $content .= '<td style="font-size:18pt;text-align:right;padding-top:23px;">';
            $content .= '<div style="display: -webkit-box; display: flex;">';
            $content .= '<div style="font-size:18pt;-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;width:290px;">';
            $content .= self::getSonliName($document->locale, $document_number) . '<br>';
            $content .= substr($document->document_date, 0, 4) . self::getYearhName($document->locale) . ' «' . (substr($document_date, 8, 2) * 1) . '» ' . self::getMonthName(substr($document_date, 5, 2), $document->locale);
            $content .= '</div>';
            $content .= '</td>';
            $content .= '</tr>';
            $content .= '<tr>';
            $content .= '<td>';
            $content .= '</td>';
            $content .= '<td style="text-align:right;">';
            if (($document->status == 3 || $document->status == 4 || $document->status == 5)) {
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                if ($document->documentType->code == 'BB' || $document->documentType->code == 'FM' || $document->documentType->code == 'PP') {
                } else {
                    $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;">';
                    $content .= '<img style=" width="80" height="80" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';
                    $content .= '</div>';
                    $content .= '</div>';
                }
            } elseif ($document->status == 6 && !($document->documentType->code == 'BB' || $document->documentType->code == 'FM' || $document->documentType->code == 'PP')) {
                // dd($document->status);
                $content .= '<img style="float:right;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
            } elseif ($document->documentType->code == 'BB' || $document->documentType->code == 'FM' || $document->documentType->code == 'PP') {
            } else {
                $content .= '<img style="float:right;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
            }
            $content .= '</td>';
            $content .= '</tr>';

            $content .= '</table>';

            // $content .= '<hr style="margin-bottom:20px;border-bottom:1px solid #44609c;">';
            // $content .= Self::fromToUzavtosanoat($document_id);

            if ($document->documentTemplate->is_from_to_department_show) {
                $content .= '<div style="letter-spacing:0.7px;text-align:center;"><h2>';
                $content .= $document->documentTemplate["name_" . $document->locale];
                $content .= '</h2></div>';
            }
        } elseif (in_array($document->document_template_id, [1, 33, 44, 54, 64, 57]) || $document->documentType->code == 'US') {
        } else {
            if ($document->documentType->code == 'PP' || $document->documentType->code == 'PM' || $document->documentType->code == 'PG' || $document->documentType->code == 'XX' || $document->documentType->code == 'TZ') {
                $content .= '<div style="font-size:16pt;font-weight:bold;">';
                $content .= '№ ' . $document_number;
                $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
                $month = $monthList[date('m', strtotime($document_date)) - 1];
                $date = date('Y\y\i\l «d» ', strtotime($document_date)) . $month;
                $content .= '<br>' . $date;
                $content .= '</div>';
            } else {
                $content .= self::fromToUzavtosanoat($document_id);
            }
        }

        $content .= '<div style="">';
        $content .= self::normalizeDocumentContent(self::documentContent($document_id));
        $content .= '</div>';
        $content .= self::getSignerTableUzavtosanoat($document_id);

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        // $pdf->setOption('images', true)
        // ->setOption('header-html', 'https://edo.uzavtosanoat.uz/header.html');
        if (in_array($document->document_template_id, [1, 33, 44, 54])) {
            $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
            $day = date('d', strtotime($document_date));
            $year = date('y', strtotime($document_date));
            $month = $monthList[date('m', strtotime($document_date)) - 1];
            $number = urlencode(str_replace('/', '_', $document_number));
            $pdf->setOption('footer-html', 'https://edo.uzavtosanoat.uz/footer_pechat2.html?day=' . $day . '&month=' . $month . '&number=' . $number . '&year=' . $year);
        } elseif ($document->documentType->code == 'US' || in_array($document->document_template_id, [57])) {
            $isp = Employee::find($attr0);
            if ($isp) {
                $c = $document->locale == 'uz_latin' ? 1 : 2;
                $isp = $isp['lastname_' . $document->locale] . ' ' . substr($isp['firstname_' . $document->locale], 0, $c) . '. ' . substr($isp['middlename_' . $document->locale], 0, $c) . '.';
            } else {
                $isp = '';
            }
            $data1 = urlencode(str_replace(' ', '_', $isp));
            $data4 = urlencode((str_replace('+', 'plus', str_replace('#', 'rushotka', str_replace(' ', '_', $attr1)))));
            $data5 = $document_number;

            if ($isp != '') {
                $pdf->setOption('footer-html', 'https://edo.uzavtosanoat.uz/footer2.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5);
                // $content .= '<div style = "display:block; clear:both; page-break-after:always;"></div>';
            }
        } elseif (!in_array($document->document_template_id, [1, 6, 64]) && $document->documentType->code != 'PG' && $document->documentType->code != 'PM' && $document->documentType->code != 'XX' && $document->documentType->code != 'TZ' && $document->documentType->code != 'PP') {
            $pdf->setOption('footer-html', 'https://edo.uzavtosanoat.uz/footer.html');
        }

        if ($document->documentType->code == 'BB' || $document->documentType->code == 'FM' || $document->documentType->code == 'PP') {
            if ($document->status == 3 || $document->status == 4 || $document->status == 5) {
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                $img_base64_encoded = 'data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded));
            } elseif ($document->status == 6) {
                $img_base64_encoded = config('app.APP_EDO_URL') . '/img/crop-cancel.png';
            } else {
                $img_base64_encoded = config('app.APP_EDO_URL') . '/img/crop.png';
            }
            $pdf->setOption('header-html', 'https://edo.uzavtosanoat.uz/qr_code.html?data1=' . $img_base64_encoded);
        }

        $pdf->setPaper('a4')
            ->setOption('footer-spacing', 2)
            ->setOption('header-spacing', 0)
            ->setOption('margin-top', 25)
            ->setOption('margin-bottom', 35)
            ->setOption('margin-left', 3);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public static function generatePdfUzavtosanoat($document_id)
    {
        $document = self::with('documentDetails')->find($document_id);
        $language = [
            'uzauto' => [
                'ru' => config("app.APP_COMPANY_NAME_RU"),
                'uz_cyril' => config("app.APP_COMPANY_NAME_UZ_CYRIL"),
                'uz_latin' => config("app.APP_COMPANY_NAME_UZ_LATIN"),
            ],
        ];
        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "freeserif";
                letter-spacing:1px;
            }
            body{
                margin-right: 80px;
                margin-left: 120px;
                font-family: "times";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }
        </style>';

        // if($document->document_date_reg == ''){
        //     $day = date('d', strtotime($document->document_date));
        //     $year = date('y', strtotime($document->document_date));
        //     $month = $monthList[date('m', strtotime($document->document_date)) - 1];
        // }
        // else {
        //     $day = date('d', strtotime($document->document_date_reg));
        //     $year = date('y', strtotime($document->document_date_reg));
        //     $month = $monthList[date('m', strtotime($document->document_date_reg)) - 1];
        // }
        $document_date = $document->document_date_reg == '' ? $document->document_date : $document->document_date_reg;
        $document_number = $document->document_number_reg == '' ? $document->document_number : $document->document_number_reg;

        if ($document->documentType->code == 'US' || in_array($document->document_template_id, [57])) {
            $attribute = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
            $attr0 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 && $document->documentDetails[0]->documentDetailAttributeValues[0] ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
            $attr1 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 1 && $document->documentDetails[0]->documentDetailAttributeValues[1] ? $document->documentDetails[0]->documentDetailAttributeValues[1]->attribute_value : "";
            $attr2 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 2 && $document->documentDetails[0]->documentDetailAttributeValues[2] ? $document->documentDetails[0]->documentDetailAttributeValues[2]->attribute_value : "";
            $attr3 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 3 ? $document->documentDetails[0]->documentDetailAttributeValues[3]->attribute_value : "";
            $attr5 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 4 ? $document->documentDetails[0]->documentDetailAttributeValues[4]->attribute_value : "";

            $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
            $day = date('d', strtotime($document_date));
            $year = date('y', strtotime($document_date));
            $month = $monthList[date('m', strtotime($document_date)) - 1];
            $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/pechat3.png" width="250">';
            $content .= '<p style="position:absolute;top:136px;left:161px;">' . $year . '</p>';
            $content .= '<p style="position:absolute;top:136px;left:215px;">' . $day . '</p>';
            $content .= '<p style="position:absolute;top:135px;left:250px;text-align:center;width:100px;">' . $month . '</p>';
            $content .= '<p style="position:absolute;top:160px;left:143px;text-align:center;width:170px;">' . $document_number . '</p>';

            $content .= '<table style="font-size:16pt;width:100%;">';
            if ($attr0) {
                $content .= '<tr>';
                $content .= '<td style="width:40%;font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="width:15%;font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;vertical-align: bottom;">';
                $content .= $attr2;
                $content .= '<br>';
                $content .= $attr3;
                $content .= '</td>';
                $content .= '</tr>';
                $content .= '<tr>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '<br>';
                // $content .= $attr6;
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';
        }

        if (!(in_array($document->document_template_id, [1, 6, 33, 43, 44, 46, 54, 64, 57, 78, 79]) || $document->documentType->code == 'PM' || $document->documentType->code == 'US' || $document->documentType->code == 'PG' || $document->documentType->code == 'TZ' || $document->documentType->code == 'XX' || $document->documentType->code == 'PP' || $document->documentType->code == 'HR')) {
            $content .= '<table style="width:100%; color:#44609c;">';
            $content .= '<tr>';
            $content .= '<td style="width:50%;vertical-align:top;" rowspan="2">';
            $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/uzautojsc.jpg" width="240">';
            $content .= '</td>';
            $content .= '<td style="text-align:right;width:50%;vertical-align:top;padding-top:30;">';
            if ($document->documentType['name_uz_latin'] == 'Buyruq') {
                $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/buyruq.jpg" height="35">';
            } elseif ($document->documentType['name_uz_latin'] == 'Farmoyish') {
                $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/farmoyish.jpg" height="35">';
            }
            $content .= '</td>';
            $content .= '</tr>';
            $content .= '<tr>';
            $content .= '<td style="font-size:18pt;text-align:right;padding-top:23px;">';
            $content .= '<div style="display: -webkit-box; display: flex;">';
            $content .= '<div style="font-size:18pt;-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;width:290px;">';
            $content .= self::getSonliName($document->locale, $document_number) . '<br>';
            $content .= substr($document->document_date, 0, 4) . self::getYearhName($document->locale) . ' «' . (substr($document_date, 8, 2) * 1) . '» ' . self::getMonthName(substr($document_date, 5, 2), $document->locale);
            $content .= '</div>';
            $content .= '</td>';
            $content .= '</tr>';
            $content .= '<tr>';
            $content .= '<td>';
            $content .= '</td>';
            $content .= '<td style="text-align:right;">';
            if (($document->status == 3 || $document->status == 4 || $document->status == 5)) {
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                if ($document->documentType->code == 'BB' || $document->documentType->code == 'FM' || $document->documentType->code == 'PP') {
                } else {
                    $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;">';
                    $content .= '<img style=" width="80" height="80" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';
                    $content .= '</div>';
                    $content .= '</div>';
                }
            } elseif ($document->status == 6 && !($document->documentType->code == 'BB' || $document->documentType->code == 'FM' || $document->documentType->code == 'PP')) {
                // dd($document->status);
                $content .= '<img style="float:right;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
            } elseif ($document->documentType->code == 'BB' || $document->documentType->code == 'FM' || $document->documentType->code == 'PP') {
            } else {
                $content .= '<img style="float:right;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
            }
            $content .= '</td>';
            $content .= '</tr>';

            $content .= '</table>';

            // $content .= '<hr style="margin-bottom:20px;border-bottom:1px solid #44609c;">';
            // $content .= Self::fromToUzavtosanoat($document_id);

            if ($document->documentTemplate->is_from_to_department_show) {
                $content .= '<div style="letter-spacing:0.7px;text-align:center;"><h2>';
                $content .= $document->documentTemplate["name_" . $document->locale];
                $content .= '</h2></div>';
            }
        } elseif (in_array($document->document_template_id, [1, 33, 44, 54, 64, 57]) || $document->documentType->code == 'US') {
        } else {
            if ($document->documentType->code == 'PP' || $document->documentType->code == 'PM' || $document->documentType->code == 'PG' || $document->documentType->code == 'XX' || $document->documentType->code == 'TZ' || $document->document_template_id == 46) {
                $content .= '<div style="font-size:16pt;font-weight:bold;">';
                $content .= '№ ' . $document_number;
                $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
                $month = $monthList[date('m', strtotime($document_date)) - 1];
                $date = date('Y\y\i\l «d» ', strtotime($document_date)) . $month;
                $content .= '<br>' . $date;
                $content .= '</div>';
            } else {
                $content .= self::fromToUzavtosanoat($document_id);
            }
        }

        $content .= '<div style="">';
        $content .= self::normalizeDocumentContent(self::documentContent($document_id));
        $content .= '</div>';
        $content .= self::getSignerTableUzavtosanoat($document_id);

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }

        // $pdf->setOption('images', true)
        // ->setOption('header-html', 'https://edo.uzavtosanoat.uz/header.html');
        if (in_array($document->document_template_id, [1, 33, 44, 54])) {
            $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
            $day = date('d', strtotime($document_date));
            $year = date('y', strtotime($document_date));
            $month = $monthList[date('m', strtotime($document_date)) - 1];
            $number = str_replace('/', '_', $document_number);
            $number = urlencode(str_replace(' ', 'probel', $number));
            $pdf->setOption('footer-html', 'https://edo.uzavtosanoat.uz/footer_pechat2.html?day=' . $day . '&month=' . $month . '&number=' . $number . '&year=' . $year);
        } elseif ($document->documentType->code == 'US' || in_array($document->document_template_id, [57])) {
            $isp = Employee::find($attr0);
            if ($isp) {
                $c = $document->locale == 'uz_latin' ? 1 : 2;
                $isp = $isp['lastname_' . $document->locale] . ' ' . substr($isp['firstname_' . $document->locale], 0, $c) . '. ' . substr($isp['middlename_' . $document->locale], 0, $c) . '.';
            } else {
                $isp = '';
            }
            $data1 = urlencode(str_replace(' ', '_', $isp));
            $data4 = urlencode((str_replace('+', 'plus', str_replace('#', 'rushotka', str_replace(' ', '_', $attr1)))));
            $data5 = $document_number;

            if ($isp != '') {
                $pdf->setOption('footer-html', 'https://edo.uzavtosanoat.uz/footer2.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5);
                // $content .= '<div style = "display:block; clear:both; page-break-after:always;"></div>';
            }
        } elseif (!in_array($document->document_template_id, [1, 6, 46, 64]) && $document->documentType->code != 'PG' && $document->documentType->code != 'PM' && $document->documentType->code != 'XX' && $document->documentType->code != 'TZ' && $document->documentType->code != 'PP' && $document->documentType->code != 'HR' && $document->documentType->code != 'ID') {
            $pdf->setOption('footer-html', 'https://edo.uzavtosanoat.uz/footer.html');
        }

        if ($document->documentType->code == 'BB' || $document->documentType->code == 'FM' || $document->documentType->code == 'PP' || $document->document_template_id == 49) {
            if ($document->status == 3 || $document->status == 4 || $document->status == 5) {
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                $img_base64_encoded = 'data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded));
            } elseif ($document->status == 6) {
                $img_base64_encoded = config('app.APP_EDO_URL') . '/img/crop-cancel.png';
            } else {
                $img_base64_encoded = config('app.APP_EDO_URL') . '/img/crop.png';
            }
            $pdf->setOption('header-html', 'https://edo.uzavtosanoat.uz/qr_code.html?data1=' . $img_base64_encoded);
        }

        $pdf->setPaper('a4')
            ->setOption('footer-spacing', 2)
            ->setOption('header-spacing', 0)
            ->setOption('margin-top', 25)
            ->setOption('margin-bottom', 35)
            ->setOption('margin-left', 3);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public static function generatePdfVW($document_id)
    {
        $document = self::with('documentDetails')->find($document_id);
        $language = [
            'uzauto' => [
                'ru' => config("app.APP_COMPANY_NAME_RU"),
                'uz_cyril' => config("app.APP_COMPANY_NAME_UZ_CYRIL"),
                'uz_latin' => config("app.APP_COMPANY_NAME_UZ_LATIN"),
            ],
        ];
        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "freeserif";
                letter-spacing:1px;
            }
            body{
                margin-right: 80px;
                margin-left: 120px;
                font-family: "times";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }
        </style>';

        // if($document->document_date_reg == ''){
        //     $day = date('d', strtotime($document->document_date));
        //     $year = date('y', strtotime($document->document_date));
        //     $month = $monthList[date('m', strtotime($document->document_date)) - 1];
        // }
        // else {
        //     $day = date('d', strtotime($document->document_date_reg));
        //     $year = date('y', strtotime($document->document_date_reg));
        //     $month = $monthList[date('m', strtotime($document->document_date_reg)) - 1];
        // }
        $document_date = $document->document_date_reg == '' ? $document->document_date : $document->document_date_reg;
        $document_number = $document->document_number_reg == '' ? $document->document_number : $document->document_number_reg;

        // Burchak shtamp bilan chiqadi
        if (in_array($document->document_template_id, [56])) {
            $attribute = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
            $attr0 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 && $document->documentDetails[0]->documentDetailAttributeValues[0] ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
            $attr1 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 1 && $document->documentDetails[0]->documentDetailAttributeValues[1] ? $document->documentDetails[0]->documentDetailAttributeValues[1]->attribute_value : "";
            $attr2 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 2 && $document->documentDetails[0]->documentDetailAttributeValues[2] ? $document->documentDetails[0]->documentDetailAttributeValues[2]->attribute_value : "";
            $attr3 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 3 ? $document->documentDetails[0]->documentDetailAttributeValues[3]->attribute_value : "";
            $attr5 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 4 ? $document->documentDetails[0]->documentDetailAttributeValues[4]->attribute_value : "";

            $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
            $day = date('d', strtotime($document_date));
            $year = date('y', strtotime($document_date));
            $month = $monthList[date('m', strtotime($document_date)) - 1];
            $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/vw/burchak_shtamp.png" width="250">';
            $content .= '<p style="position:absolute;top:165px;left:310px;">' . $year . '</p>';
            $content .= '<p style="position:absolute;top:165px;left:165px;">' . $day . '</p>';
            $content .= '<p style="position:absolute;top:165px;left:190px;text-align:center;width:100px;">' . $month . '</p>';
            $content .= '<p style="position:absolute;top:190px;left:143px;text-align:center;width:220px;">' . $document_number . '</p>';

            $content .= '<table style="font-size:16pt;width:100%;">';
            if ($attr0) {
                $content .= '<tr>';
                $content .= '<td style="width:40%;font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="width:15%;font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;vertical-align: bottom;">';
                $content .= $attr2;
                $content .= '<br>';
                $content .= $attr3;
                $content .= '</td>';
                $content .= '</tr>';
                $content .= '<tr>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '<br>';
                // $content .= $attr6;
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';
        }

        if (!(in_array($document->document_template_id, []) || $document->documentType->code == 'PM' || $document->document_template_id == 'IS' || $document->documentType->code == 'PG' || $document->documentType->code == 'TZ' || $document->documentType->code == 'PP')) {
            // dd(config('app.APP_EDO_URL') . '/img/vw/buyruq_vw.png');
            if ($document->documentType['name_uz_latin'] == 'Buyruq') {
                $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/vw/buyruq_vw.png" height="101">';
            } elseif ($document->documentType['name_uz_latin'] == 'Farmoyish') {
                $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/vw/farmoyish_vw.png" height="101">';
            } elseif ($document->documentType->code == 'XX') {
                $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/vw/xizmat_xati_vw.png" height="102">';
            } elseif ($document->documentType->code == 'KX') {
                $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/vw/kiruvchi_vw.png" height="101">';
            }

            if (!in_array($document->document_template_id, [12, 31, 32, 34, 35, 37, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 56, 63, 64])) {
                // Dokument nomer tagidan 2ta enter tashlaydi
                $content .= '<div style="font-size:18pt;width:290px;position:fixed; top:40px; float:right;right:80px; text-align:right;">';
                $content .= substr($document->document_date, 0, 4) . self::getYearhName($document->locale) . ' «' . (substr($document_date, 8, 2) * 1) . '» ' . self::getMonthName(substr($document_date, 5, 2), $document->locale) . '<br>';
                $content .= self::getSonliName($document->locale, $document_number);
                $content .= '</div>';
            } elseif ($document->document_template_id == 56) {
            } else {
                $content .= '<div style="font-size:18pt;width:290px;position:fixed; top:0px; float:right;right:90px; text-align:right;">';
                $content .= substr($document->document_date, 0, 4) . self::getYearhName($document->locale) . ' «' . (substr($document_date, 8, 2) * 1) . '» ' . self::getMonthName(substr($document_date, 5, 2), $document->locale) . '<br>';
                $content .= self::getSonliName($document->locale, $document_number);
                $content .= '</div>';
                $content .= '<br>';
            }

            if ($document->document_template_id == 1) {
                $content .= '<br>';
                $content .= '<br>';
                $content .= '<br>';
                $content .= self::fromToUsf($document_id);
            }

            $content .= '<br>';
            $content .= '<br>';

            // dd( $document->documentType->code );
            if (($document->status == 3 || $document->status == 4 || $document->status == 5)) {
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                if (
                    $document->documentType->code == 'BB' ||
                    $document->documentType->code == 'FM' ||
                    $document->documentType->code == 'PP' ||
                    $document->documentType->code == 'PM' ||
                    $document->documentType->code == 'KX' ||
                    $document->documentType->code == 'FM' ||
                    $document->documentType->code == 'XX' ||
                    $document->documentType->code == 'AA' ||
                    $document->documentType->code == 'TR' ||
                    $document->documentType->code == 'KX' ||
                    $document->documentType->code == 'LS' ||
                    $document->documentType->code == 'IS' ||
                    $document->document_template_id == 56 ||
                    $document->document_template_id == 63 ||
                    $document->document_template_id == 64 ||
                    $document->documentType->code == 'HT'
                ) {
                    // qr code tepada chiqadi
                } else {
                    $content .= '<div style="-webkit-box-flex: 1;-webkit-flex: 1;flex: 1;">';
                    $content .= '<img style=" width="80" height="80" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded)) . '"/>';
                    $content .= '</div>';
                    $content .= '</div>';
                }
            } elseif (
                $document->status == 6 && !($document->documentType->code == 'BB' ||
                    $document->documentType->code == 'FM' || $document->documentType->code == 'PP' ||
                    $document->documentType->code == 'KX' || $document->documentType->code == 'XX' ||
                    $document->documentType->code == 'IS' || $document->documentType->code == 'AA' ||
                    $document->document_template_id == 56 || $document->documentType->code == 'TZ' ||
                    $document->documentType->code == 'LS' ||
                    $document->documentType->code == 'TR' || $document->documentType->code == 'HT')
            ) {
                // dd($document->status);
                $content .= '<img style="float:right;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
            } elseif (
                $document->documentType->code == 'BB' ||
                $document->documentType->code == 'FM' ||
                $document->documentType->code == 'PP' ||
                $document->documentType->code == 'PM' ||
                $document->documentType->code == 'KX' ||
                $document->documentType->code == 'XX' ||
                $document->documentType->code == 'IS' ||
                $document->documentType->code == 'AA' ||
                $document->documentType->code == 'TR' ||
                $document->documentType->code == 'TZ' ||
                $document->documentType->code == 'LS' ||
                $document->document_template_id == 56 ||
                $document->document_template_id == 63 ||
                $document->document_template_id == 64 ||
                $document->documentType->code == 'HT'
            ) {
            } else {
                $content .= '<img style="float:right;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
            }

            // $content .= '<table style="width:100%; color:#44609c;">';
            // $content .= '<tr>';
            // $content .= '<td colspan="2">';
            // $content .= '</td>';
            // $content .= '</tr>';
            // $content .= '<tr>';
            // $content .= '<td style="font-size:18pt;text-align:right;padding-top:23px;">';
            // $content .= '<div style="display: -webkit-box; display: flex;">';
            // $content .= '</td>';
            // $content .= '</tr>';
            // $content .= '<tr>';
            // $content .= '<td>';
            // $content .= '</td>';
            // $content .= '<td style="text-align:right;">';
            // $content .= '</td>';
            // $content .= '</tr>';
            // $content .= '</table>';

            // $content .= '<hr style="margin-bottom:20px;border-bottom:1px solid #44609c;">';
            // $content .= Self::fromToUzavtosanoat($document_id);

            if ($document->documentTemplate->is_from_to_department_show) {
                $content .= '<div style="letter-spacing:0.7px;text-align:center;"><h2>';
                $content .= $document->documentTemplate["name_" . $document->locale];
                $content .= '</h2></div>';
            }
        } elseif (in_array($document->document_template_id, [56]) || $document->documentType->code == 'US') {
        } else {
            if ($document->documentType->code == 'PP' || $document->documentType->code == 'PM' || $document->documentType->code == 'PG' || $document->documentType->code == 'TZ') {
                $content .= '<div style="font-size:16pt;font-weight:bold;">';
                $content .= '№ ' . $document_number;
                $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
                $month = $monthList[date('m', strtotime($document_date)) - 1];
                $date = date('Y\y\i\l «d» ', strtotime($document_date)) . $month;
                $content .= '<br>' . $date;
                $content .= '</div>';
            } else {
                // $content .= Self::fromToUzavtosanoat($document_id);
            }
        }

        $content .= '<div style="display:inline;">';
        $content .= self::normalizeDocumentContent(self::documentContent($document_id));
        $content .= '</div>';
        $content .= self::getSignerTableUsf($document_id);

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        if ($document->documentType->code == 'US' || in_array($document->document_template_id, [])) {
            $isp = Employee::find($attr0);
            if ($isp) {
                $c = $document->locale == 'uz_latin' ? 1 : 2;
                $isp = $isp['lastname_' . $document->locale] . ' ' . substr($isp['firstname_' . $document->locale], 0, $c) . '. ' . substr($isp['middlename_' . $document->locale], 0, $c) . '.';
            } else {
                $isp = '';
            }
            $data1 = urlencode(str_replace(' ', '_', $isp));
            $data4 = urlencode((str_replace('+', 'plus', str_replace('#', 'rushotka', str_replace(' ', '_', $attr1)))));
            $data5 = $document_number;

            if ($isp != '') {
                $pdf->setOption('footer-html', config('app.APP_EDO_URL') . '/footer2.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5);
                // $content .= '<div style = "display:block; clear:both; page-break-after:always;"></div>';
            }
        } elseif (!in_array($document->document_template_id, [1, 6, 64, 63]) && $document->documentType->code != 'PG' && $document->documentType->code != 'PM' && $document->documentType->code != 'XX' && $document->documentType->code != 'TZ' && $document->documentType->code != 'PP' && $document->documentType->code != 'HT') {
            $pdf->setOption('footer-html', config('app.APP_EDO_URL') . '/footer.html');
        }

        if (
            $document->documentType->code == 'BB' ||
            $document->documentType->code == 'FM' ||
            $document->documentType->code == 'PP' ||
            $document->documentType->code == 'PM' ||
            $document->documentType->code == 'KX' ||
            $document->documentType->code == 'XX' ||
            $document->documentType->code == 'IS' ||
            $document->documentType->code == 'AA' ||
            $document->documentType->code == 'HT' ||
            $document->documentType->code == 'TZ' ||
            $document->documentType->code == 'LS' ||
            $document->document_template_id == 56 ||
            $document->document_template_id == 63 ||
            $document->document_template_id == 64 ||
            $document->documentType->code == 'TR'
        ) {
            if ($document->status == 3 || $document->status == 4 || $document->status == 5) {
                $img_base64_encoded = config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name;
                $img_base64_encoded = 'data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate($img_base64_encoded));
            } elseif ($document->status == 6) {
                $img_base64_encoded = config('app.APP_EDO_URL') . '/img/crop-cancel.png';
            } else {
                $img_base64_encoded = config('app.APP_EDO_URL') . '/img/crop.png';
            }
            $pdf->setOption('header-html', config('app.APP_EDO_URL') . '/qr_code.html?data1=' . $img_base64_encoded);
        }

        $pdf->setPaper('a4')
            ->setOption('footer-spacing', 2)
            ->setOption('header-spacing', 0)
            ->setOption('margin-top', 25)
            ->setOption('margin-bottom', 35)
            ->setOption('margin-left', 3);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public static function generatePdfFirmBlankUzavtosanoat($document_id)
    {
        $document = Document::with('documentDetails.documentDetailContents')
            ->with([
                'documentDetails' => function ($q) {
                    $q->with('documentDetailAttributeValues');
                }
            ])
            ->with([
                'documentSigners' => function ($q) {
                    $q->with('parentEmployee')
                        ->with('signerEmployee')
                        ->with('staff.employees');
                }
            ])
            ->where('id', $document_id)
            ->first();

        $attribute = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
        $attr0 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 && $document->documentDetails[0]->documentDetailAttributeValues[0] ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
        $attr1 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 1 && $document->documentDetails[0]->documentDetailAttributeValues[1] ? $document->documentDetails[0]->documentDetailAttributeValues[1]->attribute_value : "";
        $attr2 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 2 && $document->documentDetails[0]->documentDetailAttributeValues[2] ? $document->documentDetails[0]->documentDetailAttributeValues[2]->attribute_value : "";
        $attr3 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 3 ? $document->documentDetails[0]->documentDetailAttributeValues[3]->attribute_value : "";
        $attr5 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 4 ? $document->documentDetails[0]->documentDetailAttributeValues[4]->attribute_value : "";
        $attr6 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 5 ? $document->documentDetails[0]->documentDetailAttributeValues[5]->attribute_value : "";
        $attr7 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 6 ? $document->documentDetails[0]->documentDetailAttributeValues[6]->attribute_value : "";
        $attr8 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 7 ? $document->documentDetails[0]->documentDetailAttributeValues[7]->attribute_value : "";
        $attr9 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 8 ? $document->documentDetails[0]->documentDetailAttributeValues[8]->attribute_value : "";
        $attr10 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 9 ? $document->documentDetails[0]->documentDetailAttributeValues[9]->attribute_value : "";

        // if($document->id == 854)
        // {
        //     dd([$attr7,$attr8]);
        // }
        $language = [
            'uzauto' => [
                'ru' => config("app.APP_COMPANY_NAME_RU"),
                'uz_cyril' => config("app.APP_COMPANY_NAME_UZ_CYRIL"),
                'uz_latin' => config("app.APP_COMPANY_NAME_UZ_LATIN"),
            ],
        ];

        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "times";
                letter-spacing:1px;
            }
            body{
                margin-right: 80px;
                margin-left: 120px;
                font-family: "times";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }

            @media print {
                .pagebreak {
                    clear: both;
                    page-break-after: always;
                }
            }
        </style>';
        $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/uzauto_fb_header.jpg" style="width:100%;">';

        $signer = $document->documentSigners->filter(function ($item) {
            return $item->sequence == 1;
        })->first();
        if ($signer && $signer->status == 1) {
            $img_base64_encoded = 'data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate(config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name));
            $imageContent = file_get_contents($img_base64_encoded);
            $path = storage_path() . '/app' . tempnam(sys_get_temp_dir(), 'qrcode') . '.png';
            file_put_contents($path, $imageContent);
            $img = '<img style="float:right;position:absolute;top:94px;right:85px;" width="90" height="90" src="' . $path . '">';
            // $img = '<td><img width="70" height="70" src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded) . '"></td>';
            $content .= $img;
            //$content .= '<td><img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0,90,169)->format('png')->encoding('UTF-8')->size(200)->generate(config("app.APP_CHECK_EDO_URL").'/#/documents/'.$document->pdf_file_name)) . '"/></td>';
        } elseif ($signer && $signer->status == 2) {
            $content .= '<img style="float:right;position:absolute;top:94px;right:85px;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
        } else {
            $content .= '<img style="float:right;position:absolute;top:94px;right:85px;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
        }


        $document_number = $document->document_number_reg == '' ? $document->document_number : $document->document_number_reg;
        $document_date = $document->document_date_reg == '' ? $document->document_date : $document->document_date_reg;

        // $document_number = substr($document->document_number,0,1) == 2 ? $document->document_number : '';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<table style="width:100%;">';
        $content .= '<tr>';
        $content .= '<td style="font-weight:bold;font-size:18pt;" colspan="3">';
        $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
        $month = $monthList[date('m', strtotime($document_date)) - 1];
        $date = date('Y\y\i\l «d» ', strtotime($document_date)) . $month . ' № ' . $document_number;
        $content .= $date;
        $content .= '</td>';
        $content .= '</tr>';
        if ($attr0) {
            $content .= '<tr>';
            $content .= '<td style="width:40%;font-size:18pt;">';
            $content .= '</td>';
            $content .= '<td style="width:15%;font-size:18pt;">';
            $content .= '</td>';
            $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;">';
            $content .= '<br>';
            $content .= $attr0;
            $content .= '<br>';
            $content .= $attr3;
            $content .= '</td>';
            $content .= '</tr>';
            if ($attr6) {
                $content .= '<tr>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '<br>';
                $content .= $attr6;
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '</tr>';
            }
            if ($attr7) {
                $content .= '<tr>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;padding-top:15px;">';
                // $content .= '<br>';
                $content .= $attr7;
                $content .= '</td>';
                $content .= '</tr>';
            }
            if ($attr8) {
                $content .= '<tr>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;padding-top:15px;">';
                // $content .= '<br>';
                $content .= $attr8;
                $content .= '</td>';
                $content .= '</tr>';
            }
            if ($attr9) {
                $content .= '<tr>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-size:18pt;">';
                $content .= '</td>';
                $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;padding-top:15px;">';
                // $content .= '<br>';
                $content .= $attr9;
                $content .= '</td>';
                $content .= '</tr>';
            }
        }
        $content .= '<table>';

        $content .= '<p style="display:inline-block; margin-top:-8px;">';
        $content .= self::normalizeDocumentContent(self::documentContent($document_id));
        $content .= '</p>';
        $content .= self::getSignerTableUzavtosanoat($document_id, $attr10);

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        $pdf->setOption('images', true);
        // $pdf->setOption('header-html', 'https://edo.uzavtosanoat.uz/header.html');
        if (!in_array($document->document_template_id, [1, 6])) {
            $isp = Employee::find($attr1);
            if ($isp) {
                $locale = $document->locale;
                if ($attr10) {
                    $locale = 'uz_latin';
                }
                $c = $locale == 'uz_latin' ? 1 : 2;
                $isp = $isp['lastname_' . $locale] . ' ' . substr($isp['firstname_' . $locale], 0, $c) . '. ' . substr($isp['middlename_' . $locale], 0, $c) . '.';
            } else {
                $isp = '';
            }
            $data1 = urlencode(str_replace(' ', '_', $isp));
            $data4 = urlencode((str_replace('+', 'plus', str_replace('#', 'rushotka', str_replace(' ', '_', $attr2)))));
            $data5 = $document->document_number;
            $data6 = $attr10 ? 'en' : ($document->locale == 'uz_latin' ? 'la' : ($document->locale == 'uz_cyril' ? 'cy' : 'ru'));

            if ($isp != '') {
                $pdf->setOption('footer-html', 'https://edo.uzavtosanoat.uz/footer.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5 . '&data6=' . $data6);
                $content .= '<div style = "display:block; clear:both; page-break-after:always;"></div>';
            }
        }

        $pdf->setPaper('a4')
            ->setOption('footer-spacing', 0)
            ->setOption('header-spacing', 0)
            ->setOption('margin-top', 23)
            ->setOption('margin-bottom', 34)
            ->setOption('margin-left', 4)
            ->setOption('margin-right', 0)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public static function generatePdfFirmBlankUsf($document_id)
    {
        $document = Document::with('documentDetails.documentDetailContents')
            ->with([
                'documentDetails' => function ($q) {
                    $q->with('documentDetailAttributeValues');
                }
            ])
            ->with([
                'documentSigners' => function ($q) {
                    $q->with('parentEmployee')
                        ->with('signerEmployee')
                        ->with('staff.employees');
                }
            ])
            ->where('id', $document_id)
            ->first();

        $attribute = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
        $attr0 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 && $document->documentDetails[0]->documentDetailAttributeValues[0] ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
        $attr1 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 1 && $document->documentDetails[0]->documentDetailAttributeValues[1] ? $document->documentDetails[0]->documentDetailAttributeValues[1]->attribute_value : "";
        $attr2 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 2 && $document->documentDetails[0]->documentDetailAttributeValues[2] ? $document->documentDetails[0]->documentDetailAttributeValues[2]->attribute_value : "";
        $attr3 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 3 ? $document->documentDetails[0]->documentDetailAttributeValues[3]->attribute_value : "";
        $attr5 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 4 ? $document->documentDetails[0]->documentDetailAttributeValues[4]->attribute_value : "";
        $attr6 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 5 ? $document->documentDetails[0]->documentDetailAttributeValues[5]->attribute_value : "";
        $attr7 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 6 ? $document->documentDetails[0]->documentDetailAttributeValues[6]->attribute_value : "";
        $attr8 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 7 ? $document->documentDetails[0]->documentDetailAttributeValues[7]->attribute_value : "";
        $attr9 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 8 ? $document->documentDetails[0]->documentDetailAttributeValues[8]->attribute_value : "";
        $attr10 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 9 ? $document->documentDetails[0]->documentDetailAttributeValues[9]->attribute_value : "";

        // dd([$attr0,
        // $attr1,
        // $attr2,
        // $attr3,
        // $attr5,
        // $attr6,
        // $attr7,
        // $attr8,
        // $attr9,
        // $attr10,]);


        // if($document->id == 854)
        // {
        //     dd([$attr7,$attr8]);
        // }
        $language = [
            'uzauto' => [
                'ru' => config("app.APP_COMPANY_NAME_RU"),
                'uz_cyril' => config("app.APP_COMPANY_NAME_UZ_CYRIL"),
                'uz_latin' => config("app.APP_COMPANY_NAME_UZ_LATIN"),
            ],
        ];

        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "times";
                letter-spacing:1px;
            }
            body{
                margin-right: 80px;
                margin-left: 120px;
                font-family: "times";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }

            @media print {
                .pagebreak {
                    clear: both;
                    page-break-after: always;
                }
            }
        </style>';
        $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/usf/fb_header_usf.png" style="width:100%;">';

        $signer = $document->documentSigners->filter(function ($item) {
            return $item->sequence == 1;
        })->first();
        if ($signer && $signer->status == 1) {
            $img_base64_encoded = 'data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate(config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name));
            $imageContent = file_get_contents($img_base64_encoded);
            $path = storage_path() . '/app' . tempnam(sys_get_temp_dir(), 'qrcode') . '.png';
            // dd(storage_path() . '/app' . tempnam(sys_get_temp_dir(), 'qrcode') . '.png');
            file_put_contents($path, $imageContent);
            $img = '<img style="float:left;position:absolute;top:94px;left:120px;" width="90" height="90" src="' . $path . '">';
            // $img = '<td><img width="70" height="70" src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded) . '"></td>';
            $content .= $img;
            //$content .= '<td><img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0,90,169)->format('png')->encoding('UTF-8')->size(200)->generate(config("app.APP_CHECK_EDO_URL").'/#/documents/'.$document->pdf_file_name)) . '"/></td>';
        } elseif ($signer && $signer->status == 2) {
            $content .= '<img style="float:left;position:absolute;top:94px;left:120px;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
        } else {
            $content .= '<img style="float:left;position:absolute;top:60px;left:120px;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
        }


        $document_number = $document->document_number_reg == '' ? $document->document_number : $document->document_number_reg;
        $document_date = $document->document_date_reg == '' ? $document->document_date : $document->document_date_reg;

        // $document_number = substr($document->document_number,0,1) == 2 ? $document->document_number : '';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<table style="width:100%;">';
        $content .= '<tr>';
        $content .= '<td style="font-weight:bold;font-size:18pt;" colspan="3">';
        $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
        $month = $monthList[date('m', strtotime($document_date)) - 1];
        $date = '№ ' . $document_number . '<br>' . date('Y\y\i\l «d» ', strtotime($document_date)) . $month;
        $content .= $date;
        $content .= '</td>';
        if ($attr0) {
            $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;">';
            $content .= '<br>';
            $content .= $attr0;
            if ($attr7) {
                $content .= '<br>';
                $content .= $attr7;
            }
            $content .= '<br>';
            $content .= $attr3;
            if ($attr6) {
                $content .= '<br>';
                $content .= $attr6;
            }
            if ($attr8) {
                $content .= '<br>';
                $content .= $attr8;
            }
            if ($attr9) {
                $content .= '<br>';
                $content .= $attr9;
            }
            if ($attr10) {
                $content .= '<br>';
                $content .= $attr10;
            }
            $content .= '</td>';
        }
        $content .= '</tr>';
        $content .= '<table>';

        $content .= '<p style="display:inline-block; margin-top:-8px;">';
        $content .= self::normalizeDocumentContent(self::documentContent($document_id));
        $content .= '</p>';
        $content .= self::getSignerTableUsf($document_id, $attr10);

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        $pdf->setOption('images', true);
        $data1 = '';
        $data4 = '';
        $data5 = '';
        $data6 = '';
        // $pdf->setOption('header-html', 'https://edo.uzavtosanoat.uz/header.html');
        //  if (!in_array($document->document_template_id, [1, 6]))
        {
            $isp = Employee::find($attr1);
            if ($isp) {
                $locale = $document->locale;
                if ($attr5) {
                    $locale = 'uz_latin';
                }
                $c = $locale == 'uz_latin' ? 1 : 2;
                $isp = $isp['lastname_' . $locale] . ' ' . substr($isp['firstname_' . $locale], 0, $c) . '. ' . substr($isp['middlename_' . $locale], 0, $c) . '.';
            } else {
                $isp = '';
            }
            $data1 = urlencode(str_replace(' ', '_', $isp));
            $data4 = (str_replace('+', 'plus', str_replace('#', 'rushotka', str_replace(' ', '_', $attr2))));
            $data4 = urlencode(str_replace(':', 'ikkinuqta', str_replace('@', 'kuchuk', $data4)));
            $data5 = $document->document_number;
            $data6 = $attr10 ? 1 : 0;

            if ($isp != '') {
                $pdf->setOption('footer-html', 'https://edo.usf.uz/footer.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5 . '&data6=' . $data6);
                $content .= '<div style = "display:block; clear:both; page-break-after:always;"></div>';
            } elseif (in_array($document->document_template_id, [58])) {
                $pdf->setOption('footer-html', 'https://edo.usf.uz/footer.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5 . '&data6=' . $data6);
            }
        }

        $pdf->setPaper('a4')
            ->setOption('footer-spacing', 0)
            ->setOption('header-spacing', 0)
            ->setOption('margin-top', 23)
            ->setOption('margin-bottom', 34)
            ->setOption('margin-left', 4)
            ->setOption('margin-right', 0)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public static function generatePdfFirmBlankGm($document_id, $withComment = false)
    {
        // dd(1);
        $document = Document::with('documentDetails.documentDetailContents')
            ->with([
                'documentDetails' => function ($q) {
                    $q->with([
                        'documentDetailAttributeValues' => function ($q) {
                            $q->orderBy('id', 'asc');
                        }
                    ]);
                }
            ])
            ->with([
                'documentSigners' => function ($q) {
                    $q->with('parentEmployee')
                        ->with('signerEmployee')
                        ->with('staff.employees');
                }
            ])
            ->where('id', $document_id)
            ->first();

        $attribute = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
        $attr0 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 0 && $document->documentDetails[0]->documentDetailAttributeValues[0] ? $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value : "";
        $attr1 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 1 && $document->documentDetails[0]->documentDetailAttributeValues[1] ? $document->documentDetails[0]->documentDetailAttributeValues[1]->attribute_value : "";
        $attr2 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 2 && $document->documentDetails[0]->documentDetailAttributeValues[2] ? $document->documentDetails[0]->documentDetailAttributeValues[2]->attribute_value : "";
        $attr3 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 3 ? $document->documentDetails[0]->documentDetailAttributeValues[3]->attribute_value : "";
        $attr5 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 4 ? $document->documentDetails[0]->documentDetailAttributeValues[4]->attribute_value : "";
        $attr6 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 5 ? $document->documentDetails[0]->documentDetailAttributeValues[5]->attribute_value : "";
        $attr7 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 6 ? $document->documentDetails[0]->documentDetailAttributeValues[6]->attribute_value : "";
        $attr8 = ''; //$document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 7  ? $document->documentDetails[0]->documentDetailAttributeValues[7]->attribute_value : "";
        $attr9 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 8 ? $document->documentDetails[0]->documentDetailAttributeValues[8]->attribute_value : "";
        $attr10 = $document->documentDetails[0] && $document->documentDetails[0]->documentDetailAttributeValues->count() > 9 ? $document->documentDetails[0]->documentDetailAttributeValues[9]->attribute_value : "";

        $language = [
            'uzauto' => [
                'ru' => config("app.APP_COMPANY_NAME_RU"),
                'uz_cyril' => config("app.APP_COMPANY_NAME_UZ_CYRIL"),
                'uz_latin' => config("app.APP_COMPANY_NAME_UZ_LATIN"),
            ],
        ];

        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "times";
                letter-spacing:1px;
            }
            // body{
            //     margin-right: 80px;
            //     margin-left: 120px;
            //     font-family: "times";
            //     letter-spacing:1px;
            //     // text-rendering: auto;
            //     // text-rendering: optimizeSpeed;
            //     // text-rendering: optimizeLegibility;
            //     text-rendering: geometricPrecision;
            // }

            @media print {
                .pagebreak {
                    clear: both;
                    page-break-after: always;
                }
            }
        </style>';
        // $content .= '<img src="' . config('app.APP_EDO_URL') . '/img/uzauto_fb_header.jpg" style="width:100%;">';
        $content .= "<div style='width:100%;height:160px;'></div>";
        $signer = $document->documentSigners->filter(function ($item) {
            return $item->sequence == 1;
        })->first();
        if ($signer && $signer->status == 1) {
            $img_base64_encoded = 'data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(200)->generate(config("app.APP_CHECK_EDO_URL") . '/#/documents/' . $document->pdf_file_name));
            $imageContent = file_get_contents($img_base64_encoded);
            $path = storage_path() . '/app' . tempnam(sys_get_temp_dir(), 'qrcode') . '.png';
            file_put_contents($path, $imageContent);
            $img = '<img style="float:right;position:absolute;top:140px;" width="90" height="90" src="' . $path . '">';
            // $img = '<td><img width="70" height="70" src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded) . '"></td>';
            $content .= $img;
            //$content .= '<td><img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0,90,169)->format('png')->encoding('UTF-8')->size(200)->generate(config("app.APP_CHECK_EDO_URL").'/#/documents/'.$document->pdf_file_name)) . '"/></td>';
        } elseif ($signer && $signer->status == 2) {
            $content .= '<img style="float:right;position:absolute;top:140px;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop-cancel.png" width="80" height="80" />';
        } else {
            $content .= '<img style="float:right;position:absolute;top:140px;" width="90" height="90" src="' . config('app.APP_EDO_URL') . '/img/crop.png" width="80" height="80" />';
        }

        $document_number = $document->document_number_reg == '' ? $document->document_number : $document->document_number_reg;
        $document_date = $document->document_date_reg == '' ? $document->document_date : $document->document_date_reg;

        // $document_number = substr($document->document_number,0,1) == 2 ? $document->document_number : '';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<table style="width:100%;">';
        if ($document->document_template_id == 326) {
            if ($document->locale == 'ru') {
                $content .= '<tr>';
                $content .= '<td colspan="3"></td>';
                $content .= '<td colspan="2">ДСП</td>';
                $content .= '</tr>';
                $content .= '<tr>';
                $content .= '<td colspan="3"></td>';
                $content .= '<td colspan="2">Экз.№___________</td>';
                $content .= '</tr>';
            } elseif ($document->locale == 'uz_latin') {
                $content .= '<tr>';
                $content .= '<td colspan="3"></td>';
                $content .= '<td colspan="2">XDFU</td>';
                $content .= '</tr>';
                $content .= '<tr>';
                $content .= '<td colspan="3"></td>';
                $content .= '<td colspan="2">Nus.№___________</td>';
                $content .= '</tr>';
            } else {
                $content .= '<tr>';
                $content .= '<td colspan="3"></td>';
                $content .= '<td colspan="2">ХДФУ</td>';
                $content .= '</tr>';
                $content .= '<tr>';
                $content .= '<td colspan="3"></td>';
                $content .= '<td colspan="2">Нус.№____________</td>';
                $content .= '</tr>';
            }
        }
        $content .= '<tr>';
        $content .= '<td style="font-weight:bold;font-size:18pt;" colspan="3">';
        $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
        $month = $monthList[date('m', strtotime($document_date)) - 1];
        $date = '№ ' . $document_number . '<br>' . date('Y \y\i\l «d» ', strtotime($document_date)) . $month;
        $content .= $date;
        $content .= '</td>';
        if ($attr0) {
            $content .= '<td style="font-weight:bold;width:45%;font-size:18pt;">';
            $content .= '<br>';
            $content .= $attr0;
            if ($attr7) {
                $content .= '<br>';
                $content .= $attr7;
            }
            $content .= '<br>';
            $content .= $attr3; //Manzil yoki elektron pochta
            if ($attr6) {
                $content .= '<br>';
                $content .= $attr6;
            }
            if ($attr8) {
                $content .= '<br>';
                $content .= $attr8;
            }
            if ($attr9) {
                $content .= '<br>';
                $content .= $attr9;
            }
            if ($attr10) {
                $content .= '<br>';
                $content .= $attr10;
            }
            $content .= '</td>';
        }
        $content .= '</tr>';
        $content .= '<table>';

        $content .= '<p style="display:inline-block; margin-top:-8px;">';
        $content .= self::normalizeDocumentContent(self::documentContent($document_id));
        $content .= '</p>';
        $content .= self::getSignerTable($document_id, $attr5);

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        $pdf->setOption('images', true);
        $data1 = '';
        $data4 = '';
        $data5 = '';
        $data6 = '';
        // $pdf->setOption('header-html', 'https://edo.uzavtosanoat.uz/header.html');
        // if (!in_array($document->document_template_id, [1, 6]))
        {
            // array:10 [
            //      0 => "Uzkoram"
            // 1 => "2"
            // 2 => "3073"
            // 3 => "Komilov"
            // 4 => "english"
            // 5 => "addres"
            //     6 => ""
            //     7 => ""
            //     8 => ""
            //     9 => ""
            //   ]
            $isp = Employee::find($attr1);
            if ($isp) {
                $locale = $document->locale;
                if ($attr5) {
                    $locale = 'uz_latin';
                }
                $c = $locale == 'uz_latin' ? 1 : 2;
                $isp = $isp['lastname_' . $locale] . ' ' . substr($isp['firstname_' . $locale], 0, $c) . '. ' . substr($isp['middlename_' . $locale], 0, $c) . '.';
            } else {
                $isp = '';
            }
            $data1 = urlencode(str_replace(' ', '_', $isp));
            $data4 = (str_replace('+', 'plus', str_replace('#', 'rushotka', str_replace(' ', '_', $attr2))));
            $data4 = urlencode(str_replace(':', 'ikkinuqta', str_replace('@', 'kuchuk', $data4)));
            $data5 = $document->document_number;
            $data6 = $attr5 ? 'en' : ($document->locale == 'uz_latin' ? 'la' : ($document->locale == 'uz_cyril' ? 'cy' : 'ru'));

            if ($isp != '') {
                // $pdf->setOption('footer-html', 'https://edo.uzavtosanoat.uz/footer_fb.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5 . '&data6=' . $data6);
                $content .= '<div style = "display:block; clear:both; page-break-after:always;"></div>';
            }
        }

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        $pdf->setOption('images', true);
        // $pdf->setOption('header-html', 'https://edo.uzavtosanoat.uz/header.html');

        if ($document->document_template_id == 157) {
            $content = "<body style='background: url(https://edo.uzautomotors.com/img/gm/bck_fb.jpg);
                                    background-size: 990 1201;
                                    background-position: left top;
                                    background-repeat: no-repeat;
                                    padding-left:60px;padding-right:60px;'>" . $content;
        } elseif ($document->document_template_id == 310) {
            $content = "<body style='background: url(https://edo.uzautomotors.com/img/gm/bck_fb_xorazm.jpg);
                                    background-size: 990 1201;
                                    background-position: left top;
                                    background-repeat: no-repeat;
                                    padding-left:60px;padding-right:60px;'>" . $content;
        } else {
            $content = "<body style='background: url(https://edo.uzautomotors.com/img/gm/bck_fb_tas.jpg);
                                    background-size: 990 1201;
                                    background-position: left top;
                                    background-repeat: no-repeat;
                                    padding-left:60px;padding-right:60px;'>" . $content;
        }

        // $content .="<img style='width:250px; margin-left:150px;margin-top:50px;' src='https://edo.uzautomotors.com/img/gm/uzautomotors.jpg'>";
        // $content .="<img style='width:250px; margin-left:150px;margin-top:50px;' src='https://edo.uzautomotors.com/img/gm/address.jpg'>";

        $content .= "</body>";

        $pdf->setOption('header-html', 'https://edo.uzautomotors.com/header_fb.html?data1=' . 'a' . '&data4=' . 'b' . '&data5=' . 'c' . '&data6=' . 'd');
        if ($document->document_template_id == 310) {
            $pdf->setOption('footer-html', 'https://edo.uzautomotors.com/footer_fb_xorazm.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5 . '&data6=' . $data6);
        } else {
            $pdf->setOption('footer-html', 'https://edo.uzautomotors.com/footer_fb.html?data1=' . $data1 . '&data4=' . $data4 . '&data5=' . $data5 . '&data6=' . $data6);
        }

        // $pdf->setDefaultHeader('65464646');
        $pdf->setPaper('a4')
            ->setOption('footer-spacing', 0)
            ->setOption('header-spacing', 0)
            ->setOption('footer-font-name', 'times')
            ->setOption('footer-font-size', '10')
            // ->setOption('footer-right', '[page] / [topage]     ')
            ->setOption('margin-top', 18)
            ->setOption('margin-bottom', 25)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $filename = public_path('temp/' . (microtime(true) * 10000) . rand(0, 10000) . '.pdf');
            $pdf->save($filename);
            $base64 = base64_encode(file_get_contents($filename));
            unlink($filename);
            return $base64;
            // $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public static function generatePdfFirmBlankGm1($document_id)
    {
        $document = Document::with('documentDetails.documentDetailContents')
            ->with([
                'documentDetails' => function ($q) {
                    $q->with('documentDetailAttributeValues');
                }
            ])
            ->with([
                'documentSigners' => function ($q) {
                    $q->with('parentEmployee')
                        ->with('signerEmployee')
                        ->with('staff.employees');
                }
            ])
            ->where('id', $document_id)
            ->first();

        $pdf = App::make('snappy.pdf.wrapper');
        if (!$document->documentTemplate->is_pdf_portrait) {
            $pdf->setOrientation('landscape');
        }
        $pdf->setOption('images', true);
        // $pdf->setOption('header-html', 'https://edo.uzavtosanoat.uz/header.html');

        $content = "";

        $content = "<body style='position:absolute;background: url(https://edo.uzautomotors.com/img/gm/bck_fb.jpg);
                                    background-size: 990 1422;
                                    background-position: left center;
                                    background-repeat: no-repeat;
                                    padding-left:60px;padding-right:40px;'>";

        // $content .="<img style='width:250px; margin-left:150px;margin-top:50px;' src='https://edo.uzautomotors.com/img/gm/uzautomotors.jpg'>";
        // $content .="<img style='width:250px; margin-left:150px;margin-top:50px;' src='https://edo.uzautomotors.com/img/gm/address.jpg'>";

        $content .= "</body>";

        $pdf->setOption('header-html', 'https://edo.uzautomotors.com/header_fb.html?data1=' . 'a' . '&data4=' . 'b' . '&data5=' . 'c' . '&data6=' . 'd');
        $pdf->setOption('footer-html', 'https://edo.uzautomotors.com/footer_fb.html?data1=' . 'a' . '&data4=' . 'b' . '&data5=' . 'c' . '&data6=' . 'd');

        // $pdf->setDefaultHeader('65464646');
        $pdf->setPaper('a4')
            ->setOption('footer-spacing', 0)
            ->setOption('header-spacing', 0)
            ->setOption('margin-top', 18)
            ->setOption('margin-bottom', 23)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public static function savePdf($id, $withComment = false)
    {
        DB::beginTransaction();
        try {
            $document = self::find($id);
            if (config("app.APP_COMPANY_ID") == 1) {
                if ($id > 91318 && ($document->document_template_id == 157 || $document->document_template_id == 158 || $document->document_template_id == 264 || $document->document_template_id == 310 || $document->document_template_id == 326 || $document->document_template_id == 544)) {
                    $pdfBase64 = Document::generatePdfFirmBlankGm($document->id, $withComment);
                } else {
                    $pdfBase64 = $document->document_template_id == 157 || $document->document_template_id == 158 || $document->document_template_id == 264 ? Document::generatePdfFirmBlankGm($document->id, $withComment) : Document::generatePdf($document->id, $withComment);
                }
                if ($withComment) {
                    return $pdfBase64;
                }
            } elseif (config("app.APP_COMPANY_ID") == 2) {
                // $pdfBase64 = $document->document_template_id == 2 || $document->document_template_id == 58 ? Document::generatePdfFirmBlankUsf($document->id) : Document::generatePdfUsf($document->id);
                $pdfBase64 = $document->document_template_id == 2 || $document->document_template_id == 58 ? Usf::generatePdfFirmBlank($document->id) : Usf::generatePdf($document->id);
            } elseif (config("app.APP_COMPANY_ID") == 3) {
                $pdfBase64 = ($document->documentType->code == 'FB' || in_array($document->document_template_id, [11, 53])) ? Document::generatePdfFirmBlankUzavtosanoat($document->id) : Document::generatePdfUzavtosanoat($document->id);
            } elseif (config("app.APP_COMPANY_ID") == 4) {
                $pdfBase64 = $document->document_template_id == 2 || $document->document_template_id == 58 ? Vw::generatePdfFirmBlank($document->id) : Vw::generatePdf($document->id);
            } elseif (config("app.APP_COMPANY_ID") == 5) {
                $pdfBase64 = $document->document_template_id == 2 || $document->document_template_id == 58 ? Invest::generatePdfFirmBlank($document->id) : Invest::generatePdf($document->id);
            }
            if ($document->pdf_table) {
                if ($document->id) {
                    DB::connection('workflow_pdf')
                        ->table($document->pdf_table)
                        ->where('document_id', $document->id)
                        ->update(['pdfBase64' => $pdfBase64]);
                }
            } else {
                $lastPdfTable = LastPdfTable::latest('id')->first();
                if ($lastPdfTable) {
                    $id = $lastPdfTable->id;
                } else {
                    $id = LastPdfTable::insertGetId(['name' => 'pdf']);
                }
                $table_name = '';
                if (Schema::connection('workflow_pdf')->hasTable('pdf' . $id) && DB::connection('workflow_pdf')->select('select count(id) count from pdf' . $id)[0]->count < 5000) {
                    $table_name = 'pdf' . $id;
                } else {
                    $id = LastPdfTable::insertGetId(['name' => 'pdf']);
                    Schema::connection('workflow_pdf')->create('pdf' . $id, function ($table) {
                        $table->increments('id');
                        $table->unsignedInteger('document_id');
                        $table->longText('pdfBase64');
                        $table->longText('eimzoBase64')->nullable();
                        $table->string('name', 10)->default('pdf');
                        $table->index('document_id');
                    });
                    $table_name = 'pdf' . $id;
                }
                DB::connection('workflow_pdf')->table('pdf' . $id)->insert([
                    'document_id' => $document->id,
                    'pdfBase64' => $pdfBase64,
                    // 'eimzoBase64' => $document->base641,
                ]);
                $document->pdf_table = 'pdf' . $id;
                $document->save();
            }
            DB::commit();
            // $document = Document::find($id);
            return $document->pdf_table;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public static function normalizeDocumentContent($content)
    {
        $dom = new DOMDocument();
        $content = str_replace('font-size: 18pt;', 'font-size: 22pt;', $content);
        $content = str_replace('font-size: 16pt;', 'font-size: 20pt;', $content);
        $content = str_replace('font-size: 14pt;', 'font-size: 18pt;', $content);
        $content = str_replace('font-size: 12pt;', 'font-size: 16pt;', $content);
        try {
            $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        } catch (\Throwable $th) {
            // throw $th;
            foreach (libxml_get_errors() as $error) {
                // handle errors here
            }

            libxml_clear_errors();
        }
        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $table) {
            $table->removeAttribute('style');
            $table->setAttribute('style', 'width: 100%; border-collapse: collapse;margin-top:15px;');

            $tds = $table->getElementsByTagName('td');
            foreach ($tds as $td) {
                $style = $td->getAttribute('style');
                $td->removeAttribute('style');
                if ($table->getAttribute('border') == 1) {
                    $td->setAttribute('style', $style . 'border:1px solid #aaa !important; padding:5px;');
                } else {
                    $td->setAttribute('style', $style . 'border:0px; !important; padding:5px;');
                }
            }

            $ths = $table->getElementsByTagName('th');
            foreach ($ths as $th) {
                $style = $th->getAttribute('style');
                $th->removeAttribute('style');
                $th->setAttribute('style', $style . 'border:1px solid #aaa !important; padding:5px;');
            }
        }
        return $dom->saveHTML();
    }

    public static function normalizeDocumentContentFB($content)
    {
        // return $content;
        $dom = new DOMDocument();
        // $content = str_replace('&', 'and', $content);
        try {
            $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        } catch (\Throwable $th) {
            // dd($content);
            // throw $th;
            foreach (libxml_get_errors() as $error) {
                // handle errors here
            }

            libxml_clear_errors();
        }
        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $table) {
            $table->removeAttribute('style');
            $table->setAttribute('style', 'width: 100%; font-size:12px;');

            $tds = $table->getElementsByTagName('td');
            foreach ($tds as $td) {
                $style = $td->getAttribute('style');
                $td->removeAttribute('style');
                // $td->setAttribute('style', $style . 'border:1px solid #aaa !important;');
            }

            $ths = $table->getElementsByTagName('th');
            foreach ($ths as $th) {
                $style = $th->getAttribute('style');
                $th->removeAttribute('style');
                // $th->setAttribute('style', $style . 'border:1px solid #aaa !important;');
            }
        }
        return $dom->saveHTML();
    }

    public static function documentContent($id)
    {
        $document = Document::with('documentType')->find($id);

        $document_template = DocumentTemplate::find($document->document_template_id);
        $colum_locale = [

            'purchase_catalogs' => [
                'code' => [
                    'ru' => 'Код',
                    'uz_cyril' => 'Коди',
                    'uz_latin' => 'Kodi',
                ],
                'name_eng' => [
                    'ru' => 'Название на английском языке',
                    'uz_cyril' => 'Номи инглиз тилида',
                    'uz_latin' => 'Nomi ingliz tilida',
                ],
                'name_ru' => [
                    'ru' => 'Название на русском языке',
                    'uz_cyril' => 'Номи рус тилида',
                    'uz_latin' => 'Nomi rus tilida',
                ],
                'specification' => [
                    'ru' => 'Спецификация',
                    'uz_cyril' => 'Спецификацияси',
                    'uz_latin' => 'Spetsifikatsiyasi',
                ],
                'manufacturer' => [
                    'ru' => 'Производитель',
                    'uz_cyril' => 'Ишлаб чиқарувчи',
                    'uz_latin' => 'Ishlab chiqaruvchi',
                ],
            ],
            'partners' => [
                'name' => [
                    'ru' => 'Название поставщика',
                    'uz_cyril' => 'Номи',
                    'uz_latin' => 'Nomi',
                ],
                'adress' => [
                    'ru' => 'Адрес поставщика',
                    'uz_cyril' => 'Манзил',
                    'uz_latin' => 'Manzil',
                ],
                'bank_name' => [
                    'ru' => 'Название банка поставщика',
                    'uz_cyril' => 'Банк номи',
                    'uz_latin' => 'Bank nomi',
                ],
                'account' => [
                    'ru' => 'Рассчётный счёт поставщика',
                    'uz_cyril' => 'Ҳисоб рақами',
                    'uz_latin' => 'Hisob raqami',
                ],
                'swift_code' => [
                    'ru' => 'SWIFT код',
                    'uz_cyril' => 'SWIFT коди',
                    'uz_latin' => 'SWIFT kodi',
                ],
                'inn' => [
                    'ru' => 'Номер ИНН',
                    'uz_cyril' => 'СТИР',
                    'uz_latin' => 'STIR',
                ],
                'mfo' => [
                    'ru' => 'МФО',
                    'uz_cyril' => 'МФО',
                    'uz_latin' => 'MFO',
                ],
            ],
            'company_requisites' => [
                'name' => [
                    'ru' => 'Наименование',
                    'uz_cyril' => 'Номи',
                    'uz_latin' => 'Nomi',
                ],
                'address' => [
                    'ru' => 'Адрес',
                    'uz_cyril' => 'Манзил',
                    'uz_latin' => 'Manzil',
                ],
                'account' => [
                    'ru' => 'Рассчётный счёт',
                    'uz_cyril' => 'Ҳисоб рақами',
                    'uz_latin' => 'Hisob raqami',
                ],
                'inn' => [
                    'ru' => 'Номер ИНН',
                    'uz_cyril' => 'СТИР',
                    'uz_latin' => 'STIR',
                ],
                'mfo' => [
                    'ru' => 'МФО',
                    'uz_cyril' => 'МФО',
                    'uz_latin' => 'MFO',
                ],
                'swift' => [
                    'ru' => 'SWIFT код',
                    'uz_cyril' => 'SWIFT коди',
                    'uz_latin' => 'SWIFT kodi',
                ],
                'oknh' => [
                    'ru' => 'ОКНХ код',
                    'uz_cyril' => 'ОКНХ коди',
                    'uz_latin' => 'OKNH kodi',
                ],
            ],
        ];

        $otgul_locales = [
            'ru' => [
                'Отработанных сверхурочных часов',
                'Время начала',
                'Время окончания',
                ' час',
                'Час',
            ],
            'uz_latin' => [
                "Qo'shimcha ish soatlar",
                'Boshlanish vaqti',
                'Tugash vaqti',
                ' soat',
                'Soat',
            ],
            'uz_cyril' => [
                'Қўшимча иш соатлар',
                'Бошланиш вақти',
                'Тугаш вақти',
                ' соат',
                'Соат'
            ],
        ];
        // if ($document_template->is_from_to_department_show == 1) {
        //     $content = '<p style="text-align: center; font-size: 16pt; width:100%;margin-bottom: 0px;">' . $document->documentTemplate['name_' . $document->locale] . '</p>';
        // } else {
        // }
        $content = '';
        // if($document->document_template_id == 622){

        //     $content .= '<p>';
        //     $content .= 'FIO';
        //     $content .= '</p>';
        // }
        // dd($document->documentType->code);
        if ($document->documentType->code == 'FB' || $document->documentTemplate->is_attribute_show == 0) {
            return $content .= ($document->documentDetails?$document->documentDetails[0]->content != 'null' ? $document->documentDetails[0]->content : '': '');
        }

        $table = '';
        // if (($document_template->has_employee || $document_template->change_staff)) {
        if (($document_template->has_employee || $document_template->change_staff) && !in_array($document->document_template_id, [615, 619, 622]) || $document->document_template_id == 634) {
            $emp = 1;
            foreach ($document->documentDetails as $key => $documentDetail) {
                $content .= $documentDetail->content != 'null' ? $documentDetail->content : '';
                $table = '<table border="1">';
                $table .= '<thead><tr><th>';
                $table .= '№</th><th>';
                $table .= $document->locale == 'ru' ? 'Табелный номер' : ($document->locale == 'uz_cyril' ? 'Табел рақами' : 'Tabel raqami');
                $table .= '</th><th>';
                $table .= $document->locale == 'ru' ? 'Ф.И.О' : ($document->locale == 'uz_cyril' ? 'Ф.И.О' : 'F.I.O');
                $table .= '</th><th>';
                $table .= $document->locale == 'ru' ? 'Должность' : ($document->locale == 'uz_cyril' ? 'Лавозими' : 'Lavozimi');
                $table .= '</th><th>';
                $table .= $document->locale == 'ru' ? 'Код подразделение' : ($document->locale == 'uz_cyril' ? 'Бўлим коди' : 'Bo\'lim kodi');
                $table .= '</th>';
                $table .= '</th><th>';
                $table .= $document->locale == 'ru' ? 'Подразделение' : ($document->locale == 'uz_cyril' ? 'Бўлими' : 'Bo\'limi');
                $table .= '</th>';
                if ($document->document_template_id == 634) {
                    $table .= '<th>';
                    $table .= $document->locale == 'ru' ? 'Категория' : ($document->locale == 'uz_cyril' ? 'Категорияси' : 'Kategoriyasi');
                    $table .= '</th>';
                }

                if ($document_template->change_staff) {
                    // if ($document_template->change_staff && $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value != 523) {
                    if ($document->document_template_id != 635) {
                        $table .= '<th>';
                        $table .= $document->locale == 'ru' ? 'Категория' : ($document->locale == 'uz_cyril' ? 'Категорияси' : 'Kategoriyasi');
                        $table .= '</th>';
                        $table .= '<th>';
                        $table .= $document->locale == 'ru' ? 'Коэффициент' : ($document->locale == 'uz_cyril' ? 'Устама(лари)' : 'Ustama(lari)*');
                        $table .= '</th>';
                    }
                }

                if ($document->document_template_id == 587) {
                    $document_detail_conts = DocumentDetailContent::where('document_detail_id', $documentDetail->id)
                        // ->orderBy('id','asc')
                        ->get();
                    foreach ($document_detail_conts as $key => $detail_content) {
                        // $table .= '<tr><td colspan="' . $cols . '" style="width: 40%;text-align:left;font-weight: bold;">';
                        $table .= '<th>';
                        $table .= $detail_content->attribute_name;
                        $table .= '</th>';
                        // $table .= $detail_content->value;
                        // $table .= '</td></tr>';
                    }
                } else if (in_array($document->document_template_id, [636, 635, 634])) {
                    if ($document->document_template_id == 635) {
                        $table .= '<th>';
                        if (!isset($document->documentDetails[0]->documentDetailAttributeValues[0]) || $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value != 523) {
                            $table .= $document->locale == 'ru' ? 'Новые установленные коэффициенты' : ($document->locale == 'uz_cyril' ? 'Янги ўрнатилган устама(лари)' : 'Yangi o`rnatilgan ustama(lari)');
                        } else {
                            $table .= $document->locale == 'ru' ? 'Отменяется коэффициенты' : ($document->locale == 'uz_cyril' ? 'Бекор килинаётган устама(лари)' : 'Bekor qilinayotgan ustama(lari)');
                        }
                        $table .= '</th>';
                    }
                    if ($document->document_template_id == 634) {
                        $table .= '<th>';
                        $table .= $document->locale == 'ru' ? 'Новые установленные категории' : ($document->locale == 'uz_cyril' ? 'Янги ўрнатилган категория' : 'Yangi kategoriyasi');
                        $table .= '</th>';
                    }
                    if ($document->document_template_id == 636) {
                        $document_detail_contents = DocumentDetailContent::where('document_detail_id', $documentDetail->id)->orderBy('d_d_attribute_id')->get();
                        $table .= '<th>';
                        $table .= $document->locale == 'ru' ? 'Диапазон категории' : ($document->locale == 'uz_cyril' ? 'Категория диапазони' : 'Kategoriya diapazoni');
                        $table .= '</th>';
                        $table .= '<th>';
                        $table .= $document->locale == 'ru' ? 'Категория' : ($document->locale == 'uz_cyril' ? 'Категорияси' : 'Kategoriyasi');
                        $table .= '</th>';
                        foreach ($document_detail_contents as $key => $detail_content) {
                            $dda = DocumentDetailAttribute::find($detail_content->d_d_attribute_id);

                            $table .= '<th>';
                            $table .= $detail_content->attribute_name;
                            $table .= '</th>';
                        }
                        $documentDetailSignerAttributes = DocumentDetailSignerAttribute::where('document_detail_id', $documentDetail->id)
                            ->whereHas('documentDetailAttributes', function ($q) {
                                $q->where('is_active', 1);
                            })
                            ->get();
                        foreach ($documentDetailSignerAttributes as $key => $ddsa) {
                            if ($ddsa->d_d_attribute_id == 2900) {

                                $table .= '<th>';
                                $table .= $ddsa->documentDetailAttributes['attribute_name_' . $document->locale];
                                $table .= '</th>';
                            }
                        }
                    }
                } else {
                    $table .= '<th>';
                    $table .= $document->locale == 'ru' ? 'ИНН' : ($document->locale == 'uz_cyril' ? 'СТИР' : 'STIR');
                    $table .= '</th>';
                }
                $table .= '</tr></thead><tbody>';

                $detailEmployees = DocumentDetailEmployee::where('document_detail_id', $documentDetail->id)->get();
                foreach ($detailEmployees as $key => $detailEmployee) {
                    $table .= '<tr style="text-align:center"><td>' . $emp++ . '</td>';
                    $table .= '<td>' . ($detailEmployee->employee ? $detailEmployee->employee->tabel : '') . '</td>';
                    $table .= '<td>' . $detailEmployee->employee_fio . '</td>';
                    $table .= '<td>' . $detailEmployee->employee_position . '</td>';
                    $table .= '<td>' . $detailEmployee->employee_department_code . '</td>';
                    $table .= '<td>' . $detailEmployee->employee_department . '</td>';
                    if ($document_template->change_staff) {
                        // if ($document_template->change_staff && $document->documentDetails[0]->documentDetailAttributeValues[0]->attribute_value != 523) {
                        // dd($detailEmployees[0]);
                        if ($document->document_template_id != 635) {
                            $table .= '<td>' . ($detailEmployees[0]->tariff_scale_id ? $detailEmployees[0]->tariffScale->category : '') . '</td>';
                            $table .= '<td>';
                            $coefficients = DocumentDetailCoefficient::where('document_detail_id', $documentDetail->id)->where('type', 0)->get();
                            foreach ($coefficients as $ckey => $cvalue) {
                                // $table .= $cvalue->tariffScale['description_'.$document->locale];
                                $table .= $cvalue->value . '% ' . $cvalue->tariffScale['description_' . $document->locale] . '<br>';
                            }
                            $table .= '</td>';
                        }
                    }
                    if ($document->document_template_id == 587) {
                        $document_detail_conts = DocumentDetailContent::where('document_detail_id', $documentDetail->id)
                            ->get();
                        foreach ($document_detail_conts as $key => $detail_content) {
                            // $table .= '<tr><td colspan="' . $cols . '" style="width: 40%;text-align:right;font-weight: bold;">';
                            $table .= '<td>';
                            $table .= $detail_content->value;
                            $table .= '</td>';
                            // $table .= $detail_content->value;
                            // $table .= '</td></tr>';
                        }
                    } else if (in_array($document->document_template_id, [636, 635, 634])) {
                        if ($document->document_template_id == 635) {
                            $coefficients = DocumentDetailCoefficient::where('document_detail_id', $documentDetail->id)->where('type', 1)->get();
                            $table .= '<td>';
                            foreach ($coefficients as $ckey => $cvalue) {
                                // $table .= $cvalue->tariffScale['description_'.$document->locale];
                                $table .= $cvalue->value . '% ' . $cvalue->tariffScale['description_' . $document->locale] . '<br>';
                            }
                            $table .= '</td>';
                        }
                        if ($document->document_template_id == 634) {
                            $table .= '<td>' . ($detailEmployees[0]->tariff_scale_id ? $detailEmployees[0]->tariffScale->category : '') . '</td>';
                            $table .= '<td>' . ($documentDetail->documentDetailContents[0]->value) . '</td>';
                        }
                        if ($document->document_template_id == 636) {
                            $document_detail_conts = DocumentDetailContent::where('document_detail_id', $documentDetail->id)
                                ->get();
                            $emp_id = $document->documentDetails[0]->documentDetailEmployees[0]->employee_id;
                            $staff = Staff::whereHas('employees', function ($q) use ($emp_id) {
                                $q->where('employees.id', $emp_id);
                            })
                                ->first();

                            $table .= '<td>';
                            $table .= $staff->range->code;
                            $table .= '</td>';
                            $table .= '<td>' . ($detailEmployees[0]->tariff_scale_id ? $detailEmployees[0]->tariffScale->category : '') . '</td>';
                            foreach ($document_detail_conts as $key => $detail_content) {
                                $dda = DocumentDetailAttribute::find($detail_content->d_d_attribute_id);
                                $table .= '<td>';
                                $table .= $detail_content->value ? $detail_content->value : '';
                                $table .= '</td>';
                            }
                            $documentDetailSignerAttributes = DocumentDetailSignerAttribute::where('document_detail_id', $documentDetail->id)
                                ->whereHas('documentDetailAttributes', function ($q) {
                                    $q->where('is_active', 1);
                                })
                                ->get();
                            foreach ($documentDetailSignerAttributes as $key => $ddsa) {
                                if ($ddsa->d_d_attribute_id == 2900) {

                                    $table .= '<th>';
                                    $ts = TariffScale::find($ddsa->value);
                                    $table .= $ts ? $ts->category : '';
                                    $table .= '</th>';
                                }
                            }
                        }
                    } else {
                        $table .= '<td>' . ($detailEmployee->employee ? $detailEmployee->employee->INN : '') . '</td>';
                    }
                    $table .= '</tr>';
                }


                $table .= '</tbody></table>';

                if ($document->document_template_id == 597) {
                    // $detailEmployees
                    $otgulDates = OtgulDate::where('document_detail_employee_id', $detailEmployees[0]->id)->get();
                    $table .= '<table border="1">';
                    $table .= '<thead>';
                    $table .= '<tr>';
                    $table .= '<th colspan="4">' . $otgul_locales[$document->locale][0] . '</th>';
                    $table .= '</tr>';
                    $table .= '<tr>';
                    $table .= '<th>#</th>';
                    $table .= '<th>' . $otgul_locales[$document->locale][1] . '</th>';
                    $table .= '<th>' . $otgul_locales[$document->locale][2] . '</th>';
                    $table .= '<th>' . $otgul_locales[$document->locale][4] . '</th>';
                    $table .= '</tr>';
                    $table .= '</thead>';
                    $table .= '<tbody>';

                    foreach ($otgulDates as $k => $v) {
                        $reduced_lanch = 0;
                        $start_lanch_date = substr($v->start_date, 0, 10) . ' 12:00:00';
                        $end_lanch_date = substr($v->end_date, 0, 10) . ' 13:00:00';
                        $start_night_lanch_date = substr($v->end_date, 0, 10) . ' 00:00:00';
                        $end_night_lanch_date = substr($v->end_date, 0, 10) . ' 01:00:00';
                        if (
                            $v->start_date <= $start_lanch_date && $end_lanch_date <= $v->end_date ||
                            $v->start_date <= $start_night_lanch_date && $end_night_lanch_date <= $v->end_date
                        ) {
                            $reduced_lanch = 1;
                        }

                        $start = new \DateTime($v->start_date);
                        $end = new \DateTime($v->end_date);
                        $difference = $end->diff($start)->format("%h") - $reduced_lanch;

                        $table .= '<tr>';
                        $table .= '<td>';
                        $table .= $k + 1;
                        $table .= '</td>';
                        $table .= '<td>';
                        $table .= $v->start_date;
                        $table .= '</td>';
                        $table .= '<td>';
                        $table .= $v->end_date;
                        $table .= '</td>';
                        $table .= '<td>';
                        $table .= $difference . $otgul_locales[$document->locale][3];
                        $table .= '</td>';
                        $table .= '</tr>';
                    }

                    $table .= '</tbody>';
                    $table .= '</table>';
                }

                if (strpos($content, '@table@')) {
                    $content = str_replace('@table@', $table, $content);
                } else {
                    $content .= $table;
                }
                $content .= $documentDetail->content2 ? $documentDetail->content2 : '';
                if ($document_template->is_list_vertical) {
                    $group_sequences = collect(DocumentDetailContent::select('group_sequence')
                        ->where('document_detail_id', $documentDetail->id)
                        ->orderBy('group_sequence', 'asc')
                        ->groupBy('group_sequence')
                        ->get())->pluck('group_sequence');

                    foreach ($group_sequences as $key => $group_sequence) {
                        $document_detail_contents = DocumentDetailContent::where('document_detail_id', $documentDetail->id)
                            ->where('group_sequence', $group_sequence)
                            ->get();
                        $d_d_attribute_id = 0;
                        $cols = 1;
                        $table = '';
                        if ($document_detail_contents[0]->documentDetailAttribute->is_list_vertical) {
                            $table = '<table border="1">';
                            $table .= '<tbody>';
                            foreach ($document_detail_contents as $key => $detail_content) {
                                if ($detail_content->group_name) {
                                    $cols = 2;
                                    if ($d_d_attribute_id == $detail_content->d_d_attribute_id) {
                                        $table .= '<tr><td>';
                                        $table .= $colum_locale[$detail_content->table_name][$detail_content->attribute_name][$document->locale];
                                        $table .= '</td><td>';
                                        $table .= $detail_content->value;
                                        $table .= '</td></tr>';
                                    } else {
                                        $d_d_attribute_id = $detail_content->d_d_attribute_id;
                                        $rows = DocumentDetailContent::where('d_d_attribute_id', $d_d_attribute_id)->where('document_detail_id', $documentDetail->id)->count();
                                        $table .= '<tr><td rowspan="' . $rows . '" style="width: 15%;text-align:center;font-weight: bold;">';
                                        $table .= $detail_content->group_name;
                                        $table .= '</td><td style=" width: 25%;text-align:right;font-weight: bold;">';
                                        $table .= $colum_locale[$detail_content->table_name][$detail_content->attribute_name][$document->locale];
                                        $table .= '</td><td>';
                                        $table .= $detail_content->value;
                                        $table .= '</td></tr>';
                                    }
                                } else {
                                    $dda = DocumentDetailAttribute::find($detail_content->d_d_attribute_id);
                                    if ($dda->data_type_id == 12 || $dda->data_type_id == 13) {
                                        $staff = Staff::find($detail_content->value);
                                        $table .= '<tr><td colspan="' . $cols . '" style="width: 40%;text-align:right;font-weight: bold;">';
                                        $table .= $detail_content->attribute_name;
                                        $table .= '</td><td>';
                                        $table .= $staff->department['department_code'] . ' ' . $staff->department['name_' . $document->locale] . '<br>';
                                        $table .= $staff->position['name_' . $document->locale];
                                        $table .= '</td></tr>';
                                    }
                                    // else if($dda->data_type_id == 6){
                                    //     $ts = TariffScale::find(1);
                                    //     $table .= '<tr><td colspan="' . $cols . '" style="width: 40%;text-align:right;font-weight: bold;">';
                                    //     $table .= $detail_content->attribute_name;
                                    //     $table .= '</td><td>';
                                    //     $table .= 125;
                                    //     // $table .= $ts ? $ts['category'] : $detail_content->value;
                                    //     $table .= '</td></tr>';
                                    // }
                                    elseif ($detail_content->d_d_attribute_id == 2571 || $detail_content->d_d_attribute_id == 2570) {

                                        // if ($document['document_template_id'] == 592)
                                        {
                                            // dd($document->documentDetails);
                                            foreach ($document->documentDetails as $key => $value) {
                                                $tabel = $value->documentDetailEmployees[0]->employee->tabel;
                                                $response = Http::withoutVerifying()
                                                    ->post('https://edo-db2.uzautomotors.com/api/get-stage/' . $tabel)->body();
                                                if ($response != "") {
                                                    $response = json_decode($response);
                                                    $boshqa_joyda_ishlagan_yillari = $response[1];
                                                    $date = substr($response[0], 0, 4) . '-' . substr($response[0], 4, 2) . '-' . substr($response[0], 6, 2);
                                                    // Creates DateTime objects
                                                    $datetime1 = date_create($date);
                                                    $ddc1 = DocumentDetailContent::where('document_detail_id', $value->id)->where('d_d_attribute_id', 2569)->first();
                                                    $datetime2 = date_create($ddc1->value);
                                                    // Calculates the difference between DateTime objects
                                                    $interval = date_diff($datetime1, $datetime2)->format('%y') + $boshqa_joyda_ishlagan_yillari;
                                                    $otpusk_den = (int) (($interval) / 5) * 2;
                                                    $otpusk_den = $otpusk_den > 8 ? 8 : $otpusk_den;
                                                    $ddav1 = DocumentDetailAttributeValue::where('document_detail_id', $value->id)->where('d_d_attribute_id', 2571)->first();
                                                    $ddav1->attribute_value = $interval;
                                                    $ddav1->save();
                                                    $ddav2 = DocumentDetailAttributeValue::where('document_detail_id', $value->id)->where('d_d_attribute_id', 2570)->first();
                                                    $ddav2->attribute_value = $otpusk_den;
                                                    $ddav2->save();
                                                    $ddc2 = DocumentDetailContent::where('document_detail_id', $value->id)->where('d_d_attribute_id', 2570)->first();
                                                    $ddc2->value = $otpusk_den;
                                                    $ddc2->save();
                                                }
                                            }
                                        }


                                        $table .= '<tr><td colspan="' . $cols . '" style="width: 40%;text-align:right;font-weight: bold;">';
                                        $table .= $detail_content->attribute_name;
                                        $table .= '</td><td>';
                                        $table .= $detail_content->d_d_attribute_id == 2570 ? $otpusk_den : $interval;
                                        $table .= '</td></tr>';
                                        // dd($detail_content->value);
                                    } else {
                                        // dd( $cols);
                                        if ($detail_content->documentDetailAttribute->is_show == 1) {
                                            $table .= '<tr><td colspan="' . $cols . '" style="width: 40%;text-align:right;font-weight: bold;">';
                                            $table .= $detail_content->attribute_name;
                                            $table .= '</td><td>';
                                            $table .= $detail_content->value;
                                            $table .= '</td></tr>';
                                        }
                                    }
                                }
                            }
                            $table .= '</tbody></table>';
                        } else {
                            $table = '<table border="1">';
                            $table .= '<thead><tr>';
                            $table .= '</tr></tbody></table>';
                        }
                        $content .= $table;
                    }
                } elseif (count($documentDetail->documentDetailContents) > 0 && $document->document_template_id != 587 && $document->document_template_id != 636) {

                    $document_detail_contents = DocumentDetailContent::where('document_detail_id', $documentDetail->id)->orderBy('d_d_attribute_id')->get();
                    $table = '<table border="1">';
                    $table .= '<thead><tr>';
                    foreach ($document_detail_contents as $key => $detail_content) {
                        $dda = DocumentDetailAttribute::find($detail_content->d_d_attribute_id);
                        if (($dda->data_type_id == 12) && $document->documentTemplate->change_staff) {
                            $table .= '<th>';
                            $table .= $detail_content->attribute_name;
                            $table .= '</th>';

                            $table .= '<th>';
                            // $table .= $document->locale == 'uz_latin' ? 'Yangi o`rnatilgan ustama(lar)i' : 'Янги ўрнатилган устама(лар)и';
                            $table .= $document->locale == 'ru' ? 'Новые установленные коэффициенты' : ($document->locale == 'uz_cyril' ? 'Янги ўрнатилган устама(лари)' : 'Yangi o`rnatilgan ustama(lari)');
                            $table .= '</th>';

                            $table .= '<th>';
                            // $table .= $document->locale == 'uz_latin' ? 'Kategoriya diapazoni' : 'Категория диапазон';
                            $table .= $document->locale == 'ru' ? 'Диапазон категории' : ($document->locale == 'uz_cyril' ? 'Категория диапазони' : 'Kategoriya diapazoni');
                            $table .= '</th>';
                        } elseif ($dda->data_type_id == 6 && $document->document_template_id == 586) {

                            $table .= '<th>';
                            $table .= $detail_content->attribute_name;
                            $table .= '</th>';

                            $table .= '<th>';
                            // $table .= $document->locale == 'uz_latin' ? 'Yangi o`rnatilgan ustama(lar)i' : 'Янги ўрнатилган устама(лар)и';
                            $table .= $document->locale == 'ru' ? 'Новые установленные коэффициенты' : ($document->locale == 'uz_cyril' ? 'Янги ўрнатилган устама(лари)' : 'Yangi o`rnatilgan ustama(lari)');
                            $table .= '</th>';

                            // $table .= '<th>';
                            // // $table .= $document->locale == 'uz_latin' ? 'Kategoriya diapazoni' : 'Категория диапазон';
                            // $table .= $document->locale == 'ru' ? 'Диапазон категории' : ($document->locale == 'uz_cyril' ? 'Категория диапазони' : 'Kategoriya diapazoni');
                            // $table .= '</th>';                       

                        } else {
                            $table .= '<th>';
                            $table .= $detail_content->attribute_name;
                            $table .= '</th>';
                        }
                    }
                    $table .= '</tr></thead><tbody><tr>';
                    $staff = null;

                    foreach ($document_detail_contents as $key => $detail_content) {
                        $dda = DocumentDetailAttribute::find($detail_content->d_d_attribute_id);
                        if (($dda->data_type_id == 12) && $document->documentTemplate->change_staff) {
                            $staff = Staff::find($detail_content->value);
                            $table .= '<td>';
                            $table .= $staff->department['department_code'] . ' ' . $staff->department['name_' . $document->locale] . '<br>';
                            $table .= $staff->position['name_' . $document->locale];
                            $table .= '</td>';

                            // Range va koeflar
                            $coefficients = DocumentDetailCoefficient::where('document_detail_id', $documentDetail->id)->where('type', 1)->get();
                            $table .= '<td>';
                            foreach ($coefficients as $ckey => $cvalue) {
                                // $table .= $cvalue->tariffScale['description_'.$document->locale];
                                $table .= $cvalue->value . '% ' . $cvalue->tariffScale['description_' . $document->locale] . '<br>';
                            }
                            $table .= '</td>';
                            $table .= '<td>';
                            $table .= $staff->range->code;
                            $table .= '</td>';
                        } elseif ($dda->data_type_id == 6 && $document->document_template_id == 586) {
                            // dd($documentDetail->documentDetailEmployees[0]->employee->staff[0]);
                            $staff = $documentDetail->documentDetailEmployees[0]->employee->staff[0];
                            // $staff = Staff::find($detail_content->value);
                            // $table .= '<td>';
                            // $table .= $staff->department['department_code'] . ' ' . $staff->department['name_' . $document->locale] . '<br>';
                            // $table .= $staff->position['name_' . $document->locale];
                            // $table .= '</td>';


                            $table .= '<td>';
                            $table .= $detail_content->value ? $detail_content->value : '';
                            $table .= '</td>';

                            // Range va koeflar
                            $coefficients = DocumentDetailCoefficient::where('document_detail_id', $documentDetail->id)->where('type', 1)->get();
                            $table .= '<td>';
                            foreach ($coefficients as $ckey => $cvalue) {
                                // $table .= $cvalue->tariffScale['description_'.$document->locale];
                                $table .= $cvalue->value . '% ' . $cvalue->tariffScale['description_' . $document->locale] . '<br>';
                            }
                            $table .= '</td>';
                            // $table .= '<td>';
                            // $table .= $staff->range->code;
                            // $table .= '</td>';                                             

                        } else {
                            $table .= '<td>';
                            $table .= $detail_content->value ? $detail_content->value : '';
                            $table .= '</td>';
                        }
                    }
                    // if($staff){
                    //     $coefficients = DocumentDetailCoefficient::where('document_detail_id',$documentDetail->id)->where('type',1)->get();
                    //     $table .= '<td>';
                    //     foreach ($coefficients as $ckey => $cvalue) {
                    //         // $table .= $cvalue->tariffScale['description_'.$document->locale];
                    //         $table .= $cvalue->value.'% '.$cvalue->tariffScale['description_'.$document->locale].'<br>';
                    //     }
                    //     $table .= '</td>';
                    //     $table .= '<td>';
                    //     $table .= $detailEmployee->range->code;
                    //     $table .= '</td>';
                    // }
                    $table .= '</tr></tbody></table>';
                    $content .= $table;
                } elseif ($document->document_template_id == 636) {
                    //ijrochi yopganda signerattributeni yozib qoyish
                    $ijrochi = DocumentSigner::where('document_id', $document->id)
                        ->where('status', 1)
                        ->where('action_type_id', 4)
                        ->whereNull('parent_employee_id')->first();

                    $documentDetailSignerAttributes = DocumentDetailSignerAttribute::where('document_detail_id', $documentDetail->id)
                        ->whereHas('documentDetailAttributes', function ($q) {
                            $q->where('is_active', 1);
                        })
                        ->orderBy('id')
                        ->get();
                    if ($ijrochi) {
                        $qaror = DocumentDetailSignerAttribute::where('document_detail_id', $documentDetail->id)->where('d_d_attribute_id', 2900)->whereNotNull('value')->first();
                    }
                    $table = '<table border="1">';
                    $table .= '<thead><tr>';
                    foreach ($documentDetailSignerAttributes as $key => $ddsa) {
                        if ($ddsa->d_d_attribute_id != 2900) {

                            $table .= '<th>';
                            $table .= $ddsa->documentDetailAttributes['attribute_name_' . $document->locale];
                            $table .= '</th>';
                        }
                    }
                    $table .= '</tr></thead><tbody><tr>';
                    $arr = [];
                    foreach ($documentDetailSignerAttributes as $key => $ddsa) {
                        if ($ddsa->d_d_attribute_id != 2900) {
                            $table .= '<th>';
                            if ($ddsa->documentDetailAttributes->table_list_id == 5) {

                                if ($ijrochi && (($ddsa->d_d_attribute_id == 2882 || $ddsa->d_d_attribute_id == 2883) && !$ddsa->value)) {
                                    $ts = TariffScale::find($qaror->value);
                                    $table .= $ts ? $ts->category : '';
                                } else {
                                    $ts = TariffScale::find($ddsa->value);
                                    $table .= $ts ? $ts->category : '';
                                }
                            } else {
                                $table .= $ddsa->value;
                            }
                            $table .= '</th>';
                        }
                    }
                    $table .= '</tr></tbody></table>';
                    $content .= $table;
                }
                if ($document->document_template_id != 636) {
                    $documentDetailSignerAttributes = DocumentDetailSignerAttribute::where('document_detail_id', $documentDetail->id)->get();
                    if ($documentDetailSignerAttributes) {
                        $table = '<table border="1"><tboby>';
                        foreach ($documentDetailSignerAttributes as $key => $ddsa) {
                            $table .= '<tr>';
                            $table .= '<td style="width: 40%;text-align:right;font-weight: bold;">';
                            $table .= $ddsa->documentDetailAttributes['attribute_name_' . $document->locale];
                            $table .= '</td>';
                            $table .= '<td>';
                            $table .= $ddsa->value;
                            $table .= '</td>';
                            $table .= '</tr>';
                        }
                        $table .= '</tbody></table>';
                        $content .= $table;
                    }
                }
            }
        } elseif($document->document_template_id == 666){
            // dd($document);
            $content .= $document->documentDetails[0]->content != 'null' ? $document->documentDetails[0]->content : '';
        } else {            
            $content .= $document->documentDetails[0]->content != 'null' ? $document->documentDetails[0]->content : '';
            $content .= $document->documentDetails[0]->content2 ? $document->documentDetails[0]->content2 : '';
            if ($document->document_template_id == 615 || $document->document_template_id == 619 || $document->document_template_id == 622) {
                // dd(111);
                return self::replaceDate($id, $content);
                // } else if ($document->document_template_id == 552 
            } else if (in_array($document->document_template_id, [352, 552, 553, 554, 556, 557, 641])) {
                $argument = self::replaceContractData($id, $content);
                return $argument;
                //dd($content);
            } else if (in_array($document->document_template_id, [637, 424])) {
                $argument = self::replaceContractData($id, $content);
                return $argument;
                //dd($content);
            }
            if ($document_template->is_list_vertical) {
                ///UzAutoJobs/////////////////
                if ($document->document_template_id === 663) {
                    $document_dt = $document->documentDetails[0]->documentDetailContents;
                    $table = '<table border="1">';
                    $table .= '<thead><tr>';
                    $table .= '<th>';
                    $table .= 'T/r*';
                    $table .= '</th>';
                    foreach ($document_dt as $key => $detail_content) {
                        $table .= '<th>';
                        $table .= $detail_content->attribute_name;
                        $table .= '</th>';
                    }
                    $table .= '</tr></thead>';
                    $table .= '<tbody>';
                    foreach ($document->documentDetails as $keyd => $documentDetail) {                        
                            $document_detail_contents = DocumentDetailContent::where('document_detail_id', $documentDetail->id)
                                ->orderBy('id')
                                ->get();
                            $table .= '<tr>';
                            $table .= '<td>';
                            $table .= $keyd + 1;
                            $table .= '</td>';
                            foreach ($document_detail_contents as $key => $detail_content) {
                                $table .= '<td>';
                                $table .= $detail_content->value;
                                $table .= '</td>';
                            }
                            $table .= '</tr>';
                    }
                    $table .= '</tbody></table>';
                }                
                ///UzAutoJobs/////////////////
                foreach ($document->documentDetails as $key => $documentDetail) {
                    $group_sequences = collect(DocumentDetailContent::select('group_sequence')
                        ->where('document_detail_id', $documentDetail->id)
                        ->orderBy('group_sequence', 'asc')
                        ->groupBy('group_sequence')
                        ->get())->pluck('group_sequence');
                    foreach ($group_sequences as $key => $group_sequence) {
                        $document_detail_contents = DocumentDetailContent::where('document_detail_id', $documentDetail->id)
                            ->where('group_sequence', $group_sequence)
                            ->orderBy('id')
                            ->get();
                        $d_d_attribute_id = 0;
                        $cols = 1;
                        if ($document_detail_contents[0]->documentDetailAttribute->is_list_vertical) {
                            $table = '<table border="1">';
                            $table .= '<tbody>';
                            foreach ($document_detail_contents as $key => $detail_content) {
                                // return $colum_locale[$detail_content->table_name][$detail_content->attribute_name][$document->locale];
                                if ($detail_content->group_name) {
                                    $cols = 2;
                                    if ($d_d_attribute_id == $detail_content->d_d_attribute_id) {
                                        $table .= '<tr><td style="text-align:right;font-weight: bold;">';
                                        $table .= $colum_locale[$detail_content->table_name][$detail_content->attribute_name][$document->locale];
                                        $table .= '</td><td>';
                                        $table .= $detail_content->value;
                                        $table .= '</td></tr>';
                                    } else {
                                        $d_d_attribute_id = $detail_content->d_d_attribute_id;
                                        $rows = DocumentDetailContent::where('d_d_attribute_id', $d_d_attribute_id)->where('document_detail_id', $documentDetail->id)->count();
                                        $table .= '<tr><td rowspan="' . $rows . '" style="width: 15%;text-align:center;font-weight: bold;">';
                                        $table .= $detail_content->group_name;
                                        $table .= '</td><td style="width: 25%;text-align:right;font-weight: bold;">';
                                        $table .= $colum_locale[$detail_content->table_name][$detail_content->attribute_name][$document->locale];
                                        $table .= '</td><td>';
                                        $table .= $detail_content->value;
                                        $table .= '</td></tr>';
                                    }
                                } else {
                                    $table .= '<tr><td colspan="' . $cols . '" style="width: 40%;text-align:right;font-weight: bold;">';
                                    $table .= $detail_content->attribute_name;
                                    $table .= '</td><td>';
                                    $table .= $detail_content->value;
                                    $table .= '</td></tr>';
                                }
                            }
                            $table .= '</tbody></table>';
                        }
                        ///UzAutoJobs/////////////////                       
                        elseif ($document->document_template_id==663) {
                            ////Tegmanglar ma'lumoti bulmas xam tepada ishlayabdi
                        } ///UzAutoJobs/////////////////
                        else {
                            $table = '<table border="1">';
                            $table .= '<thead><tr>';
                            foreach ($document_detail_contents as $key => $detail_content) {
                                $table .= '<th>';
                                $table .= $detail_content->attribute_name;
                                $table .= '</th>';
                            }
                            $table .= '</tr></thead><tbody><tr>';
                            foreach ($document_detail_contents as $key => $detail_content) {
                                $table .= '<td>';
                                $table .= $detail_content->value;
                                $table .= '</td>';
                            }
                            $table .= '</tbody></table>';
                        }
                        $content .= $table;
                    }
                    $documentDetailSignerAttributes = DocumentDetailSignerAttribute::where('document_detail_id', $documentDetail->id)
                        ->whereHas('documentDetailAttributes', function ($q) {
                            $q->where('is_active', 1);
                        })
                        ->get();
                    if ($documentDetailSignerAttributes) {
                        $table = '<table border="1"><tboby>';
                        foreach ($documentDetailSignerAttributes as $key => $ddsa) {
                            $table .= '<tr><td style="width: 40%;text-align:right;font-weight: bold;">';
                            $table .= $ddsa->documentDetailAttributes['attribute_name_' . $document->locale];
                            $table .= '</td><td>';
                            $table .= $ddsa->value;
                            $table .= '</td></tr>';
                        }
                        $table .= '</tbody></table>';
                        $content .= $table;
                    }
                }
                // return $document->documentDetails[0]->id;
            } elseif (count($document->documentDetails[0]->documentDetailContents) > 0) {
                if (in_array($document->document_template_id, [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543, 545, 546, 547, 548, 583, 588, 595, 609, 618])) {
                    $document_detail_contents = DocumentDetailContent::where('document_detail_id', $document->documentDetails[0]->id)->orderBy('sequence')->orderBy('id')->get();
                    if ($document->id == 2380035) {
                        dd($document_detail_contents->pluck('sequence'));
                    }
                    // dd(DocumentDetailContent::where('document_detail_id', $documentDetail->id)->orderBy('sequence','desc')->get()->pluck('sequence'));
                } else {
                    $document_detail_contents = DocumentDetailContent::where('document_detail_id', $document->documentDetails[0]->id)->orderBy('group_sequence')->orderBy('id')->get();
                }
                $conut = count($document->documentDetails[0]->documentDetailContents);
                if ($id == 2730249) {
                    // dd($document_detail_contents[6]->documentDetailAttribute);
                    $table = '<table style="font-size: 12px;" class="dynamic_table" border="1">';
                } else {
                    $table = '<table style="font-size: 12px;" class="dynamic_table" border="1">';
                }
                $table .= '<thead><tr>';
                $table .= '<th>№</th>';
                foreach ($document_detail_contents as $key => $detail_content) {
                    if ($detail_content->documentDetailAttribute->is_show == 1) {
                        $table .= '<th>';
                        $table .= $detail_content->group_name ?
                            $colum_locale[$detail_content->table_name][$detail_content->attribute_name][$document->locale] :
                            $detail_content->attribute_name . '.';

                        // $table .= $detail_content->attribute_name;
                        $table .= '</th>';
                    }
                }
                $table .= '</tr></thead><tbody>';
                foreach ($document->documentDetails as $key => $documentDetail) {
                    if (in_array($document->document_template_id, [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543, 545, 546, 547, 548, 583, 588, 595, 609, 618])) {
                        $document_detail_contents = DocumentDetailContent::where('document_detail_id', $documentDetail->id)->orderBy('sequence')->orderBy('id')->get();
                        // dd(DocumentDetailContent::where('document_detail_id', $documentDetail->id)->orderBy('sequence','desc')->get()->pluck('sequence'));
                    } else {
                        $document_detail_contents = DocumentDetailContent::where('document_detail_id', $documentDetail->id)->orderBy('group_sequence')->orderBy('id')->get();
                    }
                    $number = $key + 1;
                    $table .= '<tr>';
                    $table .= '<td>' . $number . '</td>';
                    foreach ($document_detail_contents as $key1 => $detail_content) {
                        if ($detail_content->documentDetailAttribute->is_show == 1) {
                            $table .= '<td>';
                            // if($document->id == 2205072 && $detail_content->documentDetailAttribute->tableList){
                            //     $data = DB::table($detail_content->documentDetailAttribute->tableList->table_name)->find($detail_content->value);
                            //     $column_name = $detail_content->documentDetailAttribute->tableList->column_name;
                            //     $column_name = $column_name == 'name_locale' ? 'name_'.$document->locale : $column_name;
                            //     // dd(get_object_vars($data));
                            //     $encoded = json_encode($data);

                            //     $abc = json_decode($encoded,true);
                            //     // $abc = json_decode($encoded,true)[$column_name];
                            //     // dd($abc);
                            //     $table .= $abc[$column_name];
                            // }elseif ($document->documentTemplate->id == 431){
                            // if($detail_content->documentDetailAttribute->id == 1323){
                            //     $table .= ($detail_content->value);
                            //     // $table .= ($detail_content->value*.100);
                            // }else{
                            //     $table .= $detail_content->value;
                            // }
                            // } else
                            if ($detail_content->documentDetailAttribute->id == 1323) {
                                $abc = collect($document_detail_contents)->first(function ($value, $key) {
                                    return $value->d_d_attribute_id == 1322;
                                });
                                $table .= $abc->value * 0.6;                            
                            } else {
                                // if($id == 2378768){
                                $table .= isset($detail_content->value) ? $detail_content->value : '';
                                // $table .= $detail_content->value ? $detail_content->value : (empty($detail_content->value) && $detail_content->value == null ? '' : 0);
                                // }
                                // else {
                                //     $table .= $detail_content->value;
                                // }
                            }
                            $table .= '</td>';
                        }
                    }
                    $table .= '</tr>';
                }
                $table .= '</tbody></table>';
                if ($document->document_template_id === 664) {
                    $content = str_replace('@table', $table, $content);
                }
                else{
                    $content .= $table;
                }
                    // $content .= $table;
            }
            if (isset($documentDetail)) {
                $documentDetailSignerAttributes = DocumentDetailSignerAttribute::where('document_detail_id', $documentDetail->id)->get();
                if ($documentDetailSignerAttributes) {
                    $table = '<table border="1" style=""><tboby>';
                    foreach ($documentDetailSignerAttributes as $key => $ddsa) {
                        $table .= '<tr><td style="width: 40%;text-align:right;font-weight: bold;">';
                        $table .= $ddsa->documentDetailAttributes['attribute_name_' . $document->locale];
                        $table .= '</td><td>';
                        $table .= $ddsa->value;
                        $table .= '</td></tr>';
                    }
                    $table .= '</tbody></table>';
                    $content .= $table;
                }
            }
        }
        // if (in_array($document->document_template_id, [615,619,622])) {
        //     return Self::replaceDate($id, $content);
        // }


        if ($document->document_template_id == 431) {
            $department2 = $document->department2['name_uz_latin'];
            $content = str_replace('____________________________', ' ' . $department2, $content);
            // dd($department2);
            $content = preg_replace('/______/', ' ' . substr($document->document_date, 0, 4), $content, 1);
        }



        return $content;
    }

    public static function replaceDate($id, $content)
    {
        // dd($content);
        $answers = ComplaensAnswer::where('document_id', $id)->get();
        $document = self::find($id);
        // if ($document->document_template_id == 615) {
        //     $documentSigner = DocumentSigner::where('document_id', $id)
        //     ->where('action_type_id', 6)->first();           

        //     $employeeCotent = '';
        //     $employeeCotent .= $documentSigner->fio;
        //     $employeeCotent .= '<br>';
        //     $content = str_replace('@fio', $employeeCotent, $content);
        //     // dd($employeeCotent);
        // }
        if ($document->document_template_id == 622 || $document->document_template_id == 615) {
            $c = [
                'ru' => 'Ф.И.О (полностью)',
                'uz_latin' => 'F.I.Sh (to‘liq)',
                'uz_cyril' => 'Ф.И.О (тўлиқ)',
            ];
            $document_detail = DocumentDetail::where('document_id', $id)->first();
            $document_detail_id = $document_detail->id;
            $document_detail_employees = DocumentDetailEmployee::where('document_detail_id', $document_detail_id)->first();
            $employeeCotent = '';
            // $employeeCotent .= $c[$document->locale];
            // $employeeCotent .= ' ';
            $employeeCotent .= $document_detail_employees->employee_fio;
            $employeeCotent .= '<br>';
            $employeeCotent .= ' (';
            $employeeCotent .= $document_detail_employees->employee_department;
            $employeeCotent .= ' - ';
            $employeeCotent .= $document_detail_employees->employee_position;
            $employeeCotent .= ') ';
            $content = str_replace('@fio', $employeeCotent, $content);
            // dd($employeeCotent);
        }

        $answerContent = '';
        $abc = '';
        if ($answers) {
            // dd($answers);
            foreach ($answers as $key => $answer) {
                $question = ComplaensQuestion::find($answer->question_id);
                // if ($question->question_type == 4) {
                //     $answerContent .= 'Ushbu arizani imzolab quyidagilarni tasdiqlayman';
                //     $answerContent .= '<br>';
                // }
                $answerContent .= '<table style="width:100%;border-collapse: collapse; ">';
                $answerContent .= '<tr style="font-size:18pt;">';
                $answerContent .= '<td style="border: 1px solid black; background-color: #DCDCDC">';
                $answerContent .= $key + 1;
                $answerContent .= '</td>';
                $answerContent .= '<td style="border: 1px solid black; background-color: #DCDCDC">';
                $answerContent .= $question->question;
                $answerContent .= '<td>';
                $answerContent .= '<tr>';


                if ($answer->answer == 1) {
                    // $answerContent .= '<br>';
                    if ($question->question_type != 3) {
                        $answerContent .= '<tr style="font-size:18pt;">';
                        $answerContent .= '<td style="border: 1px solid black; background-color: #DCDCDC">';
                        $answerContent .= '</td>';
                        $answerContent .= '<td style="border: 1px solid black; background-color: #DCDCDC">';
                        $answerContent .= '<b>';
                        if ($question->question_type == 4) {

                            $answerContent .= 'Mavjud ';
                        } else {
                            $answerContent .= 'Ha ';
                        }
                        // $answerContent .= '</b>';
                        $answerContent .= '<td>';
                        $answerContent .= '<tr>';

                        $answerContent .= '<br>';
                    }
                    $answerContent .= '<tr style="font-size:18pt;">';
                    $answerContent .= '<td style="border: 1px solid black; background-color: #DCDCDC">';
                    $answerContent .= '</td>';
                    $answerContent .= '<td style="border: 1px solid black; background-color: #DCDCDC">';
                    if ($question->question_type == 1) {
                        $relatives = ComplaensRelative::where('answer_id', $answer->id)->get();
                        if ($relatives) {
                            foreach ($relatives as $key => $value) {
                                $answerContent .= '<b>';
                                // $employee = Employee::find($value->relative_id);
                                // $answerContent .= $employee->lastname_uz_latin . ' ' . $employee->firstname_uz_latin;

                                $answerContent .= $value->fio;
                                $answerContent .= '</b>';
                                $answerContent .= ' (';
                                $answerContent .= '<i>';
                                $answerContent .= $value->department;
                                $answerContent .= ' ';
                                $answerContent .= $value->position;
                                $answerContent .= '<i>';
                                $answerContent .= ') ';
                                $relative_type = FamilyRelative::where('id', $value->relative_type_id)->first();
                                if ($relative_type) {

                                    $answerContent .= ' (';
                                    $answerContent .= $relative_type->name_uz_latin;
                                    $answerContent .= ')';
                                    $answerContent .= '<br>';
                                }
                                // $answerContent .= '<br>';
                            }
                        }
                    } else {
                        $answerContent .= '<b>';
                        $answerContent .= $answer->description;
                        $answerContent .= '</b>';
                        $answerContent .= '<br>';
                    }
                    $answerContent .= '<td>';
                    $answerContent .= '<tr>';
                } else {
                    $answerContent .= '<tr style="font-size:18pt;">';
                    $answerContent .= '<td style="border: 1px solid black; background-color: #DCDCDC">';
                    $answerContent .= '</td>';
                    $answerContent .= '<td style="border: 1px solid black; background-color: #DCDCDC">';
                    // $answerContent .= '<br>';
                    $answerContent .= '<b>';
                    if ($question->question_type == 4) {

                        $answerContent .= 'Mavjud emas ';
                    } else {

                        $answerContent .= 'Yo`q';
                    }
                    $answerContent .= '</b>';
                    // $answerContent .= '<br>';
                    $answerContent .= '<td>';
                    $answerContent .= '<tr>';
                }
                if ($question->question_type == 4) {
                    $answerContent .= '<tr style="font-size:18pt;">';
                    $answerContent .= '<td colspan="2" >';
                    $answerContent .= 'Ushbu arizani imzolab quyidagilarni tasdiqlayman';
                    $answerContent .= '<br>';
                    $answerContent .= '</td>';
                    $answerContent .= '<tr>';
                }

                $answerContent .= '</table>';
            }

            $content = str_replace('@complaens', $answerContent, $content);
        }
        $document = Document::find($id);

        $ddcs = collect($document->documentDetails[0]->documentDetailContents)->filter(function ($item) {
            return !!$item->documentDetailAttribute->replace_attribute;
        });

        if (count($ddcs)) {
            foreach ($ddcs as $key => $value) {
                $content = str_replace($value->documentDetailAttribute->replace_attribute, $value->value, $content);
                // dd($content);
            }
        }
        return $content;
    }

    public function getHoursBeetweenDates($date1, $date2)
    {
        try {

            $date1 = strtotime($date1);
            $date2 = strtotime($date2);
            $datediff = $date2 - $date1;
            return round($datediff / (60 * 60));
        } catch (\Throwable $th) {
            //throw $th;
            return 0;
        }
    }

    public static function generatePdfForSignDocument($data)
    {
        $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "freeserif";
                letter-spacing:1px;
            }
            body{
                margin-right: 80px;
                margin-left: 120px;
                font-family: "times";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }
        </style>';

        // $content .= '<h1>';
        // $content .= Self::normalizeDocumentContent(Self::documentContent($document_id));
        // return $data['html'];
        $content .= $data['html'];
        // $content .= '</h1>';
        // $content .= Self::getSignerTableUzavtosanoat($document_id);

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setPaper('a4')
            ->setOption('footer-spacing', 2)
            ->setOption('header-spacing', 0)
            ->setOption('margin-top', 25)
            ->setOption('margin-bottom', 35)
            ->setOption('margin-left', 3);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
    }

    public static function style()
    {
        return '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <style>
                @font-face{
                    font-family:  times;
                    font-style: normal;
                    font-weight: normal;
                    src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
                }
                @font-face{
                    font-family:  times;
                    font-style: bold;
                    font-weight: normal;
                    src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
                }
                @font-face{
                    font-family:  times;
                    font-style: italic;
                    font-weight: normal;
                    src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
                }
                @font-face{
                    font-family:  times;
                    font-style: bolditalic;
                    font-weight: normal;
                    src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
                }
                p[style*="text-align: justify;"]{
                    text-indent: 40px;
                    // margin-right:20px;
                }
                b, strong{
                    font-family: "freeserif";
                    letter-spacing:1px;
                }
                @page {
                    padding: 0px 0px 0px;
                }
                body{
                        font-family: "times";
                    //font-family: "freeserif";
                    letter-spacing:1px;
                    // text-rendering: auto;
                    // text-rendering: optimizeSpeed;
                    // text-rendering: optimizeLegibility;
                    text-rendering: geometricPrecision;
                }
                #line1 {
                    border-bottom:1px solid black;
                    margin-top:30px;
                }
                #line10 {border-top:5px solid black;}
            </style>';
    }


    public static function stampDocument($id)
    {
        $document = Document::find($id);
        $doc_number = $document->document_number_reg ? $document->document_number_reg : $document->document_number;
        $files = File::where('object_id', $id)
            ->where('object_type_id', 5)
            ->where('file_name', 'ilike', '%.pdf')
            ->get();
        // return file_exists('/var/www/workflow/backend/storage/app/documents/16584013591065931709');
        // return Storage::exists('documents\16584013591065931709');

        foreach ($files as $key => $value) {
            if ($value->created_at > '2023-09-01 00:00:00') {
                $file = storage_path('app/documents_new/' . $value->physical_name);
            } else {
                $file = storage_path('app/documents/' . $value->physical_name);
            }
            // return file_exists($file);
            if ($id == 2775770) {
                // return phpinfo();
                // dd(file_exists($file));
            }
            $pdf = new Pdf($file);
            // return
            $numberOfpages = $pdf->getNumberOfPages();
            for ($i = 1; $i <= $numberOfpages; $i++) {
                $pdf->setPage($i)->saveImage(storage_path('app/temp/' . $value->physical_name . '-' . $i . '.jpg'));
            }
            $pdf = \App::make('snappy.pdf.wrapper');
            $content = self::style();

            $date = $document->document_date_reg ? $document->document_date_reg : $document->document_date;
            $day = date('d', strtotime($date));
            $month = self::getMonthName(date('m', strtotime($date)), $document->locale);
            $year = date('Y', strtotime($date));


            for ($i = 1; $i <= $numberOfpages; $i++) {
                if ($i == 1) {
                    // return file_exists('/var/www/workflow/backend/storage/app/temp/1658407857780231852-1.jpg');
                    // dd(storage_path() . '/app/temp/' . $value->physical_name . '-' .  $i . '.jpg');
                    $content .= '<img  height="1950" src="' . storage_path() . '/app/temp/' . $value->physical_name . '-' . $i . '.jpg"/>';
                    $content .= '<img style="position: absolute; top: 1800; left: 100px;" height="150" src="' . public_path() . '/img/shtamp.png"/>';
                    $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; top: 1799; left:115px;width:309px; text-align:center;">' . $doc_number . '</p>';
                    $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; top: 1845; left:200px;width:143px; text-align:center;">' . $month . '</p>';
                    $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; top: 1845; left:125px;width:55px; text-align:center;">' . $day . '</p>';
                    $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; top: 1845; left:353px;width:71px; text-align:center;">' . $year . '</p>';
                } else {
                    $content .= '<img  height="1950" src="' . storage_path() . '/app/temp/' . $value->physical_name . '-' . $i . '.jpg"/>';
                }
            }

            $pdf->setOption('images', true)
                // ->setOption('footer-right', '[page] / [topage]')
                ->setOption('footer-font-name', 'times')
                // ->setOption('footer-font-size', '10')
                ->setPaper('a4')
                ->setOption('margin-top', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0)
                ->loadHTML($content);
            try {
                if (Storage::exists('documents_new/' . $value->physical_name)) {
                    Storage::delete('documents_new/' . $value->physical_name);
                } else {
                    return ('Pdf does not exist.');
                }

                $pdf->save(storage_path() . '/app/documents_new/' . $value->physical_name);
                $file = File::find($value->id);
                $file->file_name = $doc_number . '_' . ($key + 1) . '.pdf';
                $file->save();

                for ($i = 1; $i <= $numberOfpages; $i++) {
                    if (Storage::exists('temp/' . $value->physical_name . '-' . $i . '.jpg')) {
                        Storage::delete('temp/' . $value->physical_name . '-' . $i . '.jpg');
                    } else {
                        // return ('Image does not exist.');
                    }
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        $document->stamped = 1;
        $document->save();
        return 200;
    }

    public static function stampUploaddedPdf($id)
    {
        $document = Document::find($id);
        $doc_number = $document->document_number_reg ? $document->document_number_reg : $document->document_number;
        $files = File::where('object_id', $id)
            ->where('object_type_id', 5)
            ->where('file_name', 'like', '%.pdf')
            ->get();

        foreach ($files as $key => $value) {
            $file = storage_path('app/documents/' . $value->physical_name);
            $pdf = new Pdf($file);
            $numberOfpages = $pdf->getNumberOfPages();
            for ($i = 1; $i <= $numberOfpages; $i++) {
                $pdf->setPage($i)->saveImage(storage_path('app/temp/' . $value->physical_name . '-' . $i . '.jpg'));
            }
            $pdf = \App::make('snappy.pdf.wrapper');
            $content = self::style();

            $date = $document->document_date_reg ? $document->document_date_reg : $document->document_date;
            $day = date('d', strtotime($date));
            $month = self::getMonthName(date('m', strtotime($date)), $document->locale);
            $year = date('Y', strtotime($date));

            $getimagesize = 0;
            for ($i = 1; $i <= $numberOfpages; $i++) {
                $image_info = getimagesize(storage_path('app/temp/' . $value->physical_name . '-' . $i . '.jpg'));
                $height = $image_info[1];
                if ($height > 1200) {
                    if ($i == 1) {
                        $content .= '<img  height="1950" src="' . storage_path() . '/app/temp/' . $value->physical_name . '-' . $i . '.jpg"/>';
                        $content .= '<img style="position: absolute; bottom: 20px; left: 500px;" height="150" src="' . public_path() . '/img/shtamp_ilova.png"/>';
                        $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; bottom: 90; left:610px;width:143px; text-align:center;">' . $month . '</p>';
                        $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; bottom: 90; left:525px;width:55px; text-align:center;">' . $day . '</p>';
                        $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; bottom: 90; left:753px;width:71px; text-align:center;">' . $year . '</p>';
                        $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; bottom: 50; left:525px;width:245px; text-align:center;">' . $doc_number . '</p>';
                    } else {
                        $content .= '<img  height="1950" src="' . storage_path() . '/app/temp/' . $value->physical_name . '-' . $i . '.jpg"/>';
                    }
                } else {
                    $getimagesize = 1;
                    if ($i == 1) {
                        $content .= '<img  width="1950" src="' . storage_path() . '/app/temp/' . $value->physical_name . '-' . $i . '.jpg"/>';
                        $content .= '<img style="position: absolute; bottom: 20px; left: 700px;" height="150" src="' . public_path() . '/img/shtamp_ilova.png"/>';
                        $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; bottom: 95; left:810px;width:143px; text-align:center;">' . $month . '</p>';
                        $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; bottom: 95; left:725px;width:55px; text-align:center;">' . $day . '</p>';
                        $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; bottom: 95; left:953px;width:71px; text-align:center;">' . $year . '</p>';
                        $content .= '<p style="position:fixed; font-weight:bold; font-size:20pt; bottom: 55; left:725px;width:245px; text-align:center;">' . $doc_number . '</p>';
                    } else {
                        $content .= '<img  width="1950" src="' . storage_path() . '/app/temp/' . $value->physical_name . '-' . $i . '.jpg"/>';
                    }
                }
            }
            // return $content;
            if ($getimagesize == 1) {
                $pdf->setOrientation('landscape');
            }
            $pdf->setOption('images', true)
                // ->setOption('footer-right', '[page] / [topage]')
                ->setOption('footer-font-name', 'times')
                // ->setOption('footer-font-size', '10')
                ->setPaper('a4')
                ->setOption('margin-top', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0)
                ->loadHTML($content);
            try {
                if (Storage::exists('documents/' . $value->physical_name)) {
                    Storage::delete('documents/' . $value->physical_name);
                } else {
                    // return ('Pdf does not exist.');
                }

                $pdf->save(storage_path() . '/app/documents/' . $value->physical_name);
                // $file = File::find($value->id);
                // $file->file_name =  $doc_number . '_' . ($key + 1) . '.pdf';
                // $file->save();

                for ($i = 1; $i <= $numberOfpages; $i++) {
                    if (Storage::exists('temp/' . $value->physical_name . '-' . $i . '.jpg')) {
                        Storage::delete('temp/' . $value->physical_name . '-' . $i . '.jpg');
                    } else {
                        // return ('Image does not exist.');
                    }
                }
            } catch (\Throwable $th) {
                // throw $th;
            }
        }
        $document->stamped = 1;
        $document->save();
        return 200;
    }

    public static function manualDocument($document_id)
    {
        $document = self::find($document_id);
        $doc_temp = DocumentTemplate::find($document->document_template_id);
        $content = '';
        $manual_file = File::where('object_id', $document_id)->where('object_type_id', 17)->first();
        if ($manual_file) {
            $file = storage_path('app/documents_new/' . $manual_file->physical_name);
            if (file_exists($file)) {
                $pdf = new Pdf($file);
                $numberOfpages = $pdf->getNumberOfPages();
                for ($i = 1; $i <= $numberOfpages; $i++) {
                    $pdf->setPage($i)->saveImage(storage_path('app/temp/' . $manual_file->physical_name . '-' . $i . '.jpg'));
                }
                $pdf = \App::make('snappy.pdf.wrapper');
                $content = self::style();
                for ($i = 1; $i <= $numberOfpages; $i++) {
                    $image_info = getimagesize(storage_path('app/temp/' . $manual_file->physical_name . '-' . $i . '.jpg'));
                    $content .= '<div style="height: 90px"></div>';
                    if ($doc_temp->is_pdf_portrait) {
                        $content .= '<img style="width:100%; height:1150px"; src="' . storage_path() . '/app/temp/' . $manual_file->physical_name . '-' . $i . '.jpg"/>';
                    } else {
                        $content .= '<img style="height:750px;  width:100%";   src="' . storage_path() . '/app/temp/' . $manual_file->physical_name . '-' . $i . '.jpg"/>';
                    }
                }
                for ($i = 1; $i <= $numberOfpages; $i++) {
                    if (Storage::exists('temp/' . $manual_file->physical_name . '-' . $i . '.jpg')) {
                        // Storage::delete('temp/' . $manual_file->physical_name . '-' .  $i . '.jpg');
                    } else {
                        // return ('Image does not exist.');
                    }
                }
            }



            // $content .= 'liverpoolfc';
        } else {
            $content .= 'maxsus fayl yuklanmagan';
        }
        return $content;
    }

    public static function eimzoMobileAuth()
    {
        $response = Http::post('http://127.0.0.1:8082/frontend/mobile/sign');
        return $response;
    }

    public static function frontendMobileSign()
    {
        $response = Http::post('http://127.0.0.1:8082/frontend/mobile/sign');
        return [
            'status' => true,
            'data' => json_decode($response),
            "message" => "Success"
        ];
    }

    public static function frontendMobileStatus($doc_id)
    {
        $response = Http::asForm()
            ->post('http://127.0.0.1:8082/frontend/mobile/status', [
                'documentId' => $doc_id,
            ]);
        return [
            'status' => true,
            'data' => json_decode($response),
            "message" => "Success"
        ];
    }

    public static function backendMobileVerify($data)
    {
        $document = base64_encode($data['document']);
        $document_id = $data['document_id'];
        $ip = $data['ip'];

        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8082/backend/mobile/verify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "documentId=" . $document_id . "&document=" . $document);

        $headers = array();
        $headers[] = 'X-Real-Ip: ' . $ip;
        $headers[] = 'Host: edo.uzautomotors.com';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


        try {
            $result = curl_exec($ch);
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return [
            'status' => true,
            'eimzo_base64' => json_decode($result)->pkcs7Attached,
            "message" => "Success"
        ];
    }

    public static function replaceContractData($id, $content)
    {
        // $answers = ComplaensAnswer::where('document_id', $id)->get();
        $document = self::find($id);


        //if ($document->document_template_id == 552 
        if (in_array($document->document_template_id, [352, 552, 553, 554, 556, 557, 637, 424, 641]) && $document->document_date > '2024-01-31 00:00:00') {
            //dd($document);
            $document_detail = DocumentDetail::where('document_id', $id)->first();
            $document_detail_id = $document_detail->id;
            $document_detail_employees = DocumentDetailEmployee::where('document_detail_id', $document_detail_id)->first();
            if ($document_detail_employees) {
                $employee = Employee::where('id', $document_detail_employees->employee_id)->first();

                //dd($employee);

                $tabNo = $employee->tabel;


                //dd($materialResponsbile);


                $tabnoContent = '';
                $tabnoContent .= '----' .
                    $tabnoContent .= $tabNo;

                //dd($employeeDepartment);

                $positionContent = '';
                $positionContent .= $document_detail_employees->employee_department;
                $positionContent .= ' - ';
                $positionContent .= $document_detail_employees->employee_position;


                $employeeCotent = '';

                $employeeCotent .= '  ';
                $employeeCotent .= $employee['lastname_' . $document->locale];
                $employeeCotent .= ' ';
                $employeeCotent .= $employee['firstname_' . $document->locale];
                $employeeCotent .= ' ';
                $employeeCotent .= $employee['middlename_' . $document->locale];


                $dep = $employee->staff[0]->department;
                $depId = $dep->id;
                //dd($document_detail_employees); 
                $departmentNameString = '  ';
                $departmentNameString .= $document_detail_employees->employee_department;
                $departmentNameString .= ' - ';
                $departmentNameString .= $document_detail_employees->employee_position;
                $employeeDepartment = self::findMyDepartment($dep, $departmentNameString);
                $employeeDepartment .= ' ';



                if ($document->documentStaff && count($document->documentStaff) == 0) {
                    //dd($document->documentStaff.'5');
                    $content = str_replace('@tn', $tabNo, $content);
                    $content = str_replace('@msfio', $employeeCotent, $content);
                    $content = str_replace('@bl', $employeeDepartment, $content);
                    //dd($document->documentStaff);
                } else {
                    //dd($document->documentStaff.'7');                
                    //dd($document->documentStaff); 
                    $positionId = $document->documentStaff[0]->position_id;
                    $newDepartment = $document->documentStaff[0]->department;
                    //dd($positionId);  
                    $newDepartmentName = $newDepartment['name_' . $document->locale];
                    $positionObject = Position::where('id', '=', $positionId)->first();
                    //$positionName=$positionObject->name_uz_latin;
                    $positionName = $positionObject['name_' . $document->locale];
                    $newDepartmentName .= ' ';
                    $newDepartmentName .= $positionName;
                    //dd($newDepartmentName);
                    $employeeNewStaff = self::findNewDepartment($newDepartment, $newDepartmentName);
                    //dd($employeeDepartment);
                    $content = str_replace('@tn', $tabNo, $content);
                    $content = str_replace('@msfio', $employeeCotent, $content);
                    $content = str_replace('@bl', $employeeDepartment, $content);
                    $content = str_replace('@sht', $employeeNewStaff, $content);
                }
            }

            $document = Document::find($id);

            $ddcs = collect($document->documentDetails[0]->documentDetailContents)->filter(function ($item) {
                return !!$item->documentDetailAttribute->replace_attribute;
            });

            if (count($ddcs)) {
                foreach ($ddcs as $key => $value) {
                    $content = str_replace($value->documentDetailAttribute->replace_attribute, $value->value, $content);
                    // dd($content);
                }
            }
        }
        return $content;
    }

    public static function findMyDepartment($dep, $departmentNameString)
    {
        //dd($departmentId); 

        $departmentObject = Department::where('id', $dep->parent_id)

            ->first();

        if ($departmentObject->departmentType->sequence > 3) {
            if ($departmentObject->departmentType->id != 10) {
                $departmentNameString = $departmentObject->name_uz_latin . ' - ' . $departmentNameString;

                return self::findMyDepartment($departmentObject, $departmentNameString);
            } else {
                $departmentObject = Department::where('id', $departmentObject->parent_id)->first();
                $departmentNameString = $departmentObject->name_uz_latin . ' - ' . $departmentNameString;

                return self::findMyDepartment($departmentObject, $departmentNameString);
            }
        } else {
            return $departmentNameString;
        }
    }


    public static function findNewDepartment($newDepartment, $positionName)
    {

        //dd($newDepartment); 


        $departmentObject = Department::where('id', $newDepartment->parent_id)

            ->first();

        //dd($departmentObject);

        if ($departmentObject->departmentType->sequence > 3) {
            if ($departmentObject->departmentType->id != 10) {
                $positionName = $departmentObject->name_uz_latin . ' - ' . $positionName;

                return self::findMyDepartment($departmentObject, $positionName);
            } else {
                $departmentObject = Department::where('id', $departmentObject->parent_id)->first();
                $positionName = $departmentObject->name_uz_latin . ' - ' . $positionName;

                return self::findMyDepartment($departmentObject, $positionName);
            }
        } else {
            return $positionName;
        }
    }
}
