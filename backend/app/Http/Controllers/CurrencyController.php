<?php

namespace App\Http\Controllers;

use App\Http\Models\Currency;
use App\Http\Models\CurrencyHistory;
use \GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
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
        $currency = Currency::select();

        if (isset($search)) {
            $currency->where(function ($query) use ($search) {
                return $query
                    ->where('code', 'like', "%" . $search . "%")
                    ->orWhere('name', 'like', "%" . $search . "%");
            });
        }
        return $currency->orderBy('id')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
     * @param  \App\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function edit(Currency $currency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Currency $currency)
    {
        $model = Currency::find($request->input('id'));
        if (!$model) {
            $model = new Currency();
        } else {
        }
        $model->code = $request['code'];
        $model->name = $request['name'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency, $id)
    {
        $model = Currency::find($id);
        $model->delete();
    }
    public function getCurrencyRate(){
      $response = Http::retry(3,100)->get('https://nbu.uz/uz/exchange-rates/json')->json();
     return [
         $response[3],
         $response[4],
         $response[7],
         $response[10],
         $response[11],
         $response[18],
         $response[23]
     ];
    }
}
