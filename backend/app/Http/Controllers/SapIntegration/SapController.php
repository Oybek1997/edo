<?php

namespace App\Http\Controllers\SapIntegration;

use GuzzleHttp\Client;
use SimpleXMLElement;
use Illuminate\Http\Request;
use App\Http\Models\SapIntegration\Nelekvid;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\SapIntegration\WarehouseResponsible;
use App\Http\Models\SapIntegration\Warehouse;

class SapController extends Controller
{
    public function nolekvidReport(Request $request)
    {
        // $per_page = $request->input('perPage');
        $filter = $request->input('filter');
        $page = $request->input('page');
        $itemsPerPage = $request->input('perPage');
        $offset = ($page * $itemsPerPage) - $itemsPerPage;

        // if(isset($per_page)){
        //     $data = Nelekvid::query();
        // }
        $data = Nelekvid::query();
        // $data = Nelekvid::select(
        //     DB::raw('min(id) as id'),
        //     DB::raw('product'),
        //     DB::raw('min(manufacture) as manufacture'),
        //     DB::raw('min(location) as location'),
        //     DB::raw('min(days) as days'),
        //     DB::raw('min(stock) as stock'),
        //     DB::raw('min(unit) as unit'),
        //     DB::raw('created_at')
        // );
        //$data=DB::connection('workflow_sap')->table('workflow_sap.nelekvids')->count();
        // return $data->get();
        if ($filter['location']) {
            $data->where('location', 'like', '%' . $filter['location'] . '%');
        }
        if ($filter['product']) {
            $data->where('product', 'like', '%' . $filter['product'] . '%');
        }
        if ($filter['days']) {
            $data->where('days', $filter['days']);
        }
        if ($filter['stock']) {
            $data->where('stock', $filter['stock']);
        }
        if ($filter['unit']) {
            $data->where('unit', 'like', '%' . $filter['unit'] . '%');
        }
        if ($filter['manufacture']) {
            $data->where('manufacture', 'ilike', '%' . $filter['manufacture'] . '%');
            $total = count($data->get()->groupBy('product'));
            $data2 = collect($data->skip($offset)->take($itemsPerPage)->get()->groupBy('product'));
            // return $data2->skip($offset)->take($itemsPerPage)
            return [$data2, $total, $offset];
        }
        return [];
    }
    public function send($manufacture_code)
    {
        // echo date('H-i') . ' - ';
        $curl = curl_init();
        // return $manufacture_code;
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

            // $credentials = base64_encode('AKILICHBEKOV:Asilbek1212');
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
            
            // return $response;
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
        // $this->info('The phone infos were updated successfully!');
    }
    public function warehouseFinder(Request $request)
    {
        // echo date('H-i') . ' - ';
        $curl = curl_init();
        $filter = $request->input('filter');
        $workplace = $filter['workplace'];
        $warehouse = $filter['warehouse'];
        $currentDate = $filter['currentDate'];
        $from = $currentDate.'-01';
        $to = date('Y-m-t', strtotime($currentDate));
        // return $to;
        // return [$workplace, $warehouse, $from, $to];
        try {
            // $url = 'https://sapss5.s4h.uzauto.uz/sap/bc/srt/rfc/uzauto/esi_sr_illiquids/300/uzauto_esi_sr_illiquids/uzauto_esi_sr_illiquid_asset';
            // $url = 'http://sapss5.s4h.uzauto.uz/sap/bc/srt/rfc/uzauto/esi_sr_stock/300/uzauto_esi_sr_stock/uzauto_esi_sr_stocks';
            $url = 'https://sapss5.s4h.uzauto.uz/sap/bc/srt/rfc/uzauto/esi_sr_stock/300/uzauto_esi_sr_stock/uzauto_esi_sr_stock_overview';
            $xml = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:urn="urn:sap-com:document:sap:soap:functions:mc-style">
            <soap:Header/>
             <soap:Body>
               <urn:_-uzauto_-esiFmStockReview>
                  <IvBegPeriod>'. $from .'</IvBegPeriod>
                  <IvEndPeriod>'. $to .'</IvEndPeriod>
                  <IvLgort>'. $warehouse .'</IvLgort>
                  <IvWerks>'. $workplace .'</IvWerks>
               </urn:_-uzauto_-esiFmStockReview>
             </soap:Body>
            </soap:Envelope>';
                     
            // $credentials = base64_encode('AKILICHBEKOV:Asilbek1212');
            $credentials = base64_encode('AKILICHBEKOV:Asilbek1212');
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
            // dd($response);
      
            $xxml = response($response->getBody()->getContents(), 200, [
                'Content-Type' => 'text/xml'
            ]);
            $dresponse = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xxml->content());
            $dxml = new SimpleXMLElement($dresponse);
            
            // return $response;
            $dbody = $dxml->xpath('//EtOutput')[0];
            $svod1 = $dxml->xpath('//EtSvod')[0];
            $overall1 = $dxml->xpath('//EtOverall')[0];
            $array = json_decode(json_encode((array)$dbody), TRUE);
            $svod = json_decode(json_encode((array)$svod1), TRUE);
            $overall = json_decode(json_encode((array)$overall1), TRUE);
            // return $array['item'];
            return [$array, $svod, $overall];
            // if (count($dbody)) {
            //     if (count($dbody) > 1) {
            //         foreach ($array['item'] as $key => $value) {
            //             $nelekvid = new Nelekvid();
            //             $nelekvid->manufacture = $manufacture_code;
            //             $nelekvid->product = $value['Material'];
            //             $nelekvid->days = $value['Period'];
            //             $nelekvid->location = $value['StorageLocation'];
            //             $nelekvid->stock = $value['FreeStock'];
            //             $nelekvid->unit = $value['Unit'];
            //             $nelekvid->save();
            //         }
            //     }
            // }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        echo date('H-i');
        // $this->info('The phone infos were updated successfully!');
    }
    public function MaterialFinder(Request $request)
    {
        // echo date('H-i') . ' - ';
        $curl = curl_init();
        $filter = $request->input('search');
        $filter = mb_strtoupper($filter);
        // return $filter;
        // $workplace = $filter['workplace'];
        // $warehouse = $filter['warehouse'];
        // $from = $filter['from_date'];
        // $to = $filter['to_date'];
        // return [$workplace, $warehouse, $from, $to];
        try {
            // $url = 'https://sapss5.s4h.uzauto.uz/sap/bc/srt/rfc/uzauto/esi_sr_illiquids/300/uzauto_esi_sr_illiquids/uzauto_esi_sr_illiquid_asset';
            // <IvRname>BRACKET-BRAKE RESERVIOR NO1</IvRname> 
            $url = 'https://sapss5.s4h.uzauto.uz/sap/bc/srt/rfc/uzauto/esi_sr__edo/300/uzauto_esi_sr__edo/uzauto_esi_sr_int_edo';
        
            $xml = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:urn="urn:sap-com:document:sap:soap:functions:mc-style"> 
            <soap:Header/> 
                <soap:Body> 
                    <urn:_-uzauto_-esiFmUnique> 
                    <IvMatnr></IvMatnr> 
                    <IvRname>%'.$filter.'%</IvRname> 
                    <IvWerks>2200</IvWerks> 
                    </urn:_-uzauto_-esiFmUnique> 
                </soap:Body> 
            </soap:Envelope>';

            $credentials = base64_encode('Molimjonova:12345678');

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
            $array = json_decode(json_encode((array)$dbody), TRUE);
            return $array;
            // return $array['item'];
            // if (count($dbody)) {
            //     if (count($dbody) > 1) {
            //         foreach ($array['item'] as $key => $value) {
            //             $nelekvid = new Nelekvid();
            //             $nelekvid->manufacture = $manufacture_code;
            //             $nelekvid->product = $value['Material'];
            //             $nelekvid->days = $value['Period'];
            //             $nelekvid->location = $value['StorageLocation'];
            //             $nelekvid->stock = $value['FreeStock'];
            //             $nelekvid->unit = $value['Unit'];
            //             $nelekvid->save();
            //         }
            //     }
            // }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        echo date('H-i');
        // $this->info('The phone infos were updated successfully!');
    }
    public function getWorkplaces(){
        $id = Auth::user()->employee->id;
        $warehouse = WarehouseResponsible::where('responsible_employee_id', $id)->with('warehouse.workplace')->get();
        return $warehouse;
        return DB::connection('workflow_sap')
            ->table('workplaces')
            ->get();
    }
    public function getWarehouses(Request $request){
        $workplace = $request->input('workplace');
        if($workplace){

            $workplace_id = DB::connection('workflow_sap')
            ->table('workplaces')
            ->where('code', $workplace)
            ->first();
    
            // dd($workplace_id);
            return DB::connection('workflow_sap')
                ->table('warehouses')
                ->where('workplace_id', $workplace_id->id)
                ->get();
        }
        else{
            return Warehouse::with('workplace')
                            ->get();
        }
    }
}
