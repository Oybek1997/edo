<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Models\Employee;
use App\Http\Models\Coefficient;
use App\Http\Models\EmployeeCoefficient;

class GetCoefficients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:coefficients';

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
        // echo 'stopped';
        // return 0;
        $response = Http::withoutVerifying()
            ->post('https://edo-db2.uzautomotors.com/api/as400/get-coefficients');
        foreach ($response->json() as $key => $value) {
            $tn = $value['z211tn'];
            $tip = $value['z211tipk'];
            $koef = $value["z211koef"];
            try {
                $Z211BUYNO = $value["z211buyno"];                
            } catch (\Throwable $th) {
                echo json_encode($value);
            }
            $Z211BUYDT = $value["z211buydt"];
            $emp = Employee::where('tabel', $tn)->first();
            if ($emp) {
                $c = Coefficient::where('code', substr($tip, 0, 1))->first();
                if ($c) {
                    $employeeCoefficient = new EmployeeCoefficient();
                    $employeeCoefficient->employee_id = $emp->id;
                    $employeeCoefficient->coefficient_id = $c->id;
                    $employeeCoefficient->percent = $koef;
                    $employeeCoefficient->order_number = $Z211BUYNO; // 20230101
                    $employeeCoefficient->order_date = substr($Z211BUYDT, 0, 4) . '-' . substr($Z211BUYDT, 4, 2) . '-' . substr($Z211BUYDT, 6, 2);
                    $employeeCoefficient->save();
                    echo $tn;
                    echo '----';
                    echo $tip;
                    echo '----';
                    echo $koef;
                    Http::withoutVerifying()
                        ->post('https://edo-db2.uzautomotors.com/api/as400/delete-coefficients/' . $tn . '/' . $koef . '/' . substr($tip, 0, 1));
                }
            }
        }
    }
}
