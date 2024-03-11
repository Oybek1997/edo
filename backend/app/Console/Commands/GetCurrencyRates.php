<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\CurrencyHistory;
use App\Http\Models\Currency;
use \GuzzleHttp\Client;

class GetCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts received currency rates into database';

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
        $client = new Client(['base_uri' => 'https://nbu.uz/uz/exchange-rates/json']);
        $response = $client->get('https://nbu.uz/uz/exchange-rates/json', ['verify' => false]);
        $response = json_decode($response->getBody()->getContents(), true);
        $currencies = Currency::get();
        foreach ($currencies as $key => $data) {
            $result = collect($response)->where('code',$data->code)->first();

                $model = CurrencyHistory::where(
                    "date",
                    date('Y-m-d', strtotime($result['date']))
                )->where('currency_id', $data->id)->first();
                if (!$model) {
                    $model = new CurrencyHistory();
                }
                    $model->currency_id = $data->id;
                    $model->price = $result['cb_price'];
                    $model->date = date('Y-m-d', strtotime($result['date']));
                    $model->save();

        }
        $this->info('The received rates were inserted successfully!');
        }
    }

