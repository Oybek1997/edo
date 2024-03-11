<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Department;
use App\Http\Models\Document;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentDetailFakt;

use App\Http\Models\DocumentDetailAttributeValue;
use Illuminate\Http\Request;

class DocumentDetailAttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function getGuestInformation($passport){
        $passportSeriya = substr($passport, 0,2);
        $passportNumber = substr($passport, 2);
        $guestPassport = DocumentDetailAttributeValue::where('d_d_attribute_id', 2385)->where('attribute_value', $passportNumber)->pluck('document_detail_id')->toArray();
        // $guestPassport = $guestPassport->where('d_d_attribute_id', 2384)->where('attribute_value', $passportSeriya)->first();
        if($guestPassport){

            $guest = DocumentDetailAttributeValue::whereIn('document_detail_id', $guestPassport)->where('d_d_attribute_id', 2384)->where('attribute_value', $passportSeriya)->first();
            if($guest){
                $guest = DocumentDetailAttributeValue::where('document_detail_id', $guest->document_detail_id)->get();
                return $guest;
            }
            else{
                return $guest;
            }
        }
        return 0;
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
     * @param  \App\Http\Models\DocumentDetailAttributeValue  $documentDetailAttributeValue
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentDetailAttributeValue $documentDetailAttributeValue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\DocumentDetailAttributeValue  $documentDetailAttributeValue
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentDetailAttributeValue $documentDetailAttributeValue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\DocumentDetailAttributeValue  $documentDetailAttributeValue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentDetailAttributeValue $documentDetailAttributeValue)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\DocumentDetailAttributeValue  $documentDetailAttributeValue
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentDetailAttributeValue $documentDetailAttributeValue)
    {
        //
    }
    

   
}
