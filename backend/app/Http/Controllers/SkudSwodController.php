<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Http;

class SkudSwodController extends Controller
{
    public function writeExcel($month, $tabn, $type){
        // return $type;
        $fileName = "excel-template.xlsx";
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('excel') . '/' . $fileName);

        // $spreadsheet->getActiveSheet();
        // $sheet->setCellValue('B1', 'Табел за  августь  2022г.');
        // $sheet->setCellValue('AK1', 'Табел за  августь  2022г.');
        // $response = Http::get('https://edo-db2.uzautomotors.com/api/get-skud-full-manual/3120/2022-09');
        if($type==1){
            $response = Http::get('https://edo-db2.uzautomotors.com/api/get-skud-full-manual/'.$tabn. '/' .$month);
        }
        if($type==2){
            $response = Http::get('https://edo-db2.uzautomotors.com/api/get-skud-manual/'.$tabn. '/' .$month);

        }
        $response2 = json_decode($response->getBody()->getContents(), true);
        // return $response2;
        if($response2){
            $datacount = count($response2);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
            $rangecell = $datacount +5;
            $text = "A5:AX{$rangecell}";
            $row = 5; $allak= 0; $allal= 0; $allal= 0; $allam= 0; $allan= 0; $allao= 0; $allaq= 0; $allar= 0; $allas= 0; $allat= 0; $allau= 0; $allav= 0; $allaw= 0;
             $allax = 0;
            for($i = 0; $i<$datacount; $i++ ){
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $i+1);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $response[$i]['z12tn']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $response[$i]['z08fio']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $response[$i]['z00no']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $response[$i]['z12pch']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $response[$i]['z12d1']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $response[$i]['z12d2']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $response[$i]['z12d3']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $response[$i]['z12d4']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $response[$i]['z12d5']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $response[$i]['z12d6']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $response[$i]['z12d7']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $response[$i]['z12d8']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $response[$i]['z12d9']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $response[$i]['z12d10']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $response[$i]['z12d11']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $response[$i]['z12d12']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $response[$i]['z12d13']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $response[$i]['z12d14']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $response[$i]['z12d15']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $response[$i]['z12d16']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $response[$i]['z12d17']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $response[$i]['z12d18']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $response[$i]['z12d19']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $response[$i]['z12d20']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $response[$i]['z12d21']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $response[$i]['z12d22']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(28, $row, $response[$i]['z12d23']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(29, $row, $response[$i]['z12d24']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(30, $row, $response[$i]['z12d25']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(31, $row, $response[$i]['z12d26']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(32, $row, $response[$i]['z12d27']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(33, $row, $response[$i]['z12d28']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(34, $row, $response[$i]['z12d29']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(35, $row, $response[$i]['z12d30']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(36, $row, $response[$i]['z12d31']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(37, $row, $response[$i]['z12frd']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(38, $row, $response[$i]['z12otp']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(39, $row, $response[$i]['z12blz']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(40, $row, $response[$i]['z12admotp']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(41, $row, $response[$i]['z12prg']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(43, $row, $response[$i]['z12v']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(44, $row, $response[$i]['z12gos']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(45, $row, $response[$i]['z12kom']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(46, $row, $response[$i]['z12otrch']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(47, $row, $response[$i]['z12otrsch']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(48, $row, $response[$i]['z12otrvch']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(49, $row, $response[$i]['z12otrnch']);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(50, $row, $response[$i]['z12votrch']);
                $allak +=$response[$i]['z12frd'];
                $allal +=$response[$i]['z12otp'];
                $allam +=$response[$i]['z12blz'];
                $allan +=$response[$i]['z12admotp'];
                $allao +=$response[$i]['z12prg'];
                $allaq +=$response[$i]['z12v'];
                $allar +=$response[$i]['z12gos'];
                $allas +=$response[$i]['z12kom'];
                $allat +=$response[$i]['z12otrch'];
                $allau +=$response[$i]['z12otrsch'];
                $allav +=$response[$i]['z12otrvch'];
                $allaw +=$response[$i]['z12otrnch'];
                $allax +=$response[$i]['z12votrch'];
                $row++;
            }
    
            $created_date = date('d.m.Y H:i:s');
            $ru_month = array( 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь' );
            $monthrus = substr($month, -2);
            // return $monthrus;
            $monthrus = $ru_month[$monthrus-1];
            $yearrus = substr($month, 0, 4);
            // return $yearrus;
            $spreadsheet->getActiveSheet()->setCellValue('B1', "Табел за  {$monthrus} {$yearrus}г.");
            $spreadsheet->getActiveSheet()->setCellValue('AK1', "Дата распечатки: {$created_date}");
            $spreadsheet->getActiveSheet()->mergeCells("AH{$rangecell}:AJ{$rangecell}");
            $spreadsheet->getActiveSheet()->setCellValue("AH$rangecell", "ИТОГО");
            $spreadsheet->getActiveSheet()->setCellValue("AK$rangecell", $allak);
            $spreadsheet->getActiveSheet()->setCellValue("AL$rangecell", $allal);
            $spreadsheet->getActiveSheet()->setCellValue("AM$rangecell", $allam);
            $spreadsheet->getActiveSheet()->setCellValue("AN$rangecell", $allan);
            $spreadsheet->getActiveSheet()->setCellValue("AO$rangecell", $allao);
            $spreadsheet->getActiveSheet()->setCellValue("AQ$rangecell", $allaq);
            $spreadsheet->getActiveSheet()->setCellValue("AR$rangecell", $allar);
            $spreadsheet->getActiveSheet()->setCellValue("AS$rangecell", $allas);
            $spreadsheet->getActiveSheet()->setCellValue("AT$rangecell", $allat);
            $spreadsheet->getActiveSheet()->setCellValue("AU$rangecell", $allau);
            $spreadsheet->getActiveSheet()->setCellValue("AV$rangecell", $allav);
            $spreadsheet->getActiveSheet()->setCellValue("AW$rangecell", $allaw);
            $spreadsheet->getActiveSheet()->setCellValue("AX$rangecell", $allax);
    
            $signers1 = $rangecell + 2;
            $spreadsheet->getActiveSheet()->mergeCells("F{$signers1}:O{$signers1}");
            $spreadsheet->getActiveSheet()->setCellValue("F$signers1", "Руководитель подразделения(Управления, отдел):");
    
            $spreadsheet->getActiveSheet()->mergeCells("Q{$signers1}:AG{$signers1}");
            $spreadsheet->getActiveSheet()->setCellValue("Q$signers1", "_____________________________________________       ________________");
            
            $signers2 = $signers1 + 2;
            $spreadsheet->getActiveSheet()->mergeCells("F{$signers2}:O{$signers2}");
            $spreadsheet->getActiveSheet()->setCellValue("F$signers2", "Ответственный за табельный учет:");
    
            $spreadsheet->getActiveSheet()->mergeCells("Q{$signers2}:AG{$signers2}");
            $spreadsheet->getActiveSheet()->setCellValue("Q$signers2", "_____________________________________________       ________________");
            
            $signerstyle = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
            ];
            $boldarray = [
                
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
            ];
            $styleArray = [
                
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle($text)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle("AH{$rangecell}:AX{$rangecell}")->applyFromArray($boldarray);
            $spreadsheet->getActiveSheet()->getStyle("F{$signers1}:F{$signers2}")->applyFromArray($signerstyle);
            
            
    
            $randomName = microtime(true) * 1000 . '.xlsx';
            //write it again to Filesystem with the same name (=replace)
            $writer = new Xlsx($spreadsheet);
            $writer->save(public_path('excel'). '/' . $randomName);
            return response()->download(public_path('excel/').$randomName)->deleteFileAfterSend(true);
        }
        else{

            return 0;
        }
    }
}