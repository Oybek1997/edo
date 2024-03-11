<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PostOrder;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class PostOrderController extends Controller
{
    public function upload(Request $request)
    {
        DB::beginTransaction();
        try {
            $uploadedFile = $request->file('file');
            // $filename = time().$uploadedFile->getClientOriginalName();
            $part = microtime(true);
            $fileName = microtime(true) . '.' . $request->file->extension();
            $request->file->move(public_path('uploads'), $fileName);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load(public_path('uploads') . '/' . $fileName);
            $data = $spreadsheet->getSheet(0)->toArray();

            unset($data[0]);
            // dd($data);
            $date = date('Y-m-d H:i:s');
            foreach ($data as $key => $value) {
                // if ($value[8] && $value[10] && $value[8] != '#Н/Д' && $value[8] != '#N/A' && $value[10] != '#Н/Д' && $value[10] != '#N/A') 
                {
                    PostOrder::create([
                        "number" => $value[0],
                        "send_data" => date('Y-m-d', strtotime($value[1])),
                        "client" => $value[2],
                        "contract_number" => $value[3],
                        "address" => $value[8],
                        "region" => $value[4],
                        "region_id" => $value[5],
                        "area" => $value[6],
                        "area_id" => $value[7],
                        "part_number" => $date,
                        "model" => $value[9] == 1 ? 1 : 0,

                        // "diller" => $value[0],
                        // "model" => $value[2],
                        // "modification" => $value[3],
                        // "color" => $value[4],
                        // "estimated_delivery_date" => $value[8] ? $value[8] : null,
                        // "client_type" => $value[11] == "Юридическое лицо" ? 1 : 0,
                        // "inn" => $value[10],
                        // "pinfl" => $value[13],
                        // "user_type" => $value[12],
                        // "group_code" => $value[16],
                        // "status" => 0,
                    ]);
                }
            }
            unlink(public_path('uploads') . '/' . $fileName);
            DB::commit();
            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function part()
    {
        return $orders = PostOrder::select('part_number as part')->where('part_number','>','2022-11-30')->groupBy('part_number')->get();
    }

    public function getData($part)
    {
        if ($part != 'null') {
            return [
                PostOrder::where('status', 0)->where('part_number', $part)->get(),
                PostOrder::where('status', 0)->where('part_number', $part)->count(),
                PostOrder::where('status', 1)->where('part_number', $part)->count(),

            ];
        } else {
            return [[], 0, 0];
            // return PostOrder::where('part_number', $part)->limit(20)->get();
        }
    }

    public function getPdf()
    {
        // return $pdf = base64_encode(PostOrder::generatePdf(1));
        $order = PostOrder::where('status', 0)->first();
        $token = $this->getToken();
        if ($order) {
            $response = $this->send($order);
            $order->response = json_encode($response);
            $order->save();
            $order->pdf = $this->getHash($response->Id, $token);
            // $order->pdf = base64_encode(file_get_contents(public_path('sample.pdf')));
            return $order;
        }
        return null;
    }

    public function getPdfNew($part)
    {
        // return $pdf = base64_encode(PostOrder::generatePdf(1));
        $order = PostOrder::where('status', 0)->where('part_number', $part)->first();
        $token = $this->getToken();
        if ($order) {
            $response = $this->send($order);
            $order->response = json_encode($response);
            $order->save();
            $order->pdf = $this->getHash($response->Id, $token);
            // $order->pdf = base64_encode(file_get_contents(public_path('sample.pdf')));
            return $order;
        }
        return null;
    }

    public function send($item)
    {
        $token = $this->getToken();
        $pdf = base64_encode(PostOrder::generatePdf($item->id));
        // $pdf = base64_encode(file_get_contents(public_path('sample.pdf')));

        try {
            $data = $this->sendPdf($token, $pdf, $item->region_id, $item->area_id, $item->address, $item->client);
        } catch (\Throwable $th) {
            $item->status = 2;
            $item->save();
            throw $th;
        }
        return $data;
    }

    public function accept(Request $request)
    {
        $item = $request->all();

        $token = $this->getToken();
        // $pdf = base64_encode(PostOrder::generatePdf($item->id));
        // $pdf = base64_encode(file_get_contents(public_path('sample1.pdf')));
        $res = json_decode($item['response']);

        // return [$token,$item['base64'],$res->Id];
        // $item['base64'];
        $client = new Client(['headers' => ['Authorization' => "Bearer " . $token, "Content-Type" => "application/json", 'Accept' => 'application/json']]);
        $res = $client->request('PUT', 'https://hybrid.pochta.uz/api/SendMail/' . $res->Id, [
            'body' => json_encode(['signature' => $item['base64']])
        ]);
        if ($res->getStatusCode() == 200) {
            $order = PostOrder::find($item['id']);
            $order->status = 1;
            $order->save();
            $order->new_count = PostOrder::where('status', 0)->where('part_number', $order->part_number)->count();
            $order->success_count = PostOrder::where('status', 1)->where('part_number', $order->part_number)->count();
            if ($order) {
                return $order;
                // return $res->getBody()->getContents();
            } else {
                return 500;
            }
        } else return false;
    }

    public function sendPdf($token, $base64, $region, $area, $address, $receiver)
    {
        $client = new Client(['headers' => ['Authorization' => "Bearer " . $token]]);
        $res = $client->request('POST', 'https://hybrid.pochta.uz/api/PdfMail', [
            'form_params' => [
                'Region' => $region,
                'Area' => $area,
                'Address' => $address,
                'Receiver' => $receiver,
                'Document64' => $base64,
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            return json_decode($res->getBody()->getContents());
        } else return false;
    }

    public function getHash($id, $token)
    {
        $token = $this->getToken();
        $client = new Client(['headers' => ['Authorization' => "Bearer " . $token]]);
        $res = $client->request('GET', 'https://hybrid.pochta.uz/api/SendMail/' . $id);
        if ($res->getStatusCode() == 200) {
            return json_decode($res->getBody()->getContents());
        } else return false;
        dd(json_decode($res->getBody()->getContents()));
    }

    public function getToken()
    {
        $client = new Client(['headers' => [
            "Content-Type" => "application/x-www-form-urlencoded",
            "Content-Length" => "55",
        ]]);
        $res = $client->request('POST', 'https://hybrid.pochta.uz/token', [
            'form_params' => [
                'grant_type' => 'password',
                'username' => '998901853612',
                'password' => '111',
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            return json_decode($res->getBody()->getContents())->access_token;
        } else return false;
    }

    public function test($id)
    {
        return '<iframe style="width:100%;height:100vh;" src="data:application/pdf;base64,' . base64_encode(PostOrder::generatePdf($id)) . '">';
    }

    public function info(Request $request)
    {
        $filter = $request->input('filter');
        $orders = PostOrder::query();
        if ($filter['client'] != null) {
            $orders->where('client','like','%'.$filter['client'].'%');
        }
        if ($filter['contract'] != null) {
            $orders->where('contract_number','like','%'.$filter['contract'].'%');
        }
        if ($filter['area'] != null) {
            $orders->where('area','like','%'.$filter['area'].'%');
        }
        if ($filter['region'] != null) {
            $orders->where('region','like','%'.$filter['region'].'%');
        }
        if ($filter['address'] != null) {
            $orders->where('address','like','%'.$filter['address'].'%');
        }
        return [
            $orders->limit(100)->get(),
            PostOrder::where('status', 0)->count(),
            PostOrder::where('status', 1)->count(),
        ];
    }

    public function view(Request $request)
    {
        $id = $request->input('id');
        if ($id) return base64_encode(PostOrder::generatePdf($id));
        return null;
    }
}
