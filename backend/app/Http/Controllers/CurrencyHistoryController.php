<?php

namespace App\Http\Controllers;

use App\Http\Models\CurrencyHistory;
use App\Http\Models\Currency;
use \GuzzleHttp\Client;
use Illuminate\Http\Request;

class CurrencyHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $search = $request->input('search');
        $currency = CurrencyHistory::with('currency')->select();

        if (isset($search)) {
            $currency->where(function ($query) use ($search) {
                return $query
                    ->where('currency_name', 'like', "%" . $search . "%")
                    ->orWhere('price', 'like', "%" . $search . "%")
                    ->orWhere('date', 'like', "%" . $search . "%");
            });
        }
        return $currency->orderBy('date', 'desc')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\CurrencyHistory  $currencyHistory
     * @return \Illuminate\Http\Response
     */
    public function show(CurrencyHistory $currencyHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CurrencyHistory  $currencyHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(CurrencyHistory $currencyHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CurrencyHistory  $currencyHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CurrencyHistory $currencyHistory)
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
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CurrencyHistory  $currencyHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(CurrencyHistory $currencyHistory)
    {
        //
    }
}
