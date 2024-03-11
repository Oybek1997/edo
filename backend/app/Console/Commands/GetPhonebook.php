<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\Phonebook;
use DOMDocument;
use Adldap\Laravel\Facades\Adldap;
use App\Http\Models\Department;
use App\Http\Models\Employee;
use App\Http\Models\EmployeeStaff;
use App\Http\Models\Staff;

class GetPhonebook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phone:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets information about phone numbers from a third-party api and inserts into database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo date('H-i') . ' - ';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://11.91.32.11:8443/axl/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.cisco.com/AXL/API/9.1">   <soapenv:Header/>   <soapenv:Body>      <ns:executeSQLQuery>         <sql>            select d.description, n.dnorpattern as DN from device as d, numplan as n, devicenumplanmap as dnpm where dnpm.fkdevice = d.pkid and dnpm.fknumplan = n.pkid and d.tkclass = 1       </sql>      </ns:executeSQLQuery>   </soapenv:Body></soapenv:Envelope>',
            CURLOPT_HTTPHEADER => array(
                'soapaction: CUCM:DB ver=11.5 executeSQLQuery',
                'authorization: Basic YWRtaW46VXZGY2ZyZjIwMTk=',
                'Content-Type: text/plain',
                'Cookie: JSESSIONID=6380D96CABDC0A3A3271B8A321448AF1; JSESSIONIDSSO=CE04D03A809160D4AEB01BC85B214FA9'
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        // dd($response);

        echo curl_error($curl);
        curl_close($curl);
        $doc = new DOMDocument();
        $doc->loadXML($response);
        $html = $doc->getElementsByTagName('row');
        $arr = [];
        $timestamp = now();
        foreach ($html as $key => $el) {
            // echo ($html[1842]->childNodes[0]->childNodes[0]->nodeValue);return 1;
            // if (isset($el->childNodes[0]->childNodes[0]) && isset($el->childNodes[1]->childNodes[0])) 
            {
                $arr[] = [$el->childNodes[0]->childNodes[0] ? $el->childNodes[0]->childNodes[0]->nodeValue : '-', $el->childNodes[1]->childNodes[0] ? $el->childNodes[1]->childNodes[0]->nodeValue : '-'];
                $value = $arr[$key];
                $tabel = $this->getTabel($value[0]);
                $model = Phonebook::where('name', $value[0])->first();
                if (!$model) {
                    $model = new Phonebook();
                }
                if ($tabel) {
                    $model->department_name = "" . $this->getDepartment($tabel)[0] . "";
                    $model->position_name = "" . $this->getDepartment($tabel)[1] . "";
                }
                $email = $this->getEmail($tabel);
                $model->name = $value[0];
                $model->phone = $value[1];
                // 1000-1149 - toshkent
                // 1150-1199 compliance andijon
                // 1200-1999 - toshkent
                // 2000-2799 - avtosanoat
                // 2800-2999 - xorazm
                // 3000-4699 - asaka
                // 4700-4999 - xorazm
                if ($this->betweenRange($model->phone, 1000, 1149) || $this->betweenRange($model->phone, 1200, 1999)) {
                    $model->branch = 1; // Toshkent
                } else if ($this->betweenRange($model->phone, 2000, 2799)) {
                    $model->branch = 2; // Avtosanoat
                } else if ($this->betweenRange($model->phone, 3000, 4699)) {
                    $model->branch = 3; // Asaka
                } else if ($this->betweenRange($model->phone, 2800, 2999) || $this->betweenRange($model->phone, 4700, 4999)) {
                    $model->branch = 4; // Xorazm
                } else if ($this->betweenRange($model->phone, 1150, 1199)) {
                    $model->branch = 5; // Compliance Andijon
                } else {
                    $model->branch = 6;
                }
                $model->email = $email;
                $model->updated_at = $timestamp;
                $model->save();
            }
        }
        Phonebook::where('updated_at', '!=', $timestamp)->delete();
        echo date('H-i');
        $this->info('The phone infos were updated successfully!');
    }

    public function betweenRange($value, $start, $end)
    {
        if ($start <= $value && $value <= $end)
            return true;
        return false;
    }

    public function getEmail($tabel)
    {
        $email = "";
        if ($tabel) {
            try {
                $user = Adldap::search()->select('mail')->findBy('employeenumber', $tabel);
                $email = $user && $user->mail ? $user->mail[0] : "";
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        return $email;
    }

    public function getTabel($name)
    {
        if (strpos($name, '(')) {
            $tabel = substr($name, strpos($name, '(') + 1, 4);
            return $tabel;
        }
        return '';
    }

    public function getDepartment($tabel)
    {
        $department_name = "";
        $position = "";
        $employee = Employee::with('staff')->with('staff.department')->with('staff.position')->where('tabel', $tabel)
            ->first();
        if ($employee && count($employee->staff) > 0 && $employee->staff[0]->department) {
            $department_name = $employee->staff[0]->department->department_code . ' - ' . $employee->staff[0]->department->name_uz_latin;
            $position = $employee->staff[0]->position->name_uz_latin;
        }
        return [$department_name, $position];
    }
}
