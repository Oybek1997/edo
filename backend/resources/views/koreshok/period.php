<?
$tn = $data['tn'];
$from_year = $data['from_year'];
$from_month = $data['from_month'];
$to_year = $data['to_year'];
$to_month = $data['to_month'];

function monthDot($yyyymm){
    return substr($yyyymm, 0,4) . "." . substr($yyyymm, 4,2);
}



$from = $from_year.$from_month;
$to = $to_year.$to_month;
$api = 'http://web.gm.uz/empc/slipperiodportal/'.$tn.'/'.$from.'/'.$to;

$opts = [
        "http" => [
                        "method" => "GET",
                        "header" => "Accept: application/json\r\n" .
                                        "slip_token: ZmYwFWzbf9\r\n"
        ]
];

$context = stream_context_create($opts);

$response = json_decode(file_get_contents($api, true, stream_context_create($opts)));
if($response->success == '1'){


        $arrResponse = get_object_vars($response->data);
        foreach ($arrResponse as $key => $value){
                if(isset($value->error)) continue;
                $months[] = $key;
        }
        if(count($months) != 0){
                if(count($months) <= 12){
                // require './download-buttons.php';
                ?>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <div id="print_area">
                        <div class="main">
                        <h4 style="text-align: center; font-size:18px !important;">Расчетный корешок за период <?=monthDot($from)?> - <?=monthDot($to)?></h4>

                        <table class="tb-main" style="border-collapse: collapse; width: 100%;">
                            <tr>
                                    <td style="width: 30%">Табельный номер: <b><?=$arrResponse[$from]->header->tn->value?></b></td>
                                    <td style="width: 70%;text-align: right;">ИНН: <b><?=(!empty(trim($arrResponse[$from]->header->inn->value))) ? $arrResponse[$from]->header->inn->value : '-'?></b></td>
                            </tr>
                            <tr>
                                    <td style="width: 30%">Ф.И.О: <b><?=$arrResponse[$from]->header->fio->value?></b></td>
                                    <td style="width: 70%;text-align: right;">Подразделение: <b><?=$arrResponse[$from]->header->department->value?></b></td>
                            </tr>
                        </table>



<!--                        <div class="div_header">
                            <div class="left_side">
                                Табельный номер: <b><?//=$arrResponse[$from]->header->tn->value?></b>
                                <br>
                                Ф.И.О: <b><?//=$arrResponse[$from]->header->fio->value?></b>
                            </div>
                            <div class="right_side">
                                ИНН: <b><?//=(!empty(trim($arrResponse[$from]->header->inn->value))) ? $arrResponse[$from]->header->inn->value : '-'?></b>
                                <br>
                                Подразделение: <b><?//=$arrResponse[$from]->header->department->value?></b>
                            </div>
                            <div class="clear"></div>
                        </div>-->



                        <?
                        echo "<table class='salary_table'  style='border-collapse: collapse; width: 100%;'>";

                        echo "<tr class = 'tr_header'>";
                                echo "<td rowspan='2'>Наименование</td>";
                                echo "<td colspan='".count($months)."'>Месяцы</td>";
                        echo "</tr>";

                        echo "<tr class = 'tr_header'>";
                                foreach($months as $m) {
                                        echo "<td>" . monthDot($m) . "</td>";
                                }
                        echo "</tr>";

                        foreach ($arrResponse[$from] as $section => $sectionData){
                                //if($section == 'header') continue;
                                foreach ($sectionData as $item => $itemData) {
                                        if($section == 'header' and in_array($item,['title','tn','fio','department','inn'])) continue;
                                        $class = "";
                                        if($item == 'title'){
                                                $class = " class = 'tr_header2' ";
                                        }
                                        if($item == 'total' or $item == 'total_salary'){
                                                $class = " class = 'tr_total' ";
                                        }
                                        echo "<tr ".$class.">";
                                        // $colspan = ($item == 'title')?"colspan='".(count($months)+1)."'":"";
                                        echo "<td>" . $itemData->label . "</td>";
                                        if($item == 'title'){
                                                foreach($months as $m) {
                                                        echo "<td>" . monthDot($m) . "</td>";
                                                }
                                        }
                                        if($item != 'title'){
                                                foreach ($months as $m) {
                                                        if(isset($arrResponse[$m]->error)) continue;
                                                        $extra_value = '';
                                                        if(in_array($item,['worked_hours', 'night_hours','overtimn_hours','weekend_holiday_hours','pref_leave_hours','leadership_fee','experience_payment','regional_coefficient','additional_coefficient','job_specification_fee'])) {
                                                                $extra_value = (!empty($arrResponse[$m]->{$section}->{$item}->value2))?'<br><span class="extra">( '.$arrResponse[$m]->{$section}->{$item}->value2.' )</span>':'';
                                                        }
                                                        echo "<td class='int_cell'>" . $arrResponse[$m]->{$section}->{$item}->value . $extra_value . "</td>";
                                                }
                                        }
                                        echo "</tr>";
                                }
                        }

                        echo "</table>";
                        echo "</div>";
                }else{
                        echo '<p>Период не должен быть больше 12 месяцев</p>';
                }
        }else{
                echo '<p>Нет результатов</p>';
        }
}else{
        switch ($response->prompt) {
                case 'invalid_token':$message = '<p>Недопустимый токен</p>';break;
                case 'missing_params':$message = '<p>Отсутствующие параметры</p>';break;
                case 'no_results':$message = '<p>Нет результатов</p>';break;
        }
        echo $message;
}
echo '</div>';

?>

<style>
td {
    border: 1px solid #cde;
    padding:2px;
}
th {
    background-color: #cde;
    padding:2px;
}
</style>
