<?php

namespace App\Http\Controllers;

use App\Http\Models\Document;
use App\Http\Models\DocumentSigner;
class TestController extends Controller
{
    public function signingReport()
    {
        $ds = Document::where('id','<',1000);
        $ds->get()->toArray();

        dd($ds[0]);
    }
}
