<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Graphic extends Model
{
    // use SoftDeletes;
    //
    public function document()
    {
        return $this->hasOne('App\Http\Models\Document', 'id', 'document_id');
    }
    public function department()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'department_id');
    }

    public static function graficTable($id)
    {
        $graphicDep  = Self::select('department_id', DB::raw('MIN(id) as id'))
        ->where('document_id', $id)
        ->groupBy('department_id')
        ->orderBy('id', 'ASC')
        ->get();
        $graphic =  Self::select('department_id',
         DB::raw('MIN(year) as year'), 
         DB::raw('MIN(month) as month'),
         DB::raw('MIN(day) as day'))
        ->where('document_id', $id)
        ->groupBy('department_id')
        ->first();
        $year = $graphic->year;
        $month = $graphic->month;
        $day = $graphic->day;
        $monthList = ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
        $week_days = ['Ya', 'Du', 'Se', 'Ch', 'Pa', 'Ju', 'Sh'];
        // dd(($graphic));
        // dd(count($graphicDep));
        $content = '<table style="width:100%;border-collapse: collapse; text-align:center;">';
        foreach ($graphicDep as $key => $values) {

            $command = Self::select('command',
            DB::raw('MIN(document_id) as document_id'), 
            DB::raw('MIN(department_id) as department_id'),
            DB::raw('MIN(day) as day'), 
            DB::raw('MIN(document_id) as document_id'),
            DB::raw('MIN(id) as id'))
            ->where('document_id', $id)
            ->where('department_id', $values->department_id)
            ->groupBy('command')
            ->orderBy('id', 'ASC')
            ->get();

            foreach ($command as $ke => $value) {


                $content .= '<tr>';

                $content .= '<th  colspan="34" style="border: 1px solid black;">';
                $content .= $value->department->department_code . ' ' . $value->department->name_uz_latin;
                $content .= '<br>';
                $content .= $year . ' - yil ' . $monthList[$month * 1 - 1];
                $content .= '<br>';
                $content .= $day ? "Dam olish kuni uchun" : "Ish kuni uchun";
                // $content .= '<br>';
                $content .= ' ('.$value->command.'- komanda)';
                $content .= '</th>';
                $content .= '<th rowspan="2" style="border: 1px solid black;">';
                $content .= 'Fond';
                $content .= '</th>';
                $content .= '<th rowspan="2" style="border: 1px solid black; white-space: normal">';
                $content .= $value->day == 1 ? 'Dam olish kuni <br> ish soatlari' : 'Ish kuni <br> qo`shimcha <br> ish soatlari' ;
                $content .= '</th>';

                $content .= '</tr>';

                $content .= '<tr>';

                $content .= '<td style="border: 1px solid black;width:55px;">';
                $content .= 'Smena';
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;width:55px;">';
                $content .= 'Smena kodi';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-01'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-01'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '01';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-02'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-02'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '02';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-03'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-03'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '03';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-04'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-04'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '04';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-05'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-05'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '05';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-06'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-06'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '06';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-07'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-07'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '07';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-08'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-08'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '08';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-09'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-09'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '09';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-10'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-10'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '10';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-11'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-11'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '11';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-12'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-12'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '12';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-13'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-13'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '13';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-14'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-14'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '14';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-15'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-15'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '15';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-16'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-16'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '16';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-17'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-17'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '17';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-18'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-18'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '18';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-19'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-19'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '19';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-20'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-20'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '20';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-21'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-21'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '21';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-22'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-22'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '22';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-23'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-23'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '23';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-24'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-24'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '24';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-25'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-25'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '25';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-26'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-26'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '26';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-27'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-27'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '27';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-28'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-28'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '28';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-29'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-29'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '29';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-30'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-30'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '30';
                $content .= '</td>';
                $content .= ($week_days[date('w', strtotime($year . '-' . $month . '-31'))] == 'Sh' || $week_days[date('w', strtotime($year . '-' . $month . '-31'))] == 'Ya')  ? '<td style="border: 1px solid black; background-color: #CCC">' : '<td style="border: 1px solid black;">';
                $content .= '31';
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= 'Jami:';
                $content .= '</td>';


                $content .= '</tr>';

                $countd01 = 0;
                $countd02 = 0;
                $countd03 = 0;
                $countd04 = 0;
                $countd05 = 0;
                $countd06 = 0;
                $countd07 = 0;
                $countd08 = 0;
                $countd09 = 0;
                $countd10 = 0;
                $countd11 = 0;
                $countd12 = 0;
                $countd13 = 0;
                $countd14 = 0;
                $countd15 = 0;
                $countd16 = 0;
                $countd17 = 0;
                $countd18 = 0;
                $countd19 = 0;
                $countd20 = 0;
                $countd21 = 0;
                $countd22 = 0;
                $countd23 = 0;
                $countd24 = 0;
                $countd25 = 0;
                $countd26 = 0;
                $countd27 = 0;
                $countd28 = 0;
                $countd29 = 0;
                $countd30 = 0;
                $countd31 = 0;
                $countall = 0;
                $countfond = 0;
                $countsverx = 0;

                // $count = [];

                $gr = Graphic::where('department_id', $value->department_id)->where('command', $value->command)->where('document_id', $id)->orderBy('abcd', 'asc')->get();
                foreach ($gr as $k => $val) {


                    // $count[] = $count[$val]['d01'] + $val['d01']; 
                    // $count[] = $count[$val] ? $count[$val]['d02'] + $val['d02'] : $val['d02']; 
                    // $count[] = $count[$val] ? $count[$val]['d03'] + $val['d02'] : $val['d03'];

                    $all = intval($val->d01) +  intval($val->d02) +  intval($val->d03) +  intval($val->d04) +  intval($val->d05) +  intval($val->d06) +  intval($val->d07) +  intval($val->d08) +  intval($val->d09) +  intval($val->d10) +  intval($val->d11) +  intval($val->d12) +  intval($val->d13) +  intval($val->d14) +  intval($val->d15) +  intval($val->d16) +  intval($val->d17) +  intval($val->d18) +  intval($val->d19) +  intval($val->d20) +  intval($val->d21) +  intval($val->d22) +  intval($val->d23) +  intval($val->d24) +  intval($val->d25) +  intval($val->d26) +  intval($val->d27) +  intval($val->d28) +  intval($val->d29) +  intval($val->d30) +  intval($val->d31);
                    $countd01 = $countd01 + intval($val->d01);
                    $countd02 = $countd02 + intval($val->d02);
                    $countd03 = $countd03 + intval($val->d03);
                    $countd04 = $countd04 + intval($val->d04);
                    $countd05 = $countd05 + intval($val->d05);
                    $countd06 = $countd06 + intval($val->d06);
                    $countd07 = $countd07 + intval($val->d07);
                    $countd08 = $countd08 + intval($val->d08);
                    $countd09 = $countd09 + intval($val->d09);
                    $countd10 = $countd10 + intval($val->d10);
                    $countd11 = $countd11 + intval($val->d11);
                    $countd12 = $countd12 + intval($val->d12);
                    $countd13 = $countd13 + intval($val->d13);
                    $countd14 = $countd14 + intval($val->d14);
                    $countd15 = $countd15 + intval($val->d15);
                    $countd16 = $countd16 + intval($val->d16);
                    $countd17 = $countd17 + intval($val->d17);
                    $countd18 = $countd18 + intval($val->d18);
                    $countd19 = $countd19 + intval($val->d19);
                    $countd20 = $countd20 + intval($val->d20);
                    $countd21 = $countd21 + intval($val->d21);
                    $countd22 = $countd22 + intval($val->d22);
                    $countd23 = $countd23 + intval($val->d23);
                    $countd24 = $countd24 + intval($val->d24);
                    $countd25 = $countd25 + intval($val->d25);
                    $countd26 = $countd26 + intval($val->d26);
                    $countd27 = $countd27 + intval($val->d27);
                    $countd28 = $countd28 + intval($val->d28);
                    $countd29 = $countd29 + intval($val->d29);
                    $countd30 = $countd30 + intval($val->d30);
                    $countd31 = $countd31 + intval($val->d31);
                    // $countall = $countall + intval($val->all);
                    $countall = $countall + $all;
                    $countfond = $countfond + intval($val->fond);
                    $countsverx = $countsverx + ($all - ($val->fond));

                    $content .= '<tr>';

                    $content .= '<td style="border: 1px solid black;">';
                    $content .= $val->abcd;
                    $content .= '</td>';
                    $content .= '<td style="border: 1px solid black;">';
                    $content .= $val->shift;
                    $content .= '</td>';
                    // $content .= '<td style="border: 1px solid black;">';  $content .= substr($val->d01, 0, -1);   $content .= '</td>';
                    $content .= (substr($val->d01, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d01, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d02, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d02, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d03, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d03, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d04, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d04, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d05, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d05, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d06, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d06, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d07, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d07, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d08, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d08, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d09, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d09, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d10, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d10, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d11, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d11, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d12, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d12, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d13, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d13, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d14, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d14, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d15, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d15, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d16, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d16, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d17, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d17, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d18, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d18, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d19, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d19, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d20, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d20, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d21, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d21, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d22, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d22, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d23, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d23, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d24, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d24, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d25, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d25, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d26, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d26, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d27, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d27, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d28, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d28, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d29, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d29, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d30, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d30, 0, -1);
                    $content .= '</td>';
                    $content .= (substr($val->d31, -1) == 'N') ? '<td style="border: 1px solid black; background-color: #DCDCDC">' : '<td style="border: 1px solid black;">';
                    $content .= substr($val->d31, 0, -1);
                    $content .= '</td>';
                    $content .= '<td style="border: 1px solid black;">';
                    $content .= $all;
                    $content .= '</td>';
                    $content .= '<td style="border: 1px solid black;">';
                    $content .= $val->fond;
                    $content .= '</td>';
                    $content .= '<td style="border: 1px solid black;">';
                    $content .= $all - ($val->fond);
                    $content .= '</td>';

                    $content .= '</tr>';
                }


                $content .= '<tr>';
                $content .= '<td style="border: 1px solid black;" colspan="2">';
                $content .= 'Jami:';
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd01 == 0 ? '' : $countd01;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd02 == 0 ? '' : $countd02;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd03 == 0 ? '' : $countd03;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd04 == 0 ? '' : $countd04;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd05 == 0 ? '' : $countd05;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd06 == 0 ? '' : $countd06;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd07 == 0 ? '' : $countd07;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd08 == 0 ? '' : $countd08;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd09 == 0 ? '' : $countd09;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd10 == 0 ? '' : $countd10;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd11 == 0 ? '' : $countd11;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd12 == 0 ? '' : $countd12;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd13 == 0 ? '' : $countd13;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd14 == 0 ? '' : $countd14;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd15 == 0 ? '' : $countd15;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd16 == 0 ? '' : $countd16;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd17 == 0 ? '' : $countd17;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd18 == 0 ? '' : $countd18;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd19 == 0 ? '' : $countd19;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd20 == 0 ? '' : $countd20;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd21 == 0 ? '' : $countd21;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd22 == 0 ? '' : $countd22;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd23 == 0 ? '' : $countd23;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd24 == 0 ? '' : $countd24;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd25 == 0 ? '' : $countd25;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd26 == 0 ? '' : $countd26;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd27 == 0 ? '' : $countd27;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd28 == 0 ? '' : $countd28;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd29 == 0 ? '' : $countd29;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd30 == 0 ? '' : $countd30;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countd31 == 0 ? '' : $countd31;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countall;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countfond;
                $content .= '</td>';
                $content .= '<td style="border: 1px solid black;">';
                $content .= $countsverx;
                $content .= '</td>';
                $content .= '</tr>';
                // dd($count);
            }
        } // katta foreach

        $content .= '</table>';
        return $content;
    }
}
