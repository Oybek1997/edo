<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Models\Employee;
use App\Http\Models\TariffScale;

class GetTariffScales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:tariff-scales';

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
                        ->post('https://edo-db2.uzautomotors.com/api/as400/delete-tariff-scales/'.$tn.'/'.$kat);
                }
            }
            // echo $value['z251tn'];
            // echo substr($value['z251kat'], 0, 4);
        }
    }
}
