<?php

namespace App\Http\Controllers;

use App\Http\Models\Department;
use App\Http\Models\Document;
use App\Http\Models\DocumentSigner;
use App\Http\Models\ComplaensQuestion;
use App\Http\Models\EmployeeCapital;
use App\Http\Models\ComplaensAnswer;
use App\Http\Models\ComplaensRelative;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;


class ComplaensController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function getExcel(Request $request)
    {
        $page = $request->input('page');
        $locale = $request->input('locale') == 'ru' ? 'uz_cyril' : $request->input('locale');
        $_locale = $request->input('locale');
        $lang = $request->input('lang');
        $locale = $request->input('locale');
        // $tabel = $request->input('tabel');
        // return

        $filter = $request->input('filter');
        $tabel = isset($filter['tabel']) ? $filter['tabel'] : '';
        $doc_number = isset($filter['doc_number']) ? $filter['doc_number'] : '';
        $select = isset($filter['select']) ? $filter['select'] : '';

        $perPage = $request->input('perPage');

        $users = User::with(['createDocument' => function ($q) use ($lang, $locale) {
            $q->select(
                'id',
                'department_id',
                'document_type_id',
                'document_template_id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'status',
                'created_employee_id',
                'base64',
                'pdf_file_name',
                'from_department',
                'from_manager',
                'to_department',
                'to_manager',
                'title',
                'restore'
            )->with(['compleansAnswer' => function ($q) {
                $q->select('document_id', 'employee_id', 'question_id', 'answer');
                $q->whereIn('question_id', [9, 30]);
            }]);
            $q->with(['employee' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'lastname_uz_latin',
                    'middlename_uz_latin',
                    'firstname_uz_latin',
                    'tabel'
                )
                    ->with(['employeeStaff' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'employee_id',
                            'staff_id'
                        )
                            ->with(['staff' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'position_id',
                                    'department_id'
                                )
                                    ->with(['department' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_uz_latin'
                                        );
                                    }])
                                    ->with(['position' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_uz_latin'
                                        );
                                    }]);
                            }])
                            ->where('is_active', 1);
                    }]);
            }])
                ->with('documentDetails:id,document_id')
                ->with(['documentDetails' => function ($query) use ($lang) {
                    $query->select(
                        'id',
                        'document_id',
                        'content'
                    )
                        ->with(['documentDetailEmployees' => function ($query) use ($lang) {
                            $query->select(
                                'id',
                                'document_detail_id',
                                'employee_id'
                            )
                                ->with(['employee' => function ($query) use ($lang) {
                                    $query->select(
                                        'id',
                                        'lastname_uz_latin',
                                        'middlename_uz_latin',
                                        'firstname_uz_latin',
                                        'tabel'
                                    );
                                }]);
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        }])
            ->with(['employee' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'lastname_uz_latin',
                    'middlename_uz_latin',
                    'firstname_uz_latin',
                    'tabel'
                )
                    ->with(['employeeStaff' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'employee_id',
                            'staff_id'
                        )
                            ->with(['staff' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'position_id',
                                    'department_id'
                                )
                                    ->with(['department' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_uz_latin'
                                        );
                                    }])
                                    ->with(['position' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_uz_latin'
                                        );
                                    }]);
                            }])
                            ->where('is_active', 1);
                    }]);
            }])
            ->whereHas('employee', function ($q) use ($tabel) {
                $q->where('is_active', 1);
            });

        if ($select && $select == 1) {
            $users->whereDoesntHave('createDocument');
        }

        if ($tabel) {
            $users->whereHas('employee', function ($q) use ($tabel) {
                $q->where('tabel', 'like', $tabel);
                $q->where('is_active', 1);
            });
            // return $users->get();
        }

        if ($doc_number) {
            $users->whereHas('createDocument', function ($q) use ($doc_number) {
                $q->where('document_number', 'like',  $doc_number);
            });
        }

        // $users->addSelect(\DB::raw("CONCAT('https://b-edo.uzautomotors.com/staffs/file-download/', id) as link"));

        foreach ($users as $key => $value) {

            // $users[$key]['qrCode'] = 5;
            // $signer = $value->createDocument[0] ?  DocumentSigner::where('document_id', $value->createDocument[0]->id)->where('action_type_id', 6)->first() : '';
            array_push($value, (object)[
                "qrCode" => 5
                // '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)
                // ->generate($signer->fio  . ' ' . ($signer->signed_date ? date('Y-m-d H:i', $signer->signed_date) : substr($signer->taken_datetime, 0, 16)))) . '"/>'
            ]);
        }




        return
            $users
            // ->select('users.id', 'users.employee_id', 'users.qrCode')
            ->paginate($perPage, ['*'], 'page name', $page);





        return $users;
        $excel = [];
        $fio = '';
        $tabel = '';
        $staff = '';
        $doc_date = '';
        $predmet = '';
        $doc_number = '';
        $qrCode = '';
        foreach ($users as $key => $value) {
            // dd($value->employee->employeeStaff[0]->staff->department->name_uz_latin);
            $signer = $value->create_document[0] ?  DocumentSigner::where('document_id', $value->create_document[0]->id)->where('action_type_id', 6)->first() : '';
            array_push($excel, (object)[
                "№" => $key + 1 + $page * $perPage - $perPage,
                "FIO" => $value->employee ? $value->employee->fio : '',
                "Tabel raqami" => '*' . $value->employee ? $value->employee->tabel : '',

                // "Xodimining lavozimi" => $value['firstname_' . $locale] . ' ' . $value['lastname_' . $locale] . ' ' . $value['middlename_' . $locale],

                "Xodimining lavozimi" =>
                // $value->employee->employee_staff[0] && $value->employee->employee_staff[0]->staff->department->name_uz_latin ? $value->employee->employee_staff[0]->staff->department->name_uz_latin : '1',
                ($value->employee && $value->employee->employeeStaff && $value->employee->employeeStaff[0] && $value->employee->employeeStaff[0]->staff) ?
                    ($value->employee->employeeStaff[0]->staff->department ? $value->employee->employeeStaff[0]->staff->department->name_uz_latin : '') . ' ' .
                    ($value->employee->employeeStaff[0]->staff->position ? $value->employee->employeeStaff[0]->staff->position->name_uz_latin : '')
                    : '',

                "Ma'lumot olingan sana" => $value->create_document && $value->create_document[0] ? $value->create_document[0]->document_date : '',

                "Manfaatlar to'qnashuvining predmeti" => ($value->predmet = $value->create_document && $value->create_document[0] && $value->create_document[0]->compleans_answer && $value->create_document[0]->compleans_answer[0]) ?
                    ($value->create_document[0]->compleans_answer[0]->answer == 1 ? 'Ha' : 'Yo\'q') : '',

                "Nomzodni tekshirish bo'yicha hisobot raqami" => $value->create_document && $value->create_document[0] ? $value->create_document[0]->document_number : '',

                "QrCode" => ($value->create_document && $value->create_document[0] && $signer) ?

                    // 'data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)
                    // ->generate($signer->fio . ' ' . ($signer->signed_date ? date('Y-m-d H:i', $signer->signed_date) : substr($signer->taken_datetime, 0, 16)))) :

                    '<img width="70" height="70" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)
                        ->generate($signer->fio  . ' ' . ($signer->signed_date ? date('Y-m-d H:i', $signer->signed_date) : substr($signer->taken_datetime, 0, 16)))) . '"/>' :

                    '',

            ]);
        }
        return $excel;
    }
    public function complaensUsers(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $locale = $request->input('language');
        $lang = $request->input('language') == 'ru' ? 'uz_cyril' : $request->input('language');

        $filter = $request->input('filter');
        $tabel = isset($filter['tabel']) ? $filter['tabel'] : '';
        $doc_number = isset($filter['doc_number']) ? $filter['doc_number'] : '';
        $select = isset($filter['select']) ? $filter['select'] : '';

        $users = User::with(['createDocument' => function ($q) use ($lang, $locale) {
            $q->select(
                'id',
                'department_id',
                'document_type_id',
                'document_template_id',
                'document_date',
                'document_date_reg',
                'document_number',
                'document_number_reg',
                'status',
                'created_employee_id',
                'base64',
                'pdf_file_name',
                'from_department',
                'from_manager',
                'to_department',
                'to_manager',
                'title',
                'restore'
            )->with(['compleansAnswer' => function ($q) {
                $q->select('document_id', 'employee_id', 'question_id', 'answer');
                $q->whereIn('question_id', [9, 30]);
            }]);
            $q->with(['employee' => function ($query) use ($lang, $locale) {
                $query->select(
                    'id',
                    'lastname_uz_latin',
                    'middlename_uz_latin',
                    'firstname_uz_latin',
                    // 'lastname_' . $lang,
                    // 'middlename_' . $lang,
                    // 'firstname_' . $lang,
                    'tabel'
                )
                    ->with(['employeeStaff' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'employee_id',
                            'staff_id'
                        )
                            ->with(['staff' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'position_id',
                                    'department_id'
                                )
                                    ->with(['department' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_uz_latin'
                                            // 'name_' . $locale
                                        );
                                    }])
                                    ->with(['position' => function ($query) use ($lang, $locale) {
                                        $query->select(
                                            'id',
                                            'name_uz_latin'
                                            // 'name_' . $locale
                                        );
                                    }]);
                            }])
                            ->where('is_active', 1);
                    }]);
            }])
                ->with('documentDetails:id,document_id')
                // $q->whereIn('status', [1, 2, 3, 4, 5])
                // ->whereHas('documentTemplate', function ($q) {
                //     $q->whereIn('id', [615, 622]);
                // })
                ->with(['documentDetails' => function ($query) use ($lang) {
                    $query->select(
                        'id',
                        'document_id',
                        'content'
                    )
                        ->with(['documentDetailEmployees' => function ($query) use ($lang) {
                            $query->select(
                                'id',
                                'document_detail_id',
                                'employee_id'
                            )
                                ->with(['employee' => function ($query) use ($lang) {
                                    $query->select(
                                        'id',
                                        'lastname_uz_latin',
                                        'middlename_uz_latin',
                                        'firstname_uz_latin',
                                        // 'lastname_' . $lang,
                                        // 'middlename_' . $lang,
                                        // 'firstname_' . $lang,
                                        'tabel'
                                    );
                                }]);
                        }])
                        ->with(['documentDetailContents' => function ($query) {
                            $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                        }]);
                }]);
        }])
            ->with(['employee' => function ($q) use ($tabel) {
                // $q->where('is_active', 1);
                $q->with('staff.department');
                $q->with('staff.position');
            }])
            ->whereHas('employee', function ($q) use ($tabel) {
                $q->where('is_active', 1);
            });

        if ($select && $select == 1) {
            $users->whereDoesntHave('createDocument');
        }

        if ($tabel) {
            $users->whereHas('employee', function ($q) use ($tabel) {
                $q->where('tabel', 'like', $tabel);
                $q->where('is_active', 1);
            });
            // return $users->get();
        }

        if ($doc_number) {
            $users->whereHas('createDocument', function ($q) use ($doc_number) {
                $q->where('document_number', 'like',  $doc_number);
            });
        }


        return $users->paginate($itemsPerPage == '-1' ? 100000 : $itemsPerPage, ['*'], 'page name', $page);
            // 1207
        ;
    }
    public function getQrCode(Request $request)
    {
        $ids = $request['doc_ids'];
        $doc = Document::select('id')->whereIn('id', $ids)->get();
        $data = [];
        foreach ($doc as $key => $d) {
            $fioLatin = '';

            $signer = DocumentSigner::where('document_id', $d->id)->where('action_type_id', 6)->first();
            $fioLatin = $signer->fio;
            // $fioLatin = substr($signer->employeeStaffs->employee['firstname_uz_latin'], 0, 1) . '. ';
            // $fioLatin .= !in_array($signer->signerEmployee['middlename_uz_latin'], ['', ' ']) ? substr($signer->employeeStaffs->employee['middlename_uz_latin'], 0, 1) . '. ' : '';
            // $fioLatin .= $signer->employeeStaffs->employee['lastname_uz_latin'];
            $qrCode = 'data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(80)
                ->generate($fioLatin . ' ' . ($signer->signed_date ? date('Y-m-d H:i', $signer->signed_date) : substr($signer->taken_datetime, 0, 16))));
            $data[] = ['id' => $d->id, 'qrCode' => $qrCode];
        }

        return $data;
    }
    public function departmentSumm(Request $request)
    {
        $locale = $request->input('language');
        $search = $request->input('search');
        // return
        $from_date = $search['from_date'];
        $to_date = $search['to_date'];
        $lang = $request->input('language') == 'ru' ? 'uz_cyril' : $request->input('language');
        $user = User::with('roles')->find(Auth::id());
        // return
        $dep = Department::where('department_type_id', 3)->get();
        // ->with('departmentType')


        $documents = Document::select(
            'id',
            'department_id',
            'document_type_id',
            'document_template_id',
            'document_date',
            'document_date_reg',
            'document_number',
            'document_number_reg',
            'status',
            'created_employee_id',
            'base64',
            'pdf_file_name',
            'from_department',
            'from_manager',
            'to_department',
            'to_manager',
            'title',
            'restore'
        );
        $documents
            ->with(['documentType' => function ($q) use ($locale) {
                $q->select(
                    'id',
                    'name_' . $locale
                );
            }])

            ->with(['department' => function ($q) use ($locale, $lang) {
                $q->select(
                    'id',
                    'manager_staff_id',
                    'name_' . $locale
                );
            }])
            ->with(['documentDetails' => function ($query) use ($lang, $locale) {
                $query
                    ->select(
                        'id',
                        'document_id',
                        'content'
                    )
                    ->with(['documentDetailEmployees' => function ($query) use ($lang, $locale) {
                        $query->select(
                            'id',
                            'document_detail_id',
                            'employee_id'
                        )
                            ->with(['employee' => function ($query) use ($lang, $locale) {
                                $query->select(
                                    'id',
                                    'lastname_' . $lang,
                                    'middlename_' . $lang,
                                    'firstname_' . $lang,
                                    'tabel'
                                );
                            }]);
                    }])
                    ->with(['documentDetailContents' => function ($query) {
                        $query->select('document_detail_id', 'd_d_attribute_id', 'value');
                    }]);
            }]);


        $documents
            ->whereIn('status', [1, 2, 3, 4, 5])
            ->whereHas('documentTemplate', function ($q) {
                $q->whereIn('id', [57, 66, 83, 86, 90, 95, 97, 98, 102, 117, 118, 121, 133, 140, 150, 159, 171, 189, 209, 230, 240, 252]);
            })
            ->with(['documentSigners' => function ($q) use ($lang, $user, $locale) {
                $q->select('id', 'action_type_id', 'document_id', 'status', 'signer_employee_id', 'staff_id')
                    ->whereNotNull('taken_datetime')
                    ->with(['signerEmployee' => function ($q2) use ($lang) {
                        $q2->select(
                            'id',
                            'lastname_' . $lang,
                            'middlename_' . $lang,
                            'firstname_' . $lang,
                            'tabel'
                        );
                    }])
                    ->with(['comments' => function ($comment)  use ($locale) {
                        $comment
                            ->select('id', 'document_signer_id', 'comment');
                    }]);
            }]);

        $documents->where('document_date', '>=',  $from_date);
        $documents->where('document_date', '<=',  $to_date);
        // return
        // $docum = $documents->distinct('from_department')
        // ->get()
        // ;
        $documents = $documents->get();

        $departments_sum = [];

        foreach ($dep as $key => $value) {
            $uzs = [];
            $usd = [];
            $rub = [];
            foreach ($documents as $keys => $doc) {
                if ((int)substr($value->department_code, 0, 1) == 1 || (int)substr($value->department_code, 0, 1) == 9 || (int)substr($value->department_code, 0, 3) == 431 || (int)substr($value->department_code, 0, 3) == 432) {

                    $document_number = (int)substr($doc->document_number, 5, 3);
                    $department_code = (int)substr($value->department_code, 0, 3);
                } else {
                    $document_number = (int)substr($doc->document_number, 5, 2);
                    $department_code = (int)substr($value->department_code, 0, 2);
                }
                // $document_number = (int)substr($doc->document_number, 5, 2);
                // $department_code = (int)substr($value->department_code, 0, 2);
                if ($document_number == $department_code) {
                    // return $doc;
                    $sum = collect($doc->documentDetails[0]->documentDetailContents)->first(function ($val, $k) {
                        return  in_array($val->d_d_attribute_id, [201, 169, 214, 227, 242, 262, 277, 290, 310, 368, 383, 406, 508, 489, 549, 565, 607, 630, 808, 846, 930]) ? $val->value : '';
                    });

                    $currency = collect($doc->documentDetails[0]->documentDetailContents)->first(function ($val, $k) {
                        return  in_array($val->d_d_attribute_id, [1372, 1373, 1374, 1375, 1376, 1377, 1378, 1379, 1380, 1381, 1382, 1383, 1384, 1385, 1386, 1387, 1388, 1389, 1390, 1391, 1392]) ? $val->value : '';
                    });

                    // $curr = [1372,1373,1374,1375,1376,1377,1378,1379,1380,1381,1382,1383,1384,1385,1386,1387,1388,1389,1390,1391,1392];
                    if ($currency && $currency->value == 'RUB ') {
                        str_replace(",", "", $sum->value);
                        $rub[] = str_replace(",", "", $sum->value);
                    } else if ($currency && $currency->value == 'USD ') {
                        str_replace(",", "", $sum->value);
                        $usd[] = str_replace(",", "", $sum->value);
                    } else {
                        str_replace(",", "", $sum->value);
                        $uzs[] = str_replace(",", "", $sum->value);
                    }
                }
            }
            // if((round(array_sum($uzs)) != 0) && (round(array_sum($rub)) != 0) && round(array_sum($usd)) != 0){
            array_push($departments_sum, (object)[
                "dep_name" => $value['name_' . $locale],
                // "dep_type" => $value->departmentType['name_'.$locale],
                "dep_code" => $value->department_code,
                // "cur" => $currency,
                "uzs" => round(array_sum($uzs)),
                "usd" => (array_sum($usd)),
                "rub" => (array_sum($rub))
            ]);
            // }
        }

        return $departments_sum;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getQuestions(Request $request)
    {
        $document_template_id = $request->input('document_template_id');
        $document_id = $request->input('document_id');
        if ($document_id == 1) {
            $questions = ComplaensQuestion::where('document_template_id', $document_template_id)->get();
        } else {
            $questions = ComplaensAnswer::where('document_id', $document_id)->with('questions')->with(['relatives' => function ($q) {
                $q->with(['employee' => function ($q) {
                    $q->with(['mainStaff' => function ($q) {
                        $q->with('department');
                        $q->with('position');
                    }]);
                }]);
                $q->with('relative');
            }])->get();
        }
        $pdf = base64_encode(file_get_contents(storage_path('app/template/complaens.pdf')));
        // $pdf = base64_encode(file_get_contents(storage_path('app/template/complaens_nizom.pdf')));
        // $questions['pdf'] = $pdf;
        return [$questions, $pdf];
    }
    public function getAllQuestions()
    {
        $questions = ComplaensQuestion::get();
        return $questions;
    }
    public function deleteRelative($id)
    {
        $relative = ComplaensRelative::find($id);
        $relative->delete();
    }
    public function addQuestion(Request $request)
    {
        $question = ComplaensQuestion::find($request->input('id'));
        if (!$question) {
            $question = new ComplaensQuestion();
        }
        $question->question = $request->input('question');
        $question->question_type = $request->input('question_type');
        $question->document_template_id = $request->input('document_template_id');
        $question->save();
    }
    public function deleteQuestion($id)
    {
        $relative = ComplaensQuestion::find($id);
        $relative->delete();
    }
    public function resumecapitalupdate(Request $request)
    {
        $form = $request['form'];

        $type = isset($form['type']) ? $form['type'] : '';
        $id = isset($form['id']) ? $form['id'] : time();

        // return
        $legal_name = isset($form['legal_name']) ? $form['legal_name'] : '';
        $legal_register_number = isset($form['legal_register_number']) ? $form['legal_register_number'] : '';
        $capital_register_relatives = isset($form['capital_register_relatives']) ? $form['capital_register_relatives'] : '';
        $capital_register_role = isset($form['capital_register_role']) ? $form['capital_register_role'] : '';
        $capital_register_activity = isset($form['capital_register_activity']) ? $form['capital_register_activity'] : '';

        $capital_organization_name = isset($form['capital_organization_name']) ? $form['capital_organization_name'] : '';
        $organization_register_number = isset($form['organization_register_number']) ? $form['organization_register_number'] : '';
        $ownership_stake = isset($form['ownership_stake']) ? $form['ownership_stake'] : '';
        // dd($id);

        $employeecapital = EmployeeCapital::where('id', $id)->first();

        if (!isset($employeecapital) && !$employeecapital) {
            $employeecapital = new EmployeeCapital();
        }
        $employeecapital->type = $type;
        $employeecapital->employee_id = Auth::user()->employee->id;
        if ($type == 1) {
            $employeecapital->legal_name = $legal_name;
            $employeecapital->legal_register_number = $legal_register_number;
            $employeecapital->capital_register_relatives = $capital_register_relatives;
            $employeecapital->capital_register_role = $capital_register_role;
            $employeecapital->capital_register_activity = $capital_register_activity;
            $employeecapital->save();
        } else if ($type == 2) {
            $employeecapital->capital_organization_name = $capital_organization_name;
            $employeecapital->organization_register_number = $organization_register_number;
            $employeecapital->ownership_stake = $ownership_stake;
            $employeecapital->save();
        }
        if ($employeecapital->save()) {
            return ['status' => 200, 'message' => 'success'];
        } else {
            return ['status' => 500, 'error'];
        }
    }
}
