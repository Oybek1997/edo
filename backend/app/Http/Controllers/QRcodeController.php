<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRcodeController extends Controller
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
    public function qrcodeImport(Request $request)
    {
        try {
            $fileName = microtime(true) * 1000 . '.' . $request->file->extension();
            $request->file->move(public_path('uploads'), $fileName);

            $sheetname = 'Дата база (сариқ)';
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load(public_path('uploads') . '/' . $fileName);
            $data = $spreadsheet->getSheet(0)->toArray();

            unset($data[0]);
            return $this->generatePdf($data);
            unlink(public_path('uploads') . '/' . $fileName);
            return $order;
            // return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function generatePdf($data)
    {
        $content = '';
        foreach ($data as $key => $value) {
            $text = $value[1];
            $text = $value[0].chr(9).$value[1].chr(10);
            $content .= '<div style="margin-bottom:30px; text-align:center; float: left;">';
            $content .= '<div style="max-width:220px; margin-left:80px; text-align:center; ">';
            $content .= '<img style="margin-bottom:10px;" width="200" height="200" src="data:image/png;base64,' . base64_encode(QrCode::color(0, 90, 169)->format('png')->encoding('UTF-8')->size(120)->generate($text)) . '"/>';
            $content .= '<p style="">';
            $content .= $value[1];
            $content .= '</p>';
            $content .= '</div>';
            $content .= '</div>';
        }

        $pdf = \App::make('snappy.pdf.wrapper');
        $pdf->setOption('images', true)
            ->setPaper('a4')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return $base64;
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
