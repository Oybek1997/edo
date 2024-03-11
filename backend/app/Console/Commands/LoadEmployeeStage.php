<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Models\Employee;
use App\Http\Models\TariffScale;

class LoadEmployeeStage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:load-employee-stage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $employees = Employee::limit(200)
            ->where('is_active',true)
            ->where('tabel','G101')
            ->whereHas('staff')
            ->orderBy('updated_at', 'asc')
            ->get()->pluck('tabel')
            ->toArray();
            // echo json_encode($employees);
        $response = Http::withoutVerifying()->post('https://edo-db2.uzautomotors.com/api/get-stages', $employees);
            foreach ($response->json() as $key => $value) {
                $date = date('Y-m-d', strtotime($value[0]));
                $d1 = new \DateTime($date);
                $d2 = new \DateTime(date('Y-m-d'));
                $diff = $d2->diff($d1);
                $emp = Employee::where('tabel',$key)->first();
                // if($emp)
                {
                    $emp->experience = intval($diff->y) + intval($value[1]);
                    $emp->updated_by = 1;
                    $emp->save();
                }
                echo json_encode([$emp->tabel, $response->json(),$d1 , intval($value[1])]);
                return 1;
            echo $key." - ";
        }
        return 0;
    }

    public function ______handle()
    {
        $response = Http::withoutVerifying()
            ->post('https://edo-db2.uzautomotors.com/api/as400/get-tariff-scales');
        foreach ($response->json() as $key => $value) {
            $tn = $value['z251tn'];
            $kat = $value['z251kat'];
            $emp = Employee::where('tabel', $tn)->first();
            if ($emp) {
                $ts = TariffScale::where('category', substr($kat, 0, 4))->first();
                if ($ts) {
                    $emp->tariff_scale_id = $ts->id;
                    $emp->save();
                    echo $tn;
                    echo '----';
                    echo $kat;
                    echo Http::withoutVerifying()
                        ->post('https://edo-db2.uzautomotors.com/api/as400/delete-tariff-scales/' . $tn . '/' . $kat);
                }
            }
            // echo $value['z251tn'];
            // echo substr($value['z251kat'], 0, 4);
        }
    }
}
