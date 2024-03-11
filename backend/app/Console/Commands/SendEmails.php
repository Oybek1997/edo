<?php

namespace App\Console\Commands;

use App\Http\Models\DocumentSigner;
use App\MailQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send drip e-mails to a user';

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
        // DB::select("TRUNCATE TABLE workflow_log.inventory_unique_products;");
        // DB::select("INSERT INTO workflow_log.inventory_unique_products(warehouse_id, part_number, product_name, stock_1c) SELECT pl.warehouse_id,pl.part_number,pl.product_name,sum(pl.stock) 1C FROM workflow_log.inventory_product_lists pl GROUP by pl.warehouse_id,pl.part_number, pl.product_name");
        // for ($i=0; $i < 100; $i++) {
        //     # code...
        //     $faker = app(Generator::class);
        //     MailQueue::create([
        //         'address' => $faker->safeEmail(),
        //         'content' => $faker->sentence(),
        //         'title' => $faker->word,
        //         'status' => 0,
        //         'try_count' => 0,
        //     ]);
        // }

        if (file_exists('d:\OpenServer\domains\localhost\workflow\backend\storage\logs\laravel.log')) {
            unlink('d:\OpenServer\domains\localhost\workflow\backend\storage\logs\laravel.log');
        }

        $mailQueues = MailQueue::where('status', 0)->where('try_count', '<', 3)->take(50)->get();
        foreach ($mailQueues as $key => $value) {
            try {
                $details = [
                    'title' => $value->title,
                    'content' => $value->content,
                    'footer' => ''
                ];
                echo $value->address.',  ';
                if(!in_array($value->address, ['Viktoriya.Serikova@uzavtosanoat.uz', 'Shavkat.Umurzakov@uzavtosanoat.uz', 'shukurov.a@uzavtosanoat.uz','Rovshan.Abdurakhmanov@uzavtosanoat.uz'])){
                    Mail::to($value->address)->send(new SendMail($details));
                }
                $value->try_count = $value->try_count + 1;
                $value->status = 1;
                $value->save();

                $sent_sms = DocumentSigner::where('document_id', $value->document_id)->where('staff_id', 1)->first();
                if ($value->user_phone && $sent_sms && false) {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "http://91.204.239.44/broker-api/send",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\r\n \"messages\":\r\n [\r\n {\r\n  \"recipient\":\"".$value->user_phone."\",\r\n
                            \"message-id\":\"abc0".$value->id."\",\r\n\r\n
                            \"sms\":{\r\n\r\n
                                \"originator\": \"UzAuto\",\r\n
                                \"content\": {\r\n
                                    \"text\": \"EDO: Sizda bosh direktorga yo'naltirilgan hujjat mavjud. Iltimos tezroq ko'rib chiqing.\"\r\n
                                }\r\n
                            }\r\n
                        }\r\n     ]\r\n}",
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json",
                            "Authorization: Basic dXphdXRvbW90b3JzMjpHMGlUI0Nnc2I="
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                }
                // echo $response;
            } catch (\Throwable $th) {
                throw $th;
                // dd($th);
            }
        }
        echo "8";
        return 0;
    }
}
