<?php

namespace App\Http\Controllers;

use App\InventoryAddress;
use Illuminate\Http\Request;
use App\Http\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use GuzzleHttp\Client as Client;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Str;

class IdesController extends Controller
{
    public function documentIndex(Request $request)
    {
        // $request->all();
        $token =  Auth::user()->ides_token;
        // $token =  $token =  '6822|3cOZVajSeMkICabsashTPP8tFJiZB2R2L8mn89CU';
        $client = new GuzzleClient([
            'verify' => false
        ]);
        $response = $client->request(
            'POST',
            config('app.IDES_URL') . '/api/documents/index',
            [
                'form_params' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );
        return $response->getBody()->getContents();
    }
    public function documentIndexNew(Request $request)
    {
        // return 
        // $request->all();
        $token =  Auth::user()->ides_token;
        // $token =  $token =  '6822|3cOZVajSeMkICabsashTPP8tFJiZB2R2L8mn89CU';
        $client = new GuzzleClient([
            'verify' => false
        ]);
        $response = $client->request(
            'POST',
            config('app.IDES_URL') . '/api/documents/index-new',
            [
                'form_params' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );
        return $response->getBody()->getContents();
    }
    public function showNew($id)
    {
        $token =  Auth::user()->ides_token;
        $client = new Client([
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $response = $client->request('GET', config('app.IDES_URL') . '/api/documents/show/' . $id);
        return $response->getBody()->getContents();
    }
    public function idesCounter()
    {
        //IDES COUNTER
        $ides_count = '';
        $token =  Auth::user()->ides_token;
        if ($token) {
            $client = new GuzzleClient([
                'verify' => false
            ]);
            $response = $client->request(
                'GET',
                config('app.IDES_URL') . '/api/documents/getCount',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token
                        ]
                        ]
                    );
        return $response->getBody()->getContents();

            // $ides_count = json_decode($response->getBody()->getContents());
            // return $ides_count;
        }
    }
    public function updateFiles(Request $request, $id)
    {
        $files = $request->file('files');
        // return 1;
        $multipart = [];
        if ($files) {
            //Avtosanoat commentiga biriktirilgan fayllar
            if ($request->input('comment')) {
                foreach ($files as $key => $value) {
                    $multipart[] = [
                        'name'     => 'files' . (count($multipart) + $key),
                        'contents' => file_get_contents($value->getPathName()),
                        'filename' => 't4' . $value->getClientOriginalName(),
                    ];
                }
            }
            //Control Punkt orqali biriktirilgan fayllar
            else {

                foreach ($files as $key => $value) {
                    $multipart[] = [
                        'name'     => 'files' . (count($multipart) + $key),
                        'contents' => file_get_contents($value->getPathName()),
                        'filename' => 't3' . $value->getClientOriginalName(),
                    ];
                }
            }
        }
        $token =  Auth::user()->ides_token;
        $client = new GuzzleClient([
            'verify' => false
        ]);
        $response = $client->request(
            'POST',
            config('app.IDES_URL') . '/api/documents/updatefiles/' . $id,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'multipart' => $multipart
            ]
        );
        return $response->getBody()->getContents();
    }
    public function fileDownload($id)
    {
        // return response()->download('D:\sample.pdf', 'sample.pdf', []);
        // return 1;
        $token =  '13766|cUOsGeranaKnSO4J5bzSrJlaQw53ZrseWIQB1d2N';
        $client = new GuzzleClient([
            'verify' => false
        ]);
        $response = $client->request(
            'GET',
            config('app.IDES_URL') . '/api/documents/file/' . $id,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );
        return $response;
        // return response()->download($response);
    }
    
    public function showFile($id)
    {
        $token =  '13766|cUOsGeranaKnSO4J5bzSrJlaQw53ZrseWIQB1d2N';
        $client = new GuzzleClient([
            'verify' => false
        ]);
        $response = $client->request(
            'GET',
            // config('app.IDES_URL') . '/api/documents/file/' . $id,
            config('app.IDES_URL') . '/document/file/' . $id,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );
        return $response->getBody()->getContents();
        
    }
    public function getFile($id)
    {
        // return response()->download('D:\sample.pdf', 'sample.pdf', []);
        // return 1;
        $token =  '13766|cUOsGeranaKnSO4J5bzSrJlaQw53ZrseWIQB1d2N';
        $client = new GuzzleClient([
            'verify' => false
        ]);
        $response = $client->request(
            'GET',
            config('app.IDES_URL') . '/api/documents/getFile/' . $id,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );
        return $response;
        // return response()->download($response);
    }
    public function documentReaction(Request $request)
    {
        // return Auth::id(); 
        $token =  Auth::user()->ides_token;
        $client = new GuzzleClient([
            'verify' => false
        ]);
        $response = $client->request(
            'POST',
            config('app.IDES_URL') . '/api/documents/reaction',
            [
                'form_params' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );
        return $response->getBody()->getContents();
    }
    public function idesLogin()
    {
        $user = Auth::user();
        
        
        // if ($user->employee_id == 10772){
        //     $org_id = 17;
        // }else{
        //     $org_id = 16;
        // }
        // if($user->employee_id == 109){            
        //     $org_id = 16;
        // } elseif ($user->employee_id == 10772){
        //     $org_id = 17;
        // }else{
        //     $org_id = 3;
        // }

        $client = new Client([
            'verify' => false
        ]);

        if (!$user->ides_username) {
            $user->ides_password = Str::random(20);
            // return $user->ides_password;
            $user->ides_username = Str::random(20);
            $staff = count($user->employee->mainStaff) > 0 ? $user->employee->mainStaff : $user->employee->staff;
            $response = $client->request('POST', config('app.IDES_URL') . '/api/signup', [
                'form_params' => [
                    'email' => $user->email,
                    'password' => $user->ides_password,
                    'username' => $user->ides_username,
                    'organization_id' => 16 , // Asaka Gm
                    'fio_uz_latin' =>  $user->employee->lastname_uz_latin . ' ' . $user->employee->firstname_uz_latin . ' ' . $user->employee->middlename_uz_latin,
                    'fio_uz_cyril' =>  $user->employee->lastname_uz_cyril . ' ' . $user->employee->firstname_uz_cyril . ' ' . $user->employee->middlename_uz_cyril,
                    'fio_ru' =>  $user->employee->lastname_ru . ' ' . $user->employee->firstname_ru . ' ' . $user->employee->middlename_ru,
                    'department_uz_latin' => $staff[0]->department ? $staff[0]->department->name_uz_latin : '',
                    'department_uz_cyril' => $staff[0]->department ? $staff[0]->department->name_uz_cyril : '',
                    'department_ru' => $staff[0]->department ? $staff[0]->department->name_ru : '',
                    'position_uz_latin' => $staff[0]->position ? $staff[0]->position->name_uz_latin : '',
                    'position_uz_cyril' => $staff[0]->position ? $staff[0]->position->name_uz_cyril : '',
                    'position_ru' => $staff[0]->position ? $staff[0]->position->name_ru : '',
                ]
            ]);
        } else {
            $response = $client->request('POST', config('app.IDES_URL') . '/api/login', [
                'form_params' => [
                    'username' => $user->ides_username,
                    'password' => $user->ides_password,
                ]
            ]);
        }
        if (json_decode($response->getStatusCode() != 500)) {
            $user->ides_token = json_decode($response->getBody())->token;
            $user->save();
            // return $response->getBody();
            return [
                'status_code' => 200,
                'message' => 'Successfull'
            ];
        }
        return [
            'status_code' => 500,
            'message' => json_decode($response->body())
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
        
    public function received(Request $request)
    {        
        // return Auth::id(); 
        $token =  Auth::user()->ides_token;
        $client = new GuzzleClient([
            'verify' => false
        ]);
        $response = $client->request(
            'POST',
            config('app.IDES_URL') . '/api/documents/received',
            [
                'form_params' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]
        );
        return $response->getBody()->getContents();
    }
    public function index(Request $request)
    {
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
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
