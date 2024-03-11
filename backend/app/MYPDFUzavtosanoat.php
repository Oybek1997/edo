<?php

namespace App;

use TCPDF;
use TCPDF_FONTS;

class MYPDFUzavtosanoat extends TCPDF
{

    // public function __construct(){
    //     // add a page
    //     // $fontname = TCPDF_FONTS::addTTFfont('D:/TIMCYR.ttf', 'TrueTypeUnicode', '', 96);
    //     // $fontnameb = TCPDF_FONTS::addTTFfont('D:/timesbd.ttf', 'TrueTypeUnicode', '', 96);
    //     // $fontnamebi = TCPDF_FONTS::addTTFfont('D:/timesbi.ttf', 'TrueTypeUnicode', '', 96);
    //     // $fontnamei = TCPDF_FONTS::addTTFfont('D:/timesb.ttf', 'TrueTypeUnicode', '', 96);

    //     // // use the font
    //     // $this->SetFont($fontname, '', 14, '', false);
    // }

    public function setPerformer($performer)
    {
        $this->performer = $performer;
    }

    public $performer;
    public $company_id;
    protected $compress;

    protected $last_page_flag = false;

    public function Close()
    {
        $this->last_page_flag = true;
        parent::Close();
    }

    //Page header
    public function Header()
    {
        // if($this->last_page_flag)
        {
            $this->SetY(290);
            $this->SetX(220);
            // $fontname = TCPDF_FONTS::addTTFfont('d:\OpenServer\domains\localhost\workflow\backend\storage\app\Dompdf\fonts\Times New Roman\Times New Roman.ttf', 'TrueTypeUnicode', '', 96);
            // use the font
            $this->SetFont('freeserif', '', 10, '', false);
            // Title
            $this->Cell(0, 15, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }
    }

    // Page footer
    public function Footer()
    {
        if ($this->page != 1)
        {
            // Position at 15 mm from bottom
            $this->SetY(-25);
            $this->SetX(25);
            // Set font
            // $fontname = TCPDF_FONTS::addTTFfont('d:\OpenServer\domains\localhost\workflow\backend\storage\app\Dompdf\fonts\Times New Roman\Times New Roman.ttf', 'TrueTypeUnicode', '', 96);
            // use the font
            $this->SetFont('freeserif', '', 8, '', false);
            // Page number
            $this->Cell(0, 10, $this->performer[0], 0, false, 'L', 0, '', 0, false, 'T', 'M');
            $this->SetY(-21);
            $this->SetX(25);
            $this->Cell(0, 10, $this->performer[1], 0, false, 'L', 0, '', 0, false, 'T', 'M');
        }
    }
}
