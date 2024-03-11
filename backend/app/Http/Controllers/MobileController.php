<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Document;
use App\Http\Models\Department;
use App\Http\Models\DocumentSigner;
use App\Http\Models\DocumentSignerEvent;
use App\Http\Models\Employee;
use App\Http\Models\ActionType;
use App\Http\Models\EmployeeStaff;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentBookmark;
use App\Http\Controllers\DocumentSignerController;
use App\Http\Models\Staff;
use Guzzle\Http\Exception\ClientErrorResponseException;
use GuzzleHttp\Exception\BadResponseException;
use App\Http\Models\File;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

use GuzzleHttp\Client as GuzzleClient;

class MobileController extends Controller
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
    public function test(Request $request)
    {
        // $q = $request['message']['a'];
        $qu = $request['message'];
        return
            $qu = explode(",", $qu);
        $s = [];
        foreach ($qu as $q) :
            // return
            $r =  explode(":",  $q);
            $s[] = [$r[0] => $r[1]];

        endforeach;
        return  $s;
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


    public function documentIndex(Request $request)
    {
        // $token =  Auth::user()->ides_token;
        // $client = new GuzzleClient([
        //     'verify' => false
        // ]);
        // $response = $client->request(
        //     'POST',
        //     config('app.IDES_URL') . '/api/documents/index',
        //     [
        //         'form_params' => $request->all(),
        //         'headers' => [
        //             'Authorization' => 'Bearer ' . $token
        //         ]
        //     ]
        // );
        // return $response->getBody()->getContents();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function language($key, $locale)
    {
        $language = [
            'invalid_request' => [
                'ru' => 'Данные введены не полностью',
                'uz' => 'Ma’lumot to‘liq kiritilmagan',
                'cy' => 'Маълумот тўлиқ киритилмаган'
            ],
            'invalid_grant' => [
                'ru' => 'Неверные учетные данные пользователя.',
                'uz' => 'Foydalanuvchi ma\'lumotlari noto\'g\'ri.',
                'cy' => 'Фойдаланувчи маълумотлари нотўғри.'
            ],
            'success' => [
                'ru' => 'Успешный',
                'uz' => 'muvaffaqiyatli',
                'cy' => 'Муваффақиятли'
            ],
            // document count
            'inbox' => [
                'ru' => 'Входящие',
                'uz' => 'Kiruvchi',
                'cy' => 'Кирувчи '
            ],
            'outbox' => [
                'ru' => 'Исходящие',
                'uz' => 'Chiquvchi',
                'cy' => 'Чиқувчи'
            ],
            'draft' => [
                'ru' => 'Черновики',
                'uz' => 'Qoralama',
                'cy' => 'Коралама'
            ],
            'cancel' => [
                'ru' => 'Отмененные',
                'uz' => 'Bekor qilingan',
                'cy' => 'Бекор қилинган'
            ],
            'all' => [
                'ru' => 'Все',
                'uz' => 'Barcha',
                'cy' => 'Барча'
            ],
            // noifications
            'length_nazorat' => [
                'ru' => 'Контроль',
                'uz' => 'Nazorat',
                'cy' => 'Назорат'
            ],
            'length_document_out_one' => [
                'ru' => 'остался 1 день',
                'uz' => '1 kun qolganlar',
                'cy' => '1 кун қолганлар'
            ],
            'length_document_out_two' => [
                'ru' => 'остался 2 день',
                'uz' => '2 kun qolganlar',
                'cy' => '2 кун қолганлар'
            ],
            'length_document_out_three' => [
                'ru' => 'остался 3 день',
                'uz' => '3 kun qolganlar',
                'cy' => '3 кун қолганлар'
            ],
            'length' => [
                'ru' => 'Новые документы',
                'uz' => 'Yangi hujjatlar',
                'cy' => 'Янги ҳужжатлар'
            ],
            'length_results' => [
                'ru' => 'Исполненые резолюции',
                'uz' => 'Yo`naltirish natijalari',
                'cy' => 'Йўналтириш натижалари'
            ],
            'length_resolutions' => [
                'ru' => 'Резолюции',
                'uz' => 'Yo`naltirishlar',
                'cy' => 'Йўналтиришлар'
            ],
            'length_expected' => [
                'ru' => '',
                'uz' => '',
                'cy' => ''
            ],
            'length_star' => [
                'ru' => 'Закладки',
                'uz' => 'Xatcho`plar',
                'cy' => 'Хатчўплар'
            ],
            'length_prosesing' => [
                'ru' => 'В процессе',
                'uz' => 'Jarayonda',
                'cy' => 'Жараёнда'
            ],
            'length_substantiate' => [
                'ru' => 'Обосновывать',
                'uz' => 'Asoslab bering',
                'cy' => 'Асослаб беринг'
            ],
            'length_executor' => [
                'ru' => 'К исполнению',
                'uz' => 'Ijro uchun kelgan hujjatlar',
                'cy' => 'Ижро учун келган ҳужжатлар'
            ],
            'length_expired' => [
                'ru' => 'Просроченные документы',
                'uz' => 'Muddati o`tgan xujjatlar',
                'cy' => 'Муддати ўтган хужжатлар'
            ],
            'length_watcher' => [
                'ru' => 'Просроченные документы',
                'uz' => 'Muddati o`tgan xujjatlar',
                'cy' => 'Муддати ўтган хужжатлар'
            ],
            'length_info' => [
                'ru' => 'Для информации',
                'uz' => 'Ma`lumot uchun',
                'cy' => 'Маълумот учун'
            ],
            'length_canceled' => [
                'ru' => 'Отмененные документы',
                'uz' => 'Bekor qilingan xujjatlar',
                'cy' => 'Бекор қилинган хужжатлар'
            ],
            'length_agreement' => [
                'ru' => 'Отмененные документы',
                'uz' => 'Bekor qilingan xujjatlar',
                'cy' => 'Бекор қилинган хужжатлар'
            ],

            // noifications
        ];
        return $language[$key][$locale];
    }
    public function login(Request $request)
    {
        $username  =  $request['username'];
        $password  =  $request['password'];
        $locale  =  $request['locale'];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        if (!$username || !$password) {
            return [
                'status_code' => 400,
                'message' => $this->language('invalid_request', $locale),
                'data' => ''
            ];
        }
        $arr = [
            'client_id' => '2',
            'client_secret' => 'dAkXImghyu2UeiX0EOA4NsgCUpM6cRvjauQdwdfL',
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password
        ];
        $http = new GuzzleClient;
        try {
            $response = $http->post('https://b-edo.uzautomotors.com/oauth/token', [
                'form_params' => $arr
            ]);
            return [
                'status_code' => 200,
                'message' => $this->language('success', $locale),
                'data' => json_decode((string) $response->getBody(), true)
            ];
        } catch (BadResponseException $ex) {
            return [
                'status_code' => 400,
                'message' => $this->language('invalid_grant', $locale),
                'data' => ''
            ];
            // $response = $ex->getResponse();
            // $jsonBody = (string) $response->getBody();
        }
    }

    public function refreshToken(Request $request)
    {
        $http = new GuzzleClient;

        $response = $http->post('https://b-edo.uzautomotors.com/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $request['refresh_token'],
                'client_id' => '2',
                'client_secret' => 'dAkXImghyu2UeiX0EOA4NsgCUpM6cRvjauQdwdfL',
                'scope' => '',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }
    public function countDocument($key)
    {
        $user = Auth::user();
        $staff_ids = $user->employee->staff->pluck('id')->toArray();
        switch ($key) {
            case "inbox":
                return
                    $inbox = Document::whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
                        $qu->whereIn('staff_id', $staff_ids)
                            ->whereNotIn('action_type_id', [6, 5, 3])
                            ->whereNotNull('taken_datetime')
                            ->where('signer_employee_id', $user->employee_id)

                            ->orWhereIn('staff_id', $staff_ids)
                            ->whereNotIn('action_type_id', [6, 5, 3])
                            ->whereNotNull('taken_datetime')
                            ->whereNull('signer_employee_id');
                    })
                    ->whereNotIn('status', [0, 6])
                    ->count();
                break;
            case "outbox":
                return
                    $outbox = Document::whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
                        $qu->whereIn('staff_id', $staff_ids)
                            ->whereIn('action_type_id', [6, 3])
                            ->whereNotNull('taken_datetime')
                            ->where('signer_employee_id', $user->employee_id)

                            ->orWhereIn('staff_id', $staff_ids)
                            ->whereIn('action_type_id', [6, 3])
                            ->whereNotNull('taken_datetime')
                            ->whereNull('signer_employee_id');
                    })
                    ->whereNotIn('status', [0, 6])
                    ->count();
                break;
            case "draft":
                return
                    $draft = Document::whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
                        $qu->where('action_type_id', 6)
                            ->where('signer_employee_id', $user->employee_id);
                    })
                    ->where('status', 0)
                    ->count();
                break;
            default:
                return
                    $cancel = Document::whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
                        $qu->where('signer_employee_id', $user->employee_id);
                    })
                    ->whereHas('documentSigners', function ($query) {
                        $query->where('status', 2)
                            ->whereNull('parent_employee_id');
                    })
                    ->where('status', 6)
                    ->count();
        }
    }
    public function getDashboard(Request $request)
    {
        $locale  =  $request['locale'];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        // $user = Auth::user();
        // $staff_ids = $user->employee->staff->pluck('id')->toArray();

        $inbox = $this->countDocument('inbox');
        $outbox = $this->countDocument('outbox');
        $draft = $this->countDocument('draft');
        $cancel = $this->countDocument('cancel');


        $length = strlen(max([$inbox, $outbox, $draft, $cancel]));
        $a = 1;

        for ($x = 1; $x <= $length; $x++) {
            $a = $a * 10;
        }

        // return  [$inbox/$a,$outbox/$a,$draft/$a,$cancel/$a];


        return [
            'status_code' => 200,
            'message' => $this->language('success', $locale),
            'data' =>  [
                [
                    'name' => 'inbox',
                    'title' => $this->language('inbox', $locale),
                    'color' => '#0000FF',
                    'count' => $inbox / $a
                ],
                [
                    'name' => 'outbox',
                    'title' => $this->language('outbox', $locale),
                    'color' => '#008000',
                    'count' => $outbox / $a
                ],
                [
                    'name' => 'draft',
                    'title' => $this->language('draft', $locale),
                    'color' => '#FFFF00',
                    'count' => $draft / $a
                ],
                [
                    'name' => 'cancel',
                    'title' => $this->language('cancel', $locale),
                    'color' => '#FF0000',
                    'count' => $cancel / $a
                ],
            ],
        ];
    }

    public function getDocumentCount(Request $request)
    {
        $locale  =  $request['locale'];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        $user = Auth::user();
        $staff_ids = $user->employee->staff->pluck('id')->toArray();
        // inbox
        $inbox_new = Document::whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
            $qu->whereIn('staff_id', $staff_ids)
                ->whereNotIn('action_type_id', [6, 5, 3])
                ->whereNotNull('taken_datetime')
                ->whereNotNull('parent_employee_id')
                ->where('signer_employee_id', $user->employee_id)
                ->whereIn('status', [0, 3])

                ->orWhereNull('parent_employee_id')
                ->whereIn('staff_id', $staff_ids)
                ->whereNotIn('action_type_id', [6, 5, 3])
                ->whereNotNull('taken_datetime')
                ->whereIn('status', [0, 3]);
        })
            ->whereIn('status', [1, 2, 3, 4])
            ->count();
        // inbox
        // outbox
        $outbox_new = Document::whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
            $qu->whereIn('staff_id', $staff_ids)
                ->whereNotNull('taken_datetime')
                ->where('sequence', 100)
                ->whereIn('status', [0, 3])
                ->whereNotNull('parent_employee_id')
                ->where('signer_employee_id', $user->employee_id)

                ->orWhereNull('parent_employee_id')
                ->whereIn('staff_id', $staff_ids)
                ->whereNotNull('taken_datetime')
                ->where('sequence', 100)
                ->whereIn('status', [0, 3]);
        })
            ->whereNotIn('status', [0, 6])
            ->count();

        // outbox
        // draft
        // draft
        // cancel
        // cancel
        return [
            'status_code' => 200,
            'message' => $this->language('success', $locale),
            'data' =>  [
                [
                    'name' => 'all',
                    'title' => $this->language('all', $locale),
                    'count' => $inbox_new + $outbox_new,
                    'all_count' => ($this->countDocument('inbox') + $this->countDocument('outbox'))
                ],
                [
                    'name' => 'inbox',
                    'title' => $this->language('inbox', $locale),
                    'count' => $inbox_new,
                    'all_count' => $this->countDocument('inbox')
                ],
                [
                    'name' => 'outbox',
                    'title' => $this->language('outbox', $locale),
                    'count' => $outbox_new,
                    'all_count' => $this->countDocument('outbox')
                ],
                [
                    'name' => 'draft',
                    'title' => $this->language('draft', $locale),
                    // 'count' => $draft,
                    'count' => $this->countDocument('draft'),
                    'all_count' => $this->countDocument('draft')
                ],
                [
                    'name' => 'cancel',
                    'title' => $this->language('cancel', $locale),
                    // 'count' => $cancel,
                    'count' => $this->countDocument('cancel'),
                    'all_count' => $this->countDocument('cancel')
                ],
            ],
        ];
    }


    public function getNotification(Request $request)
    {

        $locale  =  $request['locale'];

        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }

        $user = User::with('roles.permissions:name')->where('id', Auth::id())->first();
        $userStaff = EmployeeStaff::where('employee_id', '=', $user['employee_id'])->where('is_active', 1)->select('staff_id', 'employee_id')->get();
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        // return $userStaff[0]->employee_id; count

        $length = Document::whereHas('documentSigners', function ($query) use ($userStaffIds) {
            $query->select('id')->whereIn('staff_id', $userStaffIds)
                ->where(function ($q) {
                    $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('signer_employee_id');
                })
                ->where(function ($q) {
                    $q->whereNotNull('parent_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('parent_employee_id');
                })
                ->whereNotIn('action_type_id', [4, 11])
                ->whereNotNull('taken_datetime')
                ->where('status', 0);
        })
            ->whereNotIn('status', [0, 6])
            ->select(['id']);

        $length_resolutions = Document::whereHas('documentSigners', function ($query) use ($userStaff) {
            $query->select('id')->where(function ($q) use ($userStaff) {
                $q->where('signer_employee_id', $userStaff[0]->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 2);
            });
        })
            ->whereNotIn('status', [0, 6])
            ->select(['id']);
        $one_date = date('Y-m-d', strtotime('+2 days'));
        $two_date = date('Y-m-d', strtotime('+3 days'));
        $three_date = date('Y-m-d', strtotime('+4 days'));


        $length_document_out_three = Document::whereHas('DocumentSigners', function ($qr) use ($userStaff,  $three_date, $two_date) {
            $qr
                ->where('signer_employee_id', Auth::user()->employee_id)
                ->whereBetween('due_date', [$two_date, $three_date])
                ->whereIn('status', [0, 3]);
        })

            ->whereNotIn('status', [0, 6])
            ->select(['id']);
        $length_document_out_two = Document::whereHas('DocumentSigners', function ($qr) use ($userStaff,  $one_date, $two_date) {
            $qr
                ->where('signer_employee_id', Auth::user()->employee_id)
                ->whereBetween('due_date', [$one_date, $two_date])
                ->whereIn('status', [0, 3]);;
        })

            ->whereNotIn('status', [0, 6])
            ->select(['id']);
        $length_document_out_one = Document::whereHas('DocumentSigners', function ($qr) use ($userStaff,  $one_date, $two_date) {
            $qr->where('signer_employee_id', Auth::user()->employee_id)
                ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                ->whereIn('status', [0, 3]);
        })
            ->whereNotIn('status', [0, 6])
            ->select(['id']);


        $length_results = Document::whereHas('documentSigners', function ($query) use ($userStaff) {
            $query->select('id')->where(function ($q) use ($userStaff) {
                $q->where('signer_employee_id', $userStaff[0]->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 1);
            });
        })
            ->where('status', '!=', 6)
            ->select(['id']);

        $length_prosesing = Document::whereHas('documentSigners', function ($query) use ($userStaff) {
            $query->select('id')->where(function ($q) use ($userStaff) {
                $q->where('signer_employee_id', $userStaff[0]->employee_id)
                    ->where('status', 3)
                    ->where('is_done', 0);
            });
        })
            ->where('status', '!=', 6)
            ->select(['id']);

        $length_expected = Document::where(function ($q) {
            $q->whereHas('documentSigners', function ($q1) {
                $q1->select('id')
                    ->where('staff_id', 1)
                    ->whereNull('taken_datetime')
                    ->where('document_signers.status', 0);
            });
        })->whereIn('documents.status', [1, 2])
            ->orderBy('documents.document_date', 'desc')
            ->select(['id']);

        $length_star = Document::whereIn('id', collect(DocumentBookmark::where('user_id', Auth::id())->get())->pluck('document_id'))
            ->orderBy('documents.document_date', 'desc')
            ->select(['id']);

        $length_substantiate = Document::with(['documentType' => function ($q) {
            $q->select('id');
        }])
            ->whereHas('documentSigners', function ($query) use ($userStaff) {
                $query->select('id')->where(function ($q) use ($userStaff) {
                    $q->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->where('status', 4)
                        ->whereIn('is_done', [0, 1]);
                });
            })
            ->where('status', '!=', 6)
            ->select(['id']);

        $length_executor = Document::whereHas('documentSigners', function ($query) use ($userStaffIds) {
            $query->select('id')->whereIn('staff_id', $userStaffIds)
                ->where(function ($q) {
                    $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('signer_employee_id');
                })
                ->where('action_type_id', 4)
                ->whereNotNull('taken_datetime')
                ->where('status', 0);
        })
            ->where('status', '!=', 6)
            ->select(['id']);

        $length_info = Document::where(function ($query) use ($userStaffIds) {
            $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                $q->select('id')->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', 5);
            })
                ->whereHas('documentSigners', function ($q) {
                    $q->select('id')->whereIn('status', [0, 3, 4]);
                });
        })
            ->whereNotIn('status', [0, 6])
            ->select(['id']);

        $length_nazorat = Document::where(function ($query) use ($userStaffIds) {
            $query->whereHas('documentSigners', function ($q) use ($userStaffIds) {
                $q->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', 11)
                    ->whereIn('status', [0, 3, 4])
                    ->orWhereHas('parentNazorat')
                    ->whereIn('staff_id', $userStaffIds)
                    ->where('action_type_id', 4)
                    ->whereIn('status', [0, 3, 4]);
            });
        })
            ->whereNotIn('status', [0, 4, 5, 6])
            ->where('document_date', '>', '2022-01-01')
            ->select(['id']);


        $length_expired = Document::whereHas('documentSigners', function ($query) use ($userStaffIds) {
            $query->select('id')->whereIn('staff_id', $userStaffIds)
                ->where(function ($q) {
                    $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('signer_employee_id')
                        ;
                })
                ->where(function ($q) {
                    $q->whereNotNull('parent_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('parent_employee_id')
                        ;
                })
                // ->where('action_type_id', '!=', 4)
                ->whereNotNull('taken_datetime')
                ->where('due_date', '<', date("Y-m-d H:i:s"))
                ->whereIn('status', [0, 3]);
        })
            ->whereNotIn('status', [0, 6])
            ->select(['id']);
            // dd($length_expired->get());

        $length_watcher = Document::whereHas('documentSigners', function ($query) use ($userStaff) {
            $query->select('id')->where(function ($q) use ($userStaff) {
                foreach ($userStaff as $key => $value) {
                    $q->orWhere('staff_id', $value->staff_id);
                }
                return $q;
            })
                ->where(function ($q) use ($userStaff) {
                    $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->orWhereNull('signer_employee_id');
                })
                ->where(function ($q) use ($userStaff) {
                    $q->whereNotNull('parent_employee_id')
                        ->where('signer_employee_id', $userStaff[0]->employee_id)
                        ->orWhereNull('parent_employee_id');
                })
                ->where('action_type_id', 11)
                ->whereNotNull('taken_datetime')
                ->whereIn('status', [0, 3, 4]);
        })
            ->where('status', '!=', 6)
            ->select(['id']);

        $length_canceled = Document::whereHas('documentSigners', function ($query) use ($userStaff) {
            $query->select('id')->where(function ($q) use ($userStaff) {
                foreach ($userStaff as $key => $value) {
                    $q->orWhere('staff_id', $value->staff_id);
                }
                return $q;
            });
        })
            ->whereHas('documentSigners', function ($query) {
                $query->where('status', 2)->whereNull('parent_employee_id');
            })
            ->whereHas('documentSigners', function ($q) {
                $q->select('id')
                    ->where('signer_employee_id', Auth::user()->employee_id);
            })
            ->whereDoesntHave('CancelledDocument', function ($q) {
                $q->where('user_id', Auth::id());
            })
            // ->where('created_at', '>', '2021-09-01')
            ->whereIn('status', [1, 2, 6])
            ->where('created_at', '>', '2022-12-01')
            ->select(['id']);

        $length_agreement = Document::where(function ($q) use ($userStaffIds) {
            $q->whereHas('documentSigners', function ($query) use ($userStaffIds) {
                $query->select('id')->whereIn('staff_id', $userStaffIds)->where('status', 5);
            })
                ->orWhere(function ($q1) use ($userStaffIds) {
                    $q1->whereHas('documentSigners', function ($query) use ($userStaffIds) {
                        $query->select('id')->whereIn('staff_id', $userStaffIds)->where('action_type_id', 6);
                    })
                        ->whereDoesntHave('documentSigners', function ($query) {
                            $query->where('status', 5);
                        });
                });
        })
            ->where('status', 7)
            ->select(['id']);


        $data =
            [
                [
                    'count' => $length_nazorat->count(),
                    'name' =>  'length_nazorat',
                    // 'id' => 0,
                    'title' =>  $this->language('length_nazorat', $locale),
                    'color' => '#FF0000',
                    // 'image_url' => 'https://edo.uzautomotors.com/img/notification_json/search.json'
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/checklist.json'
                ],
                [
                    'count' => $length_document_out_one->count(),
                    'name' =>  'length_document_out_one',
                    // 'id' => 1,
                    'title' =>  $this->language('length_document_out_one', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/one.json'
                ],
                [
                    'count' => $length_document_out_two->count(),
                    'name' =>  'length_document_out_two',
                    // 'id' => 2,
                    'title' =>  $this->language('length_document_out_two', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/two.json'
                ],
                [
                    'count' => $length_document_out_three->count(),
                    'name' =>  'length_document_out_three',
                    // 'id' => 3,
                    'title' =>  $this->language('length_document_out_three', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/three.json'
                ],
                [
                    'count' => $length->count(),
                    'name' =>  'length',
                    // 'id' => 4,
                    'title' =>  $this->language('length', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/message.json'
                ],
                [
                    'count' => $length_results->count(),
                    'name' =>  'length_results',
                    // 'id' => 5,
                    'title' =>  $this->language('length_results', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/bell-check.json'
                ],
                [
                    'count' => $length_resolutions->count(),
                    'name' =>  'length_resolutions',
                    // 'id' => 6,
                    'title' =>  $this->language('length_resolutions', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/bell-add.json'
                ],
                // [
                //     'count' => $length_expected->count(),
                //     'name' =>  'length_expected',
                //     // 'id' => 7,
                //     'title' =>  $this->language('length_expected', $locale),
                //     'color' => '#FF0000',
                //     'image_url' => ''
                // ],
                [
                    'count' => $length_star->count(),
                    'name' =>  'length_star',
                    // 'id' => 8,
                    'title' =>  $this->language('length_star', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/star.json'
                ],
                [
                    'count' => $length_prosesing->count(),
                    'name' =>  'length_prosesing',
                    // 'id' => 9,
                    'title' =>  $this->language('length_prosesing', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/timer.json'
                ],
                [
                    'count' => $length_substantiate->count(),
                    'name' =>  'length_substantiate',
                    // 'id' => 10,
                    'title' =>  $this->language('length_substantiate', $locale),
                    'color' => '#FF0000',
                    'image_url' => ''
                ],
                [
                    'count' => $length_executor->count(),
                    'name' =>  'length_executor',
                    // 'id' => 11,
                    'title' =>  $this->language('length_executor', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/lightning.json'
                ],
                [
                    'count' => $length_expired->count(),
                    'name' =>  'length_expired',
                    // 'id' => 12,
                    'title' =>  $this->language('length_expired', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/fire.json'
                ],
                [
                    'count' => $length_watcher->count(),
                    'name' =>  'length_watcher',
                    // 'id' => 13,
                    'title' =>  $this->language('length_watcher', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/search.json'
                ],
                [
                    'count' => $length_info->count(),
                    'name' =>  'length_info',
                    // 'id' => 14,
                    'title' =>  $this->language('length_info', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/info.json'
                ],
                [
                    'count' => $length_canceled->count(),
                    'name' =>  'length_canceled',
                    // 'id' => 15,
                    'title' =>  $this->language('length_canceled', $locale),
                    'color' => '#FF0000',
                    'image_url' => 'https://edo.uzautomotors.com/img/notification_json/blocked-file.json'
                    // 'image_url' => 'https://edo.uzautomotors.com/img/notification_json/checklist.json'
                ],
                [
                    'count' => $length_agreement->count(),
                    'name' =>  'length_agreement',
                    // 'id' => 16,
                    'title' =>  $this->language('length_agreement', $locale),
                    'color' => '#FF0000',
                    'image_url' => ''
                ],
            ];
        $a = [];
        foreach ($data as $key => $item) {
            if ($item['count'] < 0 || empty($item['count'])) {
                unset($data[$key]);
            } else {
                $a[] = [
                    'count'     => $item['count'],
                    'name'      => $item['name'],
                    'title'     => $item['title'],
                    'color'     => $item['color'],
                    'image_url' => $item['image_url'],
                ];
            }
        }


        return
            [
                'status_code' => 200,
                'message' => $this->language('success', $locale),
                'data' => $a
                // 'data' => [$data->where('count', '>', 0)->toArray()]
            ];


        // return [
        //     'status_code' => 200,
        //     'message' => $this->language('success', $locale),
        //     'data' =>    [
        //         [
        //             'count' => $length_nazorat->count(),
        //             'name' =>  'length_nazorat',
        //             // 'id' => 0,
        //             'title' =>  $this->language('length_nazorat', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/04.svg'
        //         ],
        //         [
        //             'count' => $length_document_out_one->count(),
        //             'name' =>  'length_document_out_one',
        //             // 'id' => 1,
        //             'title' =>  $this->language('length_document_out_one', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/03.svg'
        //         ],
        //         [
        //             'count' => $length_document_out_two->count(),
        //             'name' =>  'length_document_out_two',
        //             // 'id' => 2,
        //             'title' =>  $this->language('length_document_out_two', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => ''
        //         ],
        //         [
        //             'count' => $length_document_out_three->count(),
        //             'name' =>  'length_document_out_three',
        //             // 'id' => 3,
        //             'title' =>  $this->language('length_document_out_three', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => ''
        //         ],
        //         [
        //             'count' => $length->count(),
        //             'name' =>  'length',
        //             // 'id' => 4,
        //             'title' =>  $this->language('length', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/01.svg'
        //         ],
        //         [
        //             'count' => $length_results->count(),
        //             'name' =>  'length_results',
        //             // 'id' => 5,
        //             'title' =>  $this->language('length_results', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/08.svg'
        //         ],
        //         [
        //             'count' => $length_resolutions->count(),
        //             'name' =>  'length_resolutions',
        //             // 'id' => 6,
        //             'title' =>  $this->language('length_resolutions', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/09.svg'
        //         ],
        //         [
        //             'count' => $length_expected->count(),
        //             'name' =>  'length_expected',
        //             // 'id' => 7,
        //             'title' =>  $this->language('length_expected', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => ''
        //         ],
        //         [
        //             'count' => $length_star->count(),
        //             'name' =>  'length_star',
        //             // 'id' => 8,
        //             'title' =>  $this->language('length_star', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/02.svg'
        //         ],
        //         [
        //             'count' => $length_prosesing->count(),
        //             'name' =>  'length_prosesing',
        //             // 'id' => 9,
        //             'title' =>  $this->language('length_prosesing', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/11.svg'
        //         ],
        //         [
        //             'count' => $length_substantiate->count(),
        //             'name' =>  'length_substantiate',
        //             // 'id' => 10,
        //             'title' =>  $this->language('length_substantiate', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => ''
        //         ],
        //         [
        //             'count' => $length_executor->count(),
        //             'name' =>  'length_executor',
        //             // 'id' => 11,
        //             'title' =>  $this->language('length_executor', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/10.svg'
        //         ],
        //         [
        //             'count' => $length_expired->count(),
        //             'name' =>  'length_expired',
        //             // 'id' => 12,
        //             'title' =>  $this->language('length_expired', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/06.svg'
        //         ],
        //         [
        //             'count' => $length_watcher->count(),
        //             'name' =>  'length_watcher',
        //             // 'id' => 13,
        //             'title' =>  $this->language('length_watcher', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/07.svg'
        //         ],
        //         [
        //             'count' => $length_info->count(),
        //             'name' =>  'length_info',
        //             // 'id' => 14,
        //             'title' =>  $this->language('length_info', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/12.svg'
        //         ],
        //         [
        //             'count' => $length_canceled->count(),
        //             'name' =>  'length_canceled',
        //             // 'id' => 15,
        //             'title' =>  $this->language('length_canceled', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => 'https://edo.uzautomotors.com/img/notifications/05.svg'
        //         ],
        //         [
        //             'count' => $length_agreement->count(),
        //             'name' =>  'length_agreement',
        //             // 'id' => 16,
        //             'title' =>  $this->language('length_agreement', $locale),
        //             'color' => '#FF0000',
        //             'image_url' => ''
        //         ],
        //     ]
        // ];
    }

    // public function getProfileImage($tabel)
    // {
    //     // return 1;
    //     // $tabel  =  $request['tabel'];

    //     $filename11 = url('storage/avatars/' . $tabel . '.jpg');
    //     $filename22 = url('storage/avatars/' . $tabel . '.JPG');


    //     $filename1 = Storage::path('avatars/' . $tabel . '.jpg');
    //     $filename2 = Storage::path('avatars/' . $tabel . '.JPG');
    //     if (file_exists($filename1)) {
    //         return file_get_contents($filename11);
    //     }
    //     if (file_exists($filename2)) {
    //         return file_get_contents($filename22);
    //     }
    //     return null;
    // }

    public function getTemplates(Request $request)
    {
        $locale  =  $request['locale'];
        $loc = [
            'ru' => 'ru',
            'uz' => 'uz_latin',
            'cy' => 'uz_cyril'
        ];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        return
            $template = DocumentTemplate::select('id', 'name_' . $loc[$locale] . ' as name')->get();
    }
    public function getEmployee(Request $request)
    {
        $locale  =  $request['locale'];
        $search = $request->input('search');
        $loc = [
            'ru' => 'ru',
            'uz' => 'uz_latin',
            'cy' => 'uz_cyril'
        ];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        $lang = $locale == 'ru' ? 'uz_cyril' : $loc[$locale];


        $employees = Employee::select([
            'id', 'tabel', 'firstname_' . $lang . ' as firstname',
            'lastname_' . $lang . ' as lastname', 'middlename_' . $lang . ' as middlename'
        ])
            ->with(['mainStaff' => function ($q) use ($loc, $locale) {
                $q->select('staff.id', 'department_id', 'position_id', 'staff.is_active')
                    ->with(['position' => function ($q) use ($loc, $locale) {
                        $q->select('positions.id', 'name_' . $loc[$locale] . ' as name');
                    }])
                    ->with(['department' => function ($q) use ($loc, $locale) {
                        $q->select('departments.id', 'name_' . $loc[$locale] . ' as name');
                    }]);
            }])
            ->where('is_active', 1);

        if (isset($search) && $search) :
            $employees->where(DB::raw("concat(employees.lastname_" . $lang . ",' ', employees.firstname_" . $lang . ",' ', employees.middlename_" . $lang . ")"), 'ilike', "%" . $search . "%")
                ->orWhere(DB::raw("concat(employees.firstname_" . $lang . ", ' ', employees.lastname_" . $lang . ", ' ', employees.middlename_" . $lang . ")"), 'ilike', "%" . $search . "%")
                ->orWhere('tabel', 'ilike', '%' . $search . '%')
                ->orWhereHas('mainstaff', function ($q) use ($loc, $locale, $search) {
                    $q->select('staff.id', 'department_id', 'position_id', 'staff.is_active')
                        ->whereHas('position', function ($q) use ($loc, $locale, $search) {
                            $q->select('name_' . $loc[$locale] . ' as name')
                                ->where('name_' . $loc[$locale], 'ilike', '%' . $search . '%');
                        })
                        ->orWhereHas('department', function ($q) use ($loc, $locale, $search) {
                            $q->select('name_' . $loc[$locale] . ' as name')
                                ->where('name_' . $loc[$locale], 'ilike', '%' . $search . '%');
                        });
                })
                // (DB::raw("concat(position.name_uz_latin, ' ', department.name_uz_latin)"), 'ilike', "%" . $search . "%")
                // ->orWhere(DB::raw("concat(position.name_uz_cyril, ' ', department.name_uz_cyril)"), 'ilike', "%" . $search . "%")
                // ->orWhere(DB::raw("concat(position.name_ru, ' ', department.name_ru)"), 'ilike', "%" . $search . "%")
            ;
        endif;
        return    $employees->paginate(20);
    }
    public function getDocuments(Request $request)
    {
        // return
        $locale  =  $request['locale'];
        $loc = [
            'ru' => 'ru',
            'uz' => 'uz_latin',
            'cy' => 'uz_cyril',
        ];


        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }

        // return Auth::user()->employee_id;
        $lang = $locale == 'ru' ? 'uz_cyril' : $loc[$locale];

        $filter = $request->input('filter');
        $id = isset($filter['id']) ? $filter['id'] : '';
        $document_template_id = isset($filter['document_template_id']) ? $filter['document_template_id'] : '';
        $documentDateOne = isset($filter['documentDateOne']) ? $filter['documentDateOne'] : '';
        $documentDateTwo = isset($filter['documentDateTwo']) ? $filter['documentDateTwo'] : '';
        $document_number = isset($filter['document_number']) ? $filter['document_number'] : '';
        $document_status = isset($filter['document_status']) ? $filter['document_status'] : '';
        $created_employee_id = isset($filter['created_employee_id']) ? $filter['created_employee_id'] : '';
        $title = isset($filter['title']) ? $filter['title'] : '';

        $itemsPerPage = $request->input('pagination')['itemsPerPage'];

        $name = $request->input('name');

        $user = Auth::user();
        $staff_ids = $user->employee->staff->pluck('id')->toArray();
        $userStaff = EmployeeStaff::where('employee_id', '=', $user['employee_id'])->where('is_active', 1)->select('staff_id', 'employee_id')->get();


        $documents = Document::select(
            'id',
            // 'department_id',
            'document_template_id',
            'document_date',
            'document_number',
            'status',
            'created_employee_id',
            'pdf_file_name',
            'title',
            // 'document_type_id',
            // 'document_date_reg',
            // 'document_number_reg',
            // 'base64',
            'from_department',
            'from_manager',
            'to_department',
            'to_manager',

            DB::raw("CONCAT(from_department, ' ', from_manager) AS from_dep"),
            DB::raw("CONCAT(to_department, ' ', to_manager) AS to_dep")
            // 'restore'
        );

        // $documents->sa = 'sa';

        $documents->with('documentTemplate:id,name_' . $loc[$locale] . ' as name')
            ->with(['employee' => function ($query) use ($lang) {
                $query->select(
                    'id',
                    'lastname_' . $lang . ' as lastname',
                    'middlename_' . $lang . ' as middlename',
                    'firstname_' . $lang . ' as firstname',
                    'tabel'
                )
                    // ->with(['employeeStaff' => function ($query) {
                    //     $query->select(
                    //         'employee_id',
                    //         'staff_id'
                    //     )
                    //         ->with(['staff' => function ($query) {
                    //             $query->select(
                    //                 'id',
                    //                 'position_id',
                    //                 'department_id'
                    //             );
                    //         }])
                    //         ->where('is_active', 1);
                    // }])
                ;
            }]);

        $one_date = date('Y-m-d', strtotime('+2 days'));
        $two_date = date('Y-m-d', strtotime('+3 days'));
        $three_date = date('Y-m-d', strtotime('+4 days'));

        switch ($name) {
            case 'all':
                $documents = $documents->whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
                    $qu->whereIn('staff_id', $staff_ids)
                        // ->whereNotIn('action_type_id', [6, 5, 3])
                        ->whereNotNull('taken_datetime')
                        ->where('signer_employee_id', $user->employee_id)

                        ->orWhereIn('staff_id', $staff_ids)
                        // ->whereNotIn('action_type_id', [6, 5, 3])
                        ->whereNotNull('taken_datetime')
                        ->whereNull('signer_employee_id');
                })
                    ->whereNotIn('status', [0, 6]);
                break;
            case 'inbox':
                $documents = $documents->whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
                    $qu->whereIn('staff_id', $staff_ids)
                        ->whereNotIn('action_type_id', [6, 5, 3])
                        ->whereNotNull('taken_datetime')
                        ->where('signer_employee_id', $user->employee_id)

                        ->orWhereIn('staff_id', $staff_ids)
                        ->whereNotIn('action_type_id', [6, 5, 3])
                        ->whereNotNull('taken_datetime')
                        ->whereNull('signer_employee_id');
                })
                    ->whereNotIn('status', [0, 6]);
                break;
            case "outbox":
                $documents = $documents->whereHas('documentSigners', function ($qu) use ($staff_ids, $user) {
                    $qu->whereIn('staff_id', $staff_ids)
                        ->whereIn('action_type_id', [6, 3])
                        ->whereNotNull('taken_datetime')
                        ->where('signer_employee_id', $user->employee_id)

                        ->orWhereIn('staff_id', $staff_ids)
                        ->whereIn('action_type_id', [6, 3])
                        ->whereNotNull('taken_datetime')
                        ->whereNull('signer_employee_id');
                })
                    ->whereNotIn('status', [0, 6]);
                break;
            case "draft":
                $documents = $documents->whereHas('documentSigners', function ($qu) use ($user) {
                    $qu->where('action_type_id', 6)
                        ->where('signer_employee_id', $user->employee_id);
                })
                    ->where('status', 0);
                break;
            case "cancel":
                $documents = $documents->whereHas('documentSigners', function ($qu) use ($user) {
                    $qu->where('signer_employee_id', $user->employee_id);
                })
                    ->where('status', 6);
                break;

                // notification

            case "length":
                $documents = $documents->whereHas('documentSigners', function ($query) use ($staff_ids) {
                    $query->select('id')->whereIn('staff_id', $staff_ids)
                        ->where(function ($q) {
                            $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', Auth::user()->employee_id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->where(function ($q) {
                            $q->whereNotNull('parent_employee_id')
                                ->where('signer_employee_id', Auth::user()->employee_id)
                                ->orWhereNull('parent_employee_id');
                        })
                        ->whereNotIn('action_type_id', [4, 11])
                        ->whereNotNull('taken_datetime')
                        ->where('status', 0);
                })
                    ->whereNotIn('status', [0, 6]);
                break;
            case "length_resolutions":
                $documents = $documents->whereHas('documentSigners', function ($query) {
                    $query->select('id')->where(function ($q) {
                        $q->where('signer_employee_id', Auth::user()->employee_id)
                            ->where('status', 3)
                            ->where('is_done', 2);
                    });
                })
                    ->whereNotIn('status', [0, 6]);
                break;
            case "length_document_out_three":
                $documents = $documents->whereHas('DocumentSigners', function ($qr) use ($three_date, $two_date) {
                    $qr->where('signer_employee_id', Auth::user()->employee_id)
                        ->whereBetween('due_date', [$two_date, $three_date])
                        ->whereIn('status', [0, 3]);
                })

                    ->whereNotIn('status', [0, 6]);
                break;
            case "length_document_out_two":
                $documents = $documents->whereHas('DocumentSigners', function ($qr) use ($one_date, $two_date) {
                    $qr->where('signer_employee_id', Auth::user()->employee_id)
                        ->whereBetween('due_date', [$one_date, $two_date])
                        ->whereIn('status', [0, 3]);;
                })

                    ->whereNotIn('status', [0, 6]);
                break;
            case "length_document_out_one":
                $documents = $documents->whereHas('DocumentSigners', function ($qr) use ($one_date) {
                    $qr->where('signer_employee_id', Auth::user()->employee_id)
                        ->whereBetween('due_date', [date('Y-m-d'), $one_date])
                        ->whereIn('status', [0, 3]);
                })
                    ->whereNotIn('status', [0, 6]);
                break;
            case "length_results":
                $documents = $documents->whereHas('documentSigners', function ($query) {
                    $query->select('id')->where(function ($q) {
                        $q->where('signer_employee_id', Auth::user()->employee_id)
                            ->where('status', 3)
                            ->where('is_done', 1);
                    });
                })
                    ->where('status', '!=', 6);
                break;
            case "length_prosesing":
                $documents = $documents->whereHas('documentSigners', function ($query) {
                    $query->select('id')->where(function ($q) {
                        $q->where('signer_employee_id', Auth::user()->employee_id)
                            ->where('status', 3)
                            ->where('is_done', 0);
                    });
                })
                    ->where('status', '!=', 6);
                break;
            case "length_expected":
                $documents = $documents->where(function ($q) {
                    $q->whereHas('documentSigners', function ($q1) {
                        $q1->select('id')
                            ->where('staff_id', 1)
                            ->whereNull('taken_datetime')
                            ->where('document_signers.status', 0);
                    });
                })->whereIn('documents.status', [1, 2])
                    ->orderBy('documents.document_date', 'desc');
                break;
            case "length_star":
                $documents = $documents->whereIn('id', collect(DocumentBookmark::where('user_id', Auth::id())->get())->pluck('document_id'))
                    ->orderBy('documents.document_date', 'desc');
                break;
            case "length_substantiate":
                $documents = $documents->with(['documentType' => function ($q) {
                    $q->select('id');
                }])
                    ->whereHas('documentSigners', function ($query) {
                        $query->select('id')->where(function ($q) {
                            $q->where('signer_employee_id', Auth::user()->employee_id)
                                ->where('status', 4)
                                ->whereIn('is_done', [0, 1]);
                        });
                    })
                    ->where('status', '!=', 6);
                break;
            case "length_executor":
                $documents = $documents->whereHas('documentSigners', function ($query) use ($staff_ids) {
                    $query->select('id')->whereIn('staff_id', $staff_ids)
                        ->where(function ($q) {
                            $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', Auth::user()->employee_id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->where('action_type_id', 4)
                        ->whereNotNul('taken_datetime')
                        ->where('status', 0);
                })
                    ->where('status', '!=', 6);
                break;
            case "length_info":
                $documents = $documents->where(function ($query) use ($staff_ids) {
                    $query->whereHas('documentSigners', function ($q) use ($staff_ids) {
                        $q->select('id')->whereIn('staff_id', $staff_ids)
                            ->where('action_type_id', 5);
                    })
                        ->whereHas('documentSigners', function ($q) {
                            $q->select('id')->whereIn('status', [0, 3, 4]);
                        });
                })
                    ->whereNotIn('status', [0, 6]);
                break;
            case "length_nazorat":
                $documents = $documents->where(function ($query) use ($staff_ids) {
                    $query->whereHas('documentSigners', function ($q) use ($staff_ids) {
                        $q->whereIn('staff_id', $staff_ids)
                            ->where('action_type_id', 11)
                            ->whereIn('status', [0, 3, 4])
                            ->orWhereHas('parentNazorat')
                            ->whereIn('staff_id', $staff_ids)
                            ->where('action_type_id', 4)
                            ->whereIn('status', [0, 3, 4]);
                    });
                })
                    ->whereNotIn('status', [0, 4, 5, 6])
                    ->where('document_date', '>', '2022-01-01');
                break;
            case "length_expired":
                $documents = $documents->whereHas('documentSigners', function ($query) use ($staff_ids) {
                    $query->select('id')->whereIn('staff_id', $staff_ids)
                        ->where(function ($q) {
                            $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', Auth::user()->employee_id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->where(function ($q) {
                            $q->whereNotNull('parent_employee_id')
                                ->where('signer_employee_id', Auth::user()->employee_id)
                                ->orWhereNull('parent_employee_id');
                        })
                        // ->where('action_type_id', '!=', 4)
                        ->whereNotNul('taken_datetime')
                        ->where('due_date', '<', date("Y-m-d H:i:s"))
                        ->whereIn('status', [0, 3]);
                })
                    ->whereNotIn('status', [0, 6]);
                break;
            case "length_watcher":
                $documents = $documents->whereHas('documentSigners', function ($query) use ($userStaff) {
                    $query->select('id')->where(function ($q) use ($userStaff) {
                        foreach ($userStaff as $key => $value) {
                            $q->orWhere('staff_id', $value->staff_id);
                        }
                        return $q;
                    })
                        ->where(function ($q) use ($userStaff) {
                            $q->whereNotNull('signer_employee_id')
                                ->where('signer_employee_id', $userStaff[0]->employee_id)
                                ->orWhereNull('signer_employee_id');
                        })
                        ->where(function ($q) use ($userStaff) {
                            $q->whereNotNull('parent_employee_id')
                                ->where('signer_employee_id', $userStaff[0]->employee_id)
                                ->orWhereNull('parent_employee_id');
                        })
                        ->where('action_type_id', 11)
                        ->whereNotNull('taken_datetime')
                        ->whereIn('status', [0, 3, 4]);
                })
                    ->where('status', '!=', 6);
                break;
            case "length_canceled":
                $documents = $documents->whereHas('documentSigners', function ($query) use ($userStaff) {
                    $query->select('id')->where(function ($q) use ($userStaff) {
                        foreach ($userStaff as $key => $value) {
                            $q->orWhere('staff_id', $value->staff_id);
                        }
                        return $q;
                    });
                })
                    ->whereHas('documentSigners', function ($query) {
                        $query->where('status', 2)->whereNull('parent_employee_id');
                    })
                    ->whereHas('documentSigners', function ($q) {
                        $q->select('id')
                            ->where('signer_employee_id', Auth::user()->employee_id);
                    })
                    ->whereDoesntHave('CancelledDocument', function ($q) {
                        $q->where('user_id', Auth::id());
                    })
                    // ->where('created_at', '>', '2021-09-01')
                    ->whereIn('status', [1, 2, 6])
                    ->where('created_at', '>', '2022-12-01');
                break;
            case "length_agreement":
                $documents = $documents->where(function ($q) use ($staff_ids) {
                    $q->whereHas('documentSigners', function ($query) use ($staff_ids) {
                        $query->select('id')->whereIn('staff_id', $staff_ids)->where('status', 5);
                    })
                        ->orWhere(function ($q1) use ($staff_ids) {
                            $q1->whereHas('documentSigners', function ($query) use ($staff_ids) {
                                $query->select('id')->whereIn('staff_id', $staff_ids)->where('action_type_id', 6);
                            })
                                ->whereDoesntHave('documentSigners', function ($query) {
                                    $query->where('status', 5);
                                });
                        });
                })
                    ->where('status', 7);
                break;

            default:
                return [];
                // $documents;
        }

        if (($id)) {
            $documents->where('id', $id);
        }
        if (($document_template_id)) {
            $documents->where('document_template_id', $document_template_id);
        }
        if ($documentDateOne && $documentDateTwo) {
            $documents->where('document_date', '>=', $documentDateOne);
            if ($documentDateTwo) {
                $documents->where('document_date', '<=', $documentDateTwo);
            }
        }
        if (($document_number)) {
            $documents->where('document_number', 'ilike', '%' . $document_number . '%');
        }
        if (($document_status)) {
            $documents->where('status', $document_status);
        }
        if (($created_employee_id)) {
            $documents->where('created_employee_id', $created_employee_id);
        }
        if (($title)) {
            $documents->where('title', 'ilike', '%' . $title . '%');
        }

        return
            // $documents->count();
            $documents->orderBy('documents.document_date', 'desc')
            ->paginate($itemsPerPage);
        // $documents->paginate(20);
    }

    public function profile(Request $request)
    {
        $user_id = Auth::user()->employee_id;
        $locale  =  $request['locale'];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        $loc = [
            'ru' => 'ru',
            'uz' => 'uz_latin',
            'cy' => 'uz_cyril'
        ];
        $lang = $locale == 'ru' ? 'uz_cyril' : $loc[$locale];
        // return
        $profile = Employee::select(
            'id',
            'lastname_' . $lang . ' as lastname',
            'middlename_' . $lang . ' as middlename',
            'firstname_' . $lang . ' as firstname',
            'tabel'

        )
            ->with(['mainStaff' => function ($q) use ($loc, $locale) {
                $q->select('staff.id', 'department_id', 'position_id', 'staff.is_active')
                    ->with(['position' => function ($q) use ($loc, $locale) {
                        $q->select('positions.id', 'name_' . $loc[$locale] . ' as name');
                    }])
                    ->with(['department' => function ($q) use ($loc, $locale) {
                        $q->select('departments.id', 'name_' . $loc[$locale] . ' as name');
                    }]);
            }])
            ->where('id', $user_id)->first();

        // $path = storage_path('app/avatars/' . $profile->tabel.'.jpg');
        // $path1 = storage_path('app/avatars/' . $profile->tabel.'.JPG');

        // if (!File::exists($path) && !File::exists($path1)) {
        //     abort(404);
        // }
        // if (!File::exists($path)) {
        //     $path = $path1;
        // }

        $profile['image'] = 'https://b-edo.uzautomotors.com/apimobile/storage/' . $profile->tabel;
        // $profile['image'] = $path;
        return
            // $path1;
            $profile;
    }

    public function documentShow(Request $request)
    {
        $locale  =  $request['locale'];
        $pdf_file_name  =  $request['pdf_file_name'];
        $refresh_pdf = $request->input('refresh_pdf');

        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        $loc = [
            'ru' => 'ru',
            'uz' => 'uz_latin',
            'cy' => 'uz_cyril'
        ];
        $lang = $locale == 'ru' ? 'uz_cyril' : $loc[$locale];

        $document = Document::select('id', 'pdf_table')->where('pdf_file_name', $pdf_file_name)->first();
        // ->makeVisible(['base64', 'pdf']);
        $documentId = $document->id;
        $employee_id = Auth::user()->employee_id;
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $signer = DocumentSigner::where('document_id', $documentId)->whereIn('staff_id', $userStaffIds);
        $employee = DocumentSigner::where('document_id', $documentId)->where('signer_employee_id', $employee_id);
        $document_department_id = Document::find($documentId)->department_id;
        $user_department_id = Staff::find($userStaffIds[0])->department_id;
        $comment_color = [
            '0'=>'',
            '3'=>'#1976d2',
            '4'=>'',
            '5'=>'#607d8b',
            '6'=>'#607d8b',
            '9'=>'',
            '11'=>'',
            '16'=>'',
            '21'=>'',
        ];

        if (!($signer->count() || $employee->count() ||  $document_department_id == $user_department_id)) {
            return null;
        }

        if (!$document->pdf_table || $refresh_pdf) {
            Document::savePdf($document->id);
        }
        $document = Document::select(
            'id',
            'document_type_id',
            'document_template_id',
            'document_date',
            'document_number',
            'title',
            'status',
            'created_employee_id',
            'pdf_file_name',
            'document_type',
            'pdf_table',
            'document_number_reg',
            'document_date_reg',
            'pdf_table',
            'pdf_table',
            'pdf_table'
        )
            ->with(['documentSigners' => function ($q) use ($lang, $comment_color) {
                $q->select(
                    'id',
                    'document_id',
                    'staff_id',
                    'taken_datetime',
                    'parent_employee_id',
                    'action_type_id',
                    'assignment',
                    'due_date',
                    'sequence',
                    'signer_employee_id',
                    'description',
                    'status',
                    'sign_type',
                    'department',
                    'position',
                    'fio',
                    'signed_date'
                )->with(['comments' => function ($q) use($comment_color) {
                    $q->select('id', 'document_signer_id', 'comment', 'status', 'created_at')
                        ->with(['files' => function ($fil) {
                            $fil->select('id', 'object_type_id', 'object_id', 'file_name', 'physical_name', 'created_at');
                            $fil->addSelect(\DB::raw("CONCAT('https://b-edo.uzautomotors.com/staffs/file-download/', id) as link"));
                        }])
                        ->orderBy('id', 'desc');
                    $q->addSelect(\DB::raw("CONCAT('https://edo.uzautomotors.com/img/icons/history_image/',status,'.svg/') as icon"));
                    $q->addSelect(\DB::raw("(case when status=0 then '#4caf50'                                            
                                            when status=3 then '#1976d2'
                                            when status=4 then '#fb8c00'
                                            when status=5 then '#607d8b'
                                            when status=6 then '#607d8b'
                                            when status=9 then '#4caf50'
                                            when status=11 then '#4caf50'
                                            when status=16 then '#fb8c00'
                                            when status=21 then '#ff5252'
                                            else '#000' end ) as color"));
                }])
                    ->with(['signerEmployee' => function ($q1) use ($lang) {
                        $q1->select('id', 'firstname_' . $lang . ' as firstname', 'lastname_' . $lang . ' as lastname', 'middlename_' . $lang . ' as middlename');
                    }]);
            }])
            ->where('id', $documentId)->first()
            ->makeVisible(['base64', 'pdf']);

        // $document['site_id'] = config('app.APP_SITE_ID');
        $document['e_imzo_data'] =  json_decode(Document::eimzoMobileAuth());




        return
            $document;
    }


    public function setBase64(Request $request)
    {
        $document_id = $request->input('document_id');
        $document = Document::find($document_id);
        if (!$document->pdf_table) {
            $document->pdf_table = Document::savePdf($document_id);
        }
        DB::connection('mysql_workflow_pdf')
            ->table($document->pdf_table)
            ->where('document_id', $document->id)
            ->update(['eimzoBase64' => $request->input('base64')]);
    }

    public function resolution(Request $request)
    {
        // return $request;
        $locale  =  $request['locale'];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        // if(Auth::id() == 4){
        //     return($request);
        //     return json_encode($request->input('employees'));
        //     // return count($employee_ids);
        // }

        // $employee_id = $request['employee_id'] ?? '';
        // $employee_id = $request['employees'] ?? '';
        // $request['employees'] = json_decode($request['employees']);
        // return $request;
        $document_id = $request['document_id'] ?? '';
        // $due_date = $request['due_date'] ?? '';
        // $action_type_id = $request['action_type_id'] ?? '';
        // $comment = $request['comment'] ?? '';

        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $document_signer = DocumentSigner::where('document_id', $document_id)
            ->whereIn('staff_id', $userStaffIds)
            ->where(function ($q) {
                return $q->where('signer_employee_id', Auth::user()->employee_id)
                    ->orWhereNull('signer_employee_id');
            })
            ->whereNotNull('taken_datetime')
            ->first();

        $sequence = $document_signer->sequence;
        $request['sequence'] = $sequence;
        // $sequence = $request['sequence'] ?? '';

        $documentSignerController = new DocumentSignerController();
        return $documentSignerController->addDocumentSigners($request);
        // [$employee_id, $due_date, $action_type_id, $comment];
    }


    public function actionType(Request $request)
    {
        $locale  =  $request['locale'];
        $loc = [
            'ru' => 'ru',
            'uz' => 'uz_latin',
            'cy' => 'uz_cyril'
        ];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        // return
        $actionTypes =  ActionType::select('id',  'name_' . $loc[$locale] . ' as name')->whereIn('id', [4, 5, 16])->get();
        return [
            'status_code' => 200,
            'message' => $this->language('success', $locale),
            'data' => $actionTypes
        ];
    }


    public function getResolutionEmployeesMobile(Request $request)
    {

        $locale  =  $request['locale'];
        $loc = [
            'ru' => 'ru',
            'uz' => 'uz_latin',
            'cy' => 'uz_cyril'
        ];
        $lang = $locale == 'ru' ? 'uz_cyril' : $loc[$locale];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }
        $search = $request->input('search');
        $user = User::with('roles')->find(Auth::id());
        $department_ids = Department::select('id')->whereHas('staff', function ($q) use ($user) {
            $q->whereHas('EmployeeStaff', function ($q) use ($user) {
                $q->where('is_active', 1)
                    ->where('employee_id', $user->employee_id);
            });
        })->get();

        if (Auth::user()->hasRole('resolution_all_employee')) {
            // return 5;
            $resolutionEmployee = Employee::select('id', 'tabel', 'firstname_' . $loc[$locale] . ' as firstname', 'lastname_' . $loc[$locale] . ' as lastname', 'middlename_' . $loc[$locale] . ' as middlename')
                ->with(['mainStaff' => function ($query) use ($loc, $locale) {
                    $query->select('position_id')
                        ->with('position:id,name_' . $loc[$locale] . ' as name');
                }])
                ->whereNotIn('id', [$user->employee_id, 4, 5]);
        } else {
            $department_idss = [];
            foreach ($department_ids as $key => $department_id) {
                $arr = $this->getDepIds($department_id->id);
                $department_idss = array_merge($department_idss, $arr);
            }
            // return $department_idss;
            $resolutionEmployee = Employee::select('id', 'tabel', 'firstname_' . $loc[$locale] . ' as firstname', 'lastname_' . $loc[$locale] . ' as lastname', 'middlename_' . $loc[$locale] . ' as middlename')
                // updated
                ->with(['mainStaff' => function ($query) use ($loc, $locale) {
                    $query->select('position_id', 'department_id')
                        ->with('position:id,name_' . $loc[$locale] . ' as name')

                        ->with('department:id,name_' . $loc[$locale] . ' as name');
                }])
                ->whereHas('mainStaff', function ($q) use ($department_idss) {
                    $q->whereIn('department_id', $department_idss);
                })
                ->where('id', '!=', $user->employee_id);
        }


        if ($search) {
            $resolutionEmployee->where(function ($query) use ($search, $lang) {
                return $query
                    // ->where('tabel', 'ilike', '%' . $search . '%')
                    // ->orWhere(DB::raw("concat(employees.lastname_" . $lang . ",' ', employees.firstname_" . $lang . ",' ', employees.middlename_" . $lang . ")"), 'ilike', "%" . $search . "%")
                    // ->orWhere(DB::raw("concat(employees.lastname_" . $lang . ",' ', employees.firstname_" . $lang . ",' ', employees.middlename_" . $lang . ")"), 'ilike', "%" . $search . "%")
                    // ->orWhere(DB::raw("concat(employees.firstname_" . $lang . ", ' ', employees.lastname_" . $lang . ", ' ', employees.middlename_" . $lang . ")"), 'ilike', "%" . $search . "%")
                    // ->orWhere(DB::raw("concat(employees.firstname_" . $lang . ", ' ', employees.lastname_" . $lang . ", ' ', employees.middlename_" . $lang . ")"), 'ilike', "%" . $search . "%");

                    ->where('tabel', 'ilike', '%' . $search . '%')
                    ->orWhere(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%");
            });
        }
        $employee_list = $resolutionEmployee
            ->paginate(20)
            // ->get()
            ->items();
        $resolution =  ActionType::select('id', 'name_' . $loc[$locale] . ' as name')->where('is_resolution', 1)->get();

        return ['data' => $employee_list, 'Resolution_types' => $resolution];
    }

    public function getDepIds($dep_id)
    {
        $ids = [$dep_id];
        $deps = Department::select('id')->where('parent_id', $dep_id)->get();
        foreach ($deps as $key => $value) {
            $ids = array_merge($ids, $this->getDepIds($value->id));
        }
        return $ids;
    }


    public function comment(Request $request)
    {
        $locale  =  $request['locale'];
        $loc = [
            'ru' => 'ru',
            'uz' => 'uz_latin',
            'cy' => 'uz_cyril'
        ];
        $lang = $locale == 'ru' ? 'uz_cyril' : $loc[$locale];
        if (!$locale || !in_array($locale, ['ru', 'uz', 'cy'])) {
            return [
                'status_code' => 400,
                'message' => 'locale not found!',
                'data' => ''
            ];
        }


        $docId = $request['document_id'];
        $assignment = $request['comment'];
        $signer_id = $request['signer_id'];
        $substantiate = $request['substantiate'];
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $addDoc = DocumentSigner::where('document_id', $docId)
            ->whereIn('staff_id', $userStaffIds)
            ->where(function ($q) {
                return $q->where('signer_employee_id', Auth::user()->employee_id)
                    ->orWhereNull('signer_employee_id');
            })
            ->whereNotNull('taken_datetime')
            // ->whereIn('status',[0,3,1])
            ->first();
        if (!$addDoc) {
            return 0;
        } else {
            $addDoc->signer_employee_id = Auth::user()->employee_id;
            $employee = Auth::user()->employee;
            $document = Document::find($docId);
            if ($substantiate) {
                $addDoc->status = 4;
            }
            $addDoc->fio = $employee->getShortname($document->locale);
            $addDoc->save();
        }
        $documentSignerEvent = new DocumentSignerEvent;
        $documentSignerEvent->document_signer_id = $addDoc->id;
        $documentSignerEvent->action_type_id = $addDoc->action_type_id;

        $documentSignerEvent->comment = $assignment;
        $documentSignerEvent->status = $substantiate ? 4 : 5;
        $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
        $documentSignerEvent->fio = $addDoc->fio;
        $documentSignerEvent->save();

        // mail yuborish uchun //
        $document = Document::where('id', $docId)->with('documentTemplate')->with('documentType')->first();
        if ($signer_id) {
            $comment_signer = DocumentSigner::find($signer_id);
            if ($comment_signer->signer_employee_id) {
                $employee = Employee::find($comment_signer->signer_employee_id);
                $documentSignerEvent->comment = substr($employee->firstname_uz_cyril, 0, 2) . '.' . substr($employee->middlename_uz_cyril, 0, 2) . '. ' . $employee->lastname_uz_cyril . ': -' . $assignment;
                $documentSignerEvent->save();
                $user = User::where('employee_id', $comment_signer->signer_employee_id)->first();
            } else {
                $comment_employee = EmployeeStaff::where('staff_id', $comment_signer->staff_id)->where('is_active', 1)->first();
                $employee = Employee::find($comment_employee->employee_id);
                $documentSignerEvent->comment = substr($employee->firstname_uz_cyril, 0, 2) . '.' . substr($employee->middlename_uz_cyril, 0, 2) . '. ' . $employee->lastname_uz_cyril . ': -' . $assignment;
                $documentSignerEvent->save();
                $user = User::where('employee_id', $comment_employee->employee_id)->first();
            }
            $comment_signer->assignment = $assignment;
            $comment_signer->save();
        } elseif ($addDoc->parent_employee_id) {
            $user = User::where('employee_id', $addDoc->parent_employee_id)->first();
        } else {
            $user = User::where('employee_id', $document->created_employee_id)->first();
        }

        // Obosnovat bosilganda hujjat avtoriga email jo'natish
        $user = User::where('employee_id', $document->created_employee_id)->first();
        if ($user && $user->email) {
            // $reaction_type = 'Ushbu hujjatga izox yozildi. Ko\'rib chiqing.';
            if ($substantiate) {
                $reaction_type = 'Ushbu hujjat shaxsan asoslab berilsin.';
                // SendEmail::addToQueue($user->email, $document->id, $reaction_type);
                // if ($document->id == 164861) 
                {
                    $link = "https://edo.uzautomotors.com/#/document/" . $document->pdf_file_name;
                    $details = [
                        'title' => "Ushbu hujjat shaxsan asoslab berilsin.",
                        'content' => json_encode([
                            'Link' => $link,
                        ]),
                        'footer' => ''
                    ];
                    Mail::to($user->email)->send(new SendMail($details));
                }
            }
        }

        // Avtor comment yozganda Obosnavat bosgan odamga email jo'natish
        if (!$substantiate && $document->created_employee_id == Auth::user()->employee_id) {
            $signers = DocumentSigner::where('status', 4)->where('document_id', $docId)->whereNotNull('signer_employee_id')->get();
            foreach ($signers as $key => $value) {
                $user = User::where('employee_id', $value->signer_employee_id)->first();
                if ($user && $user->email) {
                    $reaction_type = 'Ushbu hujjat shaxsan asoslab berilsin.';
                    $link = "https://edo.uzautomotors.com/#/document/" . $document->pdf_file_name;
                    $details = [
                        'title' => "Sizning so'rovnomangizga javob berildi.",
                        'content' => json_encode([
                            'Link' => $link,
                            'Izoh' => $assignment,
                            'Hujjat muallifi' => Auth::user()->employee->getShortname('uz_latin')
                        ]),
                        'footer' => ''
                    ];
                    Mail::to($user->email)->send(new SendMail($details));
                }
            }
        }

        $this->commentFile($request, $documentSignerEvent);


        if ($documentSignerEvent) {
            return [
                'status_code' => 200,
                'message' => $this->language('success', $locale),
            ];
        } else {
            return [
                'status_code' => 400,
                'message' => $this->language('invalid_request', $locale)
            ];
        }
    }


    public function commentFile($request, $documentSignerEvent)
    {
        // return $request;
        // return $_POST;
        // $docId = $request['document_id'];

        // $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        // return
        // $id = DocumentSigner::where('document_id', $docId)
        // ->whereIn('staff_id', $userStaffIds)
        // ->where(function ($q) {
        //     return $q->where('signer_employee_id', Auth::user()->employee_id)
        //     ->orWhereNull('signer_employee_id');
        // })
        // ->whereNotNull('taken_datetime')
        // // ->whereIn('status',[0,3,1])
        // ->first();
        // // return $id->id;

        DB::beginTransaction();
        try {
            $files = $request->file('files');
            $object_type_id = 6;
            $object_id = $documentSignerEvent->id;

            foreach ($files as $key => $value) {
                $filename = time() . rand();
                Storage::putFileAs(
                    'documents',
                    $value,
                    $filename
                );
                $file = new File();
                $file->object_type_id = $object_type_id;
                $file->file_name = $value->getClientOriginalName();
                $file->physical_name = $filename;
                $file->object_id = $object_id;
                // $file->description = $description;
                $file->created_by = Auth::id();
                $file->save();
            }
            DB::commit();
            return ['status' => 200, 'message' => 'Successfully saved!', 'object_id' => $object_id,];
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
        // 'file' => $file
    }


    public function reaction(Request $request)
    {
        $docId = $request['document_id'];
        $userStaffIds = collect(Auth::user()->employee->staff)->pluck('id');
        $status = $request->input('status');
        $sign_type = $request->input('sign_type');
        // if($docId == 2957511){
        //     dd(123);
        // }

        $signer_serial_number = $request->input('signer_serial_number');
        $description = $request->input('description');
        $document = Document::where('id', $docId)->with('documentTemplate')->with('documentType')->first();

        DB::beginTransaction();
        try {
            $addDoc = DocumentSigner::where('document_id', $docId)
                ->whereIn('staff_id', $userStaffIds)
                ->whereIn('status', [0, 3, 4])
                ->whereNotNull('taken_datetime')
                ->where(function ($q) {
                    $q->whereNotNull('signer_employee_id')
                        ->where('signer_employee_id', Auth::user()->employee_id)
                        ->orWhereNull('signer_employee_id');
                })
                ->first();

            $addDoc->signer_employee_id = Auth::user()->employee_id;
            $employee = Auth::user()->employee;
            $count = $document->locale == 'uz_latin' ? 1 : 2;
            $addDoc->fio = $employee->getShortname($document->locale);
            $addDoc->signed_date = time();
            $addDoc->sign_at = date('Y-m-d H:i:s');
            $addDoc->description = $description;
            $addDoc->status = $status;
            $addDoc->sign_type = $sign_type;
            $addDoc->signer_serial_number = $signer_serial_number;
            $addDoc->save();

            if ($addDoc->parent_employee_id) {
                $count = DocumentSigner::where('document_id', $docId)
                    ->where('parent_employee_id', $addDoc->parent_employee_id)
                    ->whereIn('status', [0, 3, 4])->count();
                if ($count == 0) {
                    $parent_signer = DocumentSigner::where('document_id', $docId)->where('signer_employee_id', $addDoc->parent_employee_id)->where('status', 3)->first();
                    if ($parent_signer) {
                        $parent_signer->is_done = 1;
                        $parent_signer->save();
                    }
                }
            }

            $documentSignerEvent = new DocumentSignerEvent;
            $documentSignerEvent->document_signer_id = $addDoc->id;
            $documentSignerEvent->action_type_id = $addDoc->action_type_id;
            $documentSignerEvent->comment = $description;
            $documentSignerEvent->status = $sign_type ? $status . $sign_type : $status;
            $documentSignerEvent->signer_employee_id = $addDoc->signer_employee_id;
            $documentSignerEvent->fio = $addDoc->fio;
            $documentSignerEvent->save();

            $this->commentFile($request, $documentSignerEvent);

            if ($status == 2 && $addDoc->parent_employee_id == null && ($addDoc->action_type_id != 4 || $document->document_type_id == 7 && !$addDoc->parent_employee_id && $addDoc->status != 0) && $addDoc->action_type_id != 8 && $addDoc->action_type_id != 17) {
                // $document = Document::where('id', $docId)->first();
                if ($document->document_type_id == 12 && $addDoc->action_type_id == 12 && $addDoc->sequence == 98) {
                    DocumentSigner::where('document_id', $document->id)->where('action_type_id', '<>', 6)
                        ->update(['status' => 0, 'taken_datetime' => null]);
                    $document->status = 0;
                    $document->save();
                } else {
                    $document->status = 6;
                    $document->save();
                }

                // Email jo'natish ---------------------
                $user = User::where('employee_id', $document->created_employee_id)->first();
                if ($user && $user->email && $document->status == 6) {
                    $reaction_type = 'Ushbu hujjat shaxsan asoslab berilsin.';
                    // if ($document->id == 211767) 
                    {
                        $employee = Employee::find(Auth::user()->employee_id);
                        $link = "https://edo.uzautomotors.com/#/document/" . $document->pdf_file_name;
                        $details = [
                            $fio =
                                'title' => "Ushbu hujjat " . $employee->getShortname('uz_latin') . " tomonidan bekor qilindi.",
                            'content' => json_encode([
                                'Link' => $link,
                                'Izoh' => $description,
                            ]),
                            'footer' => ''
                        ];
                        Mail::to($user->email)->send(new SendMail($details));
                    }
                }
                //------------------------
                $documentSigners = DocumentSigner::where('document_id', $document->id)->where('status', 1)->where('action_type_id', '!=', 6)->get();
                foreach ($documentSigners as $key => $documentSigner) {
                    // mail yuborish uchun
                    // $user = User::where('employee_id', $documentSigner->signer_employee_id)->first();
                    // if ($user && $user->email) {
                    //     $reaction_type = 'Siz imzolagan hujjat bekor qilindi!';
                    //     SendEmail::addToQueue($user->email, $document->id, $reaction_type);
                    // }
                }
                if ($document->document_template_id == 114) {
                    foreach ($document->documentRelation as $key => $value) {
                        $value->status = 3;
                        $value->save();

                        $ds  = DocumentSigner::where('document_id', $value->id)->where('sequence', 0)->first();
                        if ($ds) {
                            try {
                                $history = new DocumentSignerEvent();
                                $history->comment = 'Buyruq bekor qilindi.';
                                $history->status = 2;
                                $history->created_at = date('Y-m-d H:i:s');
                                $history->action_type_id = 5;
                                $history->document_signer_id = $ds->id;
                                $history->signer_employee_id = $ds->signer_employee_id;
                                $history->fio = $ds->fio;
                                $history->save();
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                    }
                }
            }

            $count = DocumentSigner::where('document_id', $docId)
                ->where('sequence', $addDoc->sequence)
                // ->where('action_type_id', "!=", 12)
                ->whereNull('parent_employee_id')
                ->whereIn('status', [0, 3, 4])->count();
            if ($count == 0 && $addDoc->sequence > 0) {
                // if ($count == 0 && $addDoc->sequence > 0 && $addDoc->action_type_id != 12) {
                // return $docId;
                $sequence = DocumentSigner::where('document_id', $docId)
                    ->where('status', 0)
                    // ->where('action_type_id', "!=", 12)
                    ->orderByDesc('sequence')->first();
                if ($sequence && $document->status != 6) {
                    $taken = DocumentSigner::where('document_id', $docId)
                        ->where('status', 0)
                        ->whereNull('taken_datetime')
                        ->where('sequence', $sequence->sequence);
                    foreach ($taken->get() as $key => $value) {
                        $due_day = DocumentSigner::select('document_signer_templates.due_day_count')
                            ->join('documents', 'documents.id', '=', 'document_signers.document_id')
                            ->join('document_templates', 'document_templates.id', '=', 'documents.document_template_id')
                            ->join('document_signer_templates', 'document_signer_templates.document_template_id', '=', 'document_templates.id')
                            ->where('document_signers.id', $value->id)->first();
                        $value->taken_datetime = date("Y-m-d H:i:s");
                        if ($sequence->sequence != 100 && $document->status != 2) {
                            $document->status = 2;
                            $document->save();
                        }
                        $value->due_date = isset($due_day->due_day_count) ?
                            date("Y-m-d H:i:s", time() + 3600 * $due_day->due_day_count) : ($value->due_date ? $value->due_date : date("Y-m-d H:i:s", time() + 86400));
                        if ($value->action_type_id == 5) {
                            $value->status = 1;
                        }
                        if ($value->sign_type == 0) {
                            $value->sign_type = 1;
                        }
                        $value->save();

                        $employeeStaffs = EmployeeStaff::where('staff_id', $value->staff_id)->where('is_active', 1)->get();
                        if ($addDoc->action_type_id == 1) {
                            $actionType = "Rozilik";
                        } elseif ($addDoc->action_type_id == 2) {
                            $actionType = "Tasdiqlash";
                        } elseif ($addDoc->action_type_id == 3) {
                            $actionType = "Bo'lim ichida rozilik";
                        } else {
                            $actionType = "Ko'rib chiqish";
                        }
                        foreach ($employeeStaffs as $key => $employeeStaff) {

                            // mail yuborish uchun
                            // $user = User::where('employee_id', $employeeStaff->employee_id)->first();
                            // if ($user && $user->email) {
                            //     SendEmail::addToQueue($user->email, $document->id, $actionType);
                            // }
                        }
                    }
                }
            }

            $completed = DocumentSigner::where('document_id', $document->id)
                ->where('sequence', '>', 0)
                ->where('status', '!=', 1)
                ->whereNotIn('action_type_id', [4, 8, 12, 17])
                ->whereNull('parent_employee_id');
            // DocumentSigner::where('document_id', $docId)->where('status', '!=', 1)->whereNotIn('action_type_id', [4, 5, 6, 8, 11, 12, 14])->whereNull('parent_employee_id');
            $signing = false;
            $outSigner = DocumentSigner::where('document_id', $docId)->whereIn('action_type_id', [2])->first();
            if ($completed->count() == 0 && $document->status < 3 && $document->document_type_id != 55) {
                $document->status = 3;
                $document->save();
                if ($document->document_template_id == 574) {
                    $this->sendStatusToTengeBank($document->id);
                }
                $signing = true;
            } else if ($document->document_type_id == 55) {
                $completed = DocumentSigner::where('document_id', $document->id)
                    ->where('sequence', '>', 0)
                    ->where('status', '!=', 1)
                    ->whereNotIn('action_type_id', [4, 8, 17])
                    ->whereNull('parent_employee_id');
                if ($completed->count() == 0 && $document->status < 3) {
                    $document->status = 3;
                    $document->save();
                }
            }

            $notCompleted = DocumentSigner::where('document_id', $docId)->whereNotIn('status', [1, 2])->whereNull('parent_employee_id');
            if ($notCompleted->count() == 0 && $outSigner) {
                $cancelled = DocumentSigner::where('document_id', $docId)
                    ->where('status', 2)
                    ->whereIn('action_type_id', [1, 2, 9])
                    ->whereNull('parent_employee_id')
                    ->count();
                if ($cancelled == 0) {
                    $document->status = 5;
                    $document->save();
                }
            }
            // if ($document->document_template_id == 114 && $document->status == 3) {
            //     $this->saveAttributesToAs400($document->id);
            // }
            // if ($document->document_template_id == 287 && in_array($document->status, [3, 4, 5])) {
            //     $this->uvolnitelniyCreate($document->id);
            // }
            if ($document->document_template_id == 224 && in_array($document->status, [3, 4, 5])) {
                $this->transportRequestCreate($document->id);
            }
            DB::commit();

            // if ($document->document_template_id == 218) 
            {
                // Bo'lim ichidan chiqqanda nomer olish tartibi
                $ds = DocumentSigner::where('document_id', $document->id)
                    ->where('sequence', '>', 98)
                    ->where('status', '!=', 1)
                    ->first();
                if (!$ds && $document->documentTemplate->numbering_order == 2 && $addDoc->sequence > 98) {
                    Document::generateDocumentNumberNew2022($document->id);
                } else {
                    // Rahbar imzolaganda nomer olish tartibi
                    $ds = DocumentSigner::where('document_id', $document->id)
                        ->where('sequence', '>', 0)
                        ->where('status', '!=', 1)
                        ->whereNotIn('action_type_id', [4, 8, 12, 17]) // komissiya azolari, kuzatuvchi
                        ->first();
                    if (!$ds && $document->documentTemplate->numbering_order == 3 && $addDoc->sequence > 0 && $document->document_type_id != 55) {
                        Document::generateDocumentNumberNew2022($document->id);
                    } else if ($document->document_type_id == 55) {
                        $completed = DocumentSigner::where('document_id', $document->id)
                            ->where('sequence', '>', 0)
                            ->where('status', '!=', 1)
                            ->whereNotIn('action_type_id', [4, 8, 17])
                            ->whereNull('parent_employee_id');
                        if ($completed->count() == 0) {
                            Document::generateDocumentNumberNew2022($document->id);
                        }
                    }
                }
            }

            Document::savePdf($document->id);

            if ($document->document_template_id == 287 && in_array($document->status, [3, 4, 5])) {
                $this->uvolnitelniyCreate($document->id);
            }

            // Perevod va yangi hodi olish shablonlari uchun
            // dd($document->document_template_id, $sign_type, $document->status);
            if (in_array($document->document_template_id, [415]) && $addDoc->action_type_id == 2 && in_array($document->status, [3, 4, 5])) {
                $this->changeStaffAndSaveAs400($document->id);
            }
            if ($document->document_template_id == 114 && $signing) {
                $this->saveAttributesToAs400($document->id);
            } elseif ($document->document_template_id == 226 && $signing) {
                $this->businessTripToAs400($document->id);
            } elseif ($document->document_template_id == 4 && $signing) {
                $this->otgulToAs400($document->id);
            }

            return [
                'message' => 'Successfully saved!',
                'document_status' => $document->status,
                'signer_event_id' => $documentSignerEvent->id,
                'action_type_id' => $addDoc->action_type_id
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

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
}
