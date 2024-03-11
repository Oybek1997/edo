<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use SimpleXMLElement;
use App\Http\Models\Nelekvid;

class GetNelekvidReport extends Command
{
    protected $signature = 'sap:get-nelekvid-report';

    protected $description = 'Gets Nelekvid products report and inserts into database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(){
        Nelekvid::truncate();
        $this->send(2100);
        $this->send(2200);
        $this->send(2300);
    }

    public function send($manufacture_code)
    {
        echo date('H-i') . ' - ';
        $curl = curl_init();

        try {
            // $url = 'https://sapss5.s4h.uzauto.uz/sap/bc/srt/rfc/uzauto/esi_sr_illiquids/300/uzauto_esi_sr_illiquids/uzauto_esi_sr_illiquid_asset';
            $url = 'https://sapss6.s4h.uzauto.uz/sap/bc/srt/rfc/uzauto/esi_sr_illiquids/300/uzauto_esi_sr_illiquids/uzauto_esi_sr_illiquid_asset';
            $xml = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:urn="urn:sap-com:document:sap:soap:functions:mc-style">
                <soap:Header/>
                <soap:Body>
                    <urn:_-uzauto_-esiFmIlliquidAssets>
                        <IvPeriod>400</IvPeriod>
                        <IvWerksD>'.$manufacture_code.'</IvWerksD>
                    </urn:_-uzauto_-esiFmIlliquidAssets>
                </soap:Body>
                </soap:Envelope>';

            $credentials = base64_encode('EWM_FINDPART:Welcome123');
            $options = [
                'headers' => [
                    'Content-Type' => 'application/soap+xml; charset=UTF8',
                    'Authorization' => 'Basic ' . $credentials,
                    'SOAPAction' => 'urn:sap-com:document:sap:soap:functions:mc-style/_-UZAUTO_-ESI_SR_ILLIQUIDS/_-uzauto_-esiFmIlliquidAssetsRequest',
                ],
                'body' => $xml
            ];

            $client = new Client([
                'verify' => false,
            ]);

            $response = $client->request('POST', $url, $options);

            $xxml = response($response->getBody()->getContents(), 200, [
                'Content-Type' => 'text/xml'
            ]);
            $dresponse = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xxml->content());
            $dxml = new SimpleXMLElement($dresponse);

            $dbody = $dxml->xpath('//EtOutputData')[0];

            if (count($dbody)) {
                if (count($dbody) > 1) {
                    $array = json_decode(json_encode((array)$dbody), TRUE);
                    foreach ($array['item'] as $key => $value) {
                        $nelekvid = new Nelekvid();
                        $nelekvid->manufacture = $manufacture_code;
                        $nelekvid->product = $value['Material'];
                        $nelekvid->days = $value['Period'];
                        $nelekvid->location = $value['StorageLocation'];
                        $nelekvid->stock = $value['FreeStock'];
                        $nelekvid->unit = $value['Unit'];
                        $nelekvid->save();
                    }
                }
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        echo date('H-i');
        $this->info('The phone infos were updated successfully!');
    }
}
