<?php

namespace App\Http\Controllers;

use App\EdofilesTmp;
use App\Http\Models\Document;
use App\Http\Models\File;
use App\Services\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function getCaptcha(Request $request) {
        $filename = $request->input('filename');
        // Create the image
        $im = imagecreatetruecolor(100, 25);
        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 100, 24, $white);
        // The text to draw
        $text = rand(10000, 99999);
        // Replace path by your own font path
        $font = public_path(). '\fonts\captcha.ttf';
        // Add some shadow to the text
        imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);
        // Add the text
        imagettftext($im, 20, 0, 10, 20, $black, $font, $text);
        // Using imagepng() results in clearer text compared with imagejpeg()
        ob_start();
        imagepng($im);
        $image_data = ob_get_contents();
        ob_end_clean();
    
        $tmp = EdofilesTmp::insert([
            'name' => $filename,
            'number' => $text,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return base64_encode($image_data);
    }

    public function fileCaptcha($filename) {
        // Create the image
        $im = imagecreatetruecolor(100, 25);
        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 100, 24, $white);
        // The text to draw
        $text = rand(10000, 99999);
        // Replace path by your own font path
        $font = public_path(). '/fonts/captcha.ttf';
        // Add some shadow to the text
        imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);
        // Add the text
        imagettftext($im, 20, 0, 10, 20, $black, $font, $text);
        // Using imagepng() results in clearer text compared with imagejpeg()
        ob_start();
        imagepng($im);
        $file = ob_get_contents();
        ob_end_clean();
        $path = sys_get_temp_dir() . '/';
        $file_path =  time() . rand(100, 1000).'.png';
    
        file_put_contents($path.$file_path, $file);
        $tmp = EdofilesTmp::insert([
            'name' => $filename,
            'number' => $text,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return response()->file($path.$file_path, ['Content-Type' => 'image/png']);
    }

    public function fileDownload($id) {
        $file = File::where('physical_name', $id)->first();
        if ($file) {
            $path = Storage::path('documents\\' . $file->physical_name);
            return response()->file($path);
        }
        return '';
    }

    public function getFile(Request $request) {
        $filename = $request->input('filename');
        $captchaNumber = $request->input('captchaNumber');
        $document = Document::where('pdf_file_name', $filename)->first();
        // dd($document);
        // Document::savePdf($document->id);
        $document = Document::where('pdf_file_name', $filename)
                            ->whereIn('status', [3,4,5])
                            ->with(['documentSigners' => function ($q) {
                                $q->with('signerEmployee')
                                    ->with('signerEmployee.user')
                                    ->orderBy('sequence', 'asc');
                            }])
                            ->first();
        if (!$document) {
            return 1;
        }
    
        // $tmp = EdofilesTmp::where('number', $captchaNumber)->where('name', $filename)->first();
        // if (!$tmp) {
        //     return 2;
        // }
    
        $document_files = File::where('object_id', $document->id)
                ->where('object_type_id', 5)
                ->get();
    
        return json_encode(['document' => $document->makeVisible('pdf', 'eimzoinfo'), 'actionTypes' => Document::actionTypesSort(),'document_files' => $document_files]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function dashboard($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->dashboardData();
    }

    public function dashboardMobile($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->dashboardDataMobile();
    }

    public function dashboardRegistry($id, $locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->getDocumentsByTemplateId($id);
    }

    public function vacationRegistry($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->vacationRegistry();
    }
    public function IshRejimiRegistry($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->ishRejimiRegistry();
    }
    public function otgulRegistry($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->otgulRegistry();
    }
    public function educationRegistry($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->educationRegistry();
    }
    public function categoryChangeRegistry($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->categoryChangeRegistry();
    }
    public function tabelRegistry($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->tabelRegistry();
    }

    public function businessTripRegistry($locale) {
        $dashboard = new Dashboard($locale);
        return $dashboard->businessTripRegistry();
    }

    public function generatePdfForSignDocument(Request $request)
    {
        return Document::generatePdfForSignDocument($request->all());
    }
}
