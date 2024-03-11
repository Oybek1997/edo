<?php

namespace App\Http\Controllers;

use App\Http\Models\SapTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client as Client;
use DOMDocument;
use SimpleXMLElement;

class FindPartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $url = 'https://sapss6.s4h.uzauto.uz/sap/bc/srt/rfc/uzauto/esi_fm_ewm_report/300/ewm_report/ewm_report';

        $p1 = $request->matnr;
        $p2 = $request->werks;
        $xml = '
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:urn="urn:sap-com:document:sap:soap:functions:mc-style">
           <soap:Header/>
           <soap:Body>
              <urn:_-uzauto_-esiFmEwmReport>
                 <IvMatnr>GM'. $p1 . '</IvMatnr>
                 <IvWerks>2100</IvWerks>
              </urn:_-uzauto_-esiFmEwmReport>
           </soap:Body>
        </soap:Envelope>
        ';

        $credentials = base64_encode('EWM_FINDPART:Welcome123');
        $options = [
            'headers' => [
                'Content-Type' => 'application/soap+xml; charset=UTF8',
                'Authorization' => 'Basic ' . $credentials,
                'SOAPAction' => 'urn:sap-com:document:sap:soap:functions:mc-style/_-UZAUTO_-ESI_FM_EWM_REPORT/_-uzauto_-esiFmEwmReportRequest',
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
        $dbody = $dxml->xpath('//EtOutput')[0];
        // return $dbody;
        
        if (count($dbody)) {
                if(count($dbody) > 1) {
                $array = json_decode(json_encode((array)$dbody), TRUE); 
                $for = $array['item'];
                $col = collect($for);
                $grs = $col->groupBy('Sgroup');
                return compact('xxml', 'p1', 'p2',  'grs');
            } 

            else {
                $array = json_decode(json_encode((array)$dbody), TRUE);
                $pp = $array['item'];

                
                return compact('xxml','pp', 'p1', 'p2');
            }
        } 

        else {
            // $request->session()->flash('error', 'So`rov bo`yicha ma`lumot topilmadi');
            $xxml = NULL;
            $p2 = NULL;
            return compact('xxml', 'p1', 'p2');
        }
        
    }
    public function getContainer(Request $request){
        $url = 'http://10.142.61.149:8081/api/search?konteyner=';
        $container = $request->input('container');
        $url2 = $url .$container;
        // return $url2;
        $client = new Client([
            'verify' => false
        ]);
        $response = $client->request(
            'GET',
            $url2
        );
        $data = $response->getBody();
        $str=str_replace("\r\n","",$data);
        return json_decode($str, true);
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
    public function show(SapTransaction $SapTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SapTransaction $SapTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\SapTransaction $SapTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SapTransaction $SapTransaction)
    {
        //
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\SapTransaction $SapTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(SapTransaction $SapTransaction, $id)
    {
        
    }
}
