<?php
$tn = $data['tn'];
$from_year = $data['from_year'];
$from_month = $data['from_month'];
$api = 'http://web.gm.uz/empc/slipportal/' . $tn . '/' . $from_year . '/' . $from_month;

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Accept: application/json\r\n" .
                        "slip_token: ZmYwFWzbf9\r\n"
    ]
];

$context = stream_context_create($opts);
$response = json_decode(file_get_contents($api, true, stream_context_create($opts)));
if ($response->success == '1') {
    $arrResponse = $response->data; ?>
                <div id="print_area">
                    <div>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                        <p style="text-align: center; font-size:18px !important;">Расчетный корешок за  месяц <?=$from_month.' '.$from_year?></p>
                        <table class="tb-main" style="border-collapse: collapse; width: 100%;">
                        <tr>
                                <td style="width: 50%" colspan="4">
                                    <b><?=$arrResponse->header->fio->value?></b>(<?=$arrResponse->header->tn->value?>), 
                                    <b><?=$arrResponse->header->department->value?></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 50%" colspan="4">
                                    ИНН: <b><?=(!empty(trim($arrResponse->header->inn->value))) ? $arrResponse->header->inn->value : '-'?></b> 
                                    Оклад: <b><?=$arrResponse->header->salary->value?></b> 
                                    Часовая ст:(<?=$arrResponse->header->category->value?>) <b><?=$arrResponse->header->hourly_rate->value?></b> 
                                    Фонд Р.В.: <b><?=$arrResponse->header->fund->value?></b> 
                                    </td>
                                </tr>
                        </table>

                        <table class="tb-data" style="border-collapse: collapse; width: 100%;">
                            <tr>
                                    <th>Вид</th>
                                    <th style="width: 80px">Сумма</th>
                                    <th>Вид</th>
                                    <th style="width: 80px">Сумма</th>
                            </tr>


                            <tr>
                                    <td colspan="2"><b>1. Начислено</b></td>
                                    <td colspan="2"><b>2. Удержано</b></td>
                            </tr>
                            <?php
                                $arr_accrued = [];
    foreach ($arrResponse->accrued as $obj) {
        if($obj->value != "0")
        $arr_accrued[] = $obj;
    }
    $length_accrued = count($arr_accrued);

    $arr_withheld = [];
    foreach ($arrResponse->withheld as $obj) {
        if($obj->value != "0")
        $arr_withheld[] = $obj;
    }
    $length_withheld = count($arr_withheld);

    $length = ($length_accrued > $length_withheld) ? $length_accrued : $length_withheld;

    for ($i = 1; $i < $length - 1; $i++) {
        ?>
                                    <tr>
                                            <td><?=isset($arr_accrued[$i]) && $length_accrued-1 != $i ? $arr_accrued[$i]->label : ''?></td>
                                            <td style="padding-right: 5px;text-align:right;"><?=isset($arr_accrued[$i]) && $length_accrued-1 != $i ? $arr_accrued[$i]->value : ''?></td>
                                            <td><?=isset($arr_withheld[$i]) && $length_withheld-1 != $i ? $arr_withheld[$i]->label : ''?></td>
                                            <td style="padding-right: 5px;text-align:right;"><?=isset($arr_withheld[$i]) && $length_withheld-1 != $i ? $arr_withheld[$i]->value : ''?></td>
                                    </tr>
                                <?php
    } ?>
                            <tr>
                                <th style="text-align: left;padding-left: 5px"><?=$arr_accrued[$length_accrued-1]->label?></th>
                                <th style="text-align: right;padding-right: 5px"><?=$arr_accrued[$length_accrued-1]->value?></th>
                                <th style="text-align: left;padding-left: 5px"><?=$arr_withheld[$length_withheld-1]->label?></th>
                                <th style="text-align: right;padding-right: 5px"><?=$arr_withheld[$length_withheld-1]->value?></th>
                            </tr>
                            <tr>
                                    <td colspan="2"><b>3. Выплачено</b></td>
                                    <td colspan="2"><b>4. К выдаче</b></td>
                            </tr>
                            <?php
                                $arr_to_issue = [];
    foreach ($arrResponse->to_issue as $obj) {
        if($obj->value != "0")
        $arr_to_issue[] = $obj;
    }
    $length_to_issue = count($arr_to_issue);

    $arr_paid = [];
    foreach ($arrResponse->paid as $obj) {
        if($obj->value != "0")
        $arr_paid[] = $obj;
    }
    $length_paid = count($arr_paid);

    $length = ($length_to_issue > $length_paid) ? $length_to_issue : $length_paid;

    for ($i = 1; $i < $length - 1; $i++) {
        ?>
                                    <tr>
                                            <td><?=isset($arr_paid[$i]) ? $arr_paid[$i]->label : ''?></td>
                                            <td style="padding-right: 5px;text-align:right;"><?=isset($arr_paid[$i]) && $length_paid-1 != $i ? $arr_paid[$i]->value : ''?></td>
                                            <td><?=isset($arr_to_issue[$i]) && $length_to_issue-1 != $i ? $arr_to_issue[$i]->label : ''?></td>
                                            <td style="padding-right: 5px;text-align:right;"><?=isset($arr_to_issue[$i]) && $length_to_issue-1 != $i ? $arr_to_issue[$i]->value : ''?></td>
                                    </tr>
                                <?php
    } ?>

                            <tr>
                                <th style="text-align: left;padding-left: 5px"><?=$arr_paid[$length_paid-1]->label ? $arr_paid[$length_paid-1]->label : ''?></th>
                                <th style="text-align: right;padding-right: 5px"><?=$arr_paid[$length_paid-1]->value ? $arr_paid[$length_paid-1]->value : ''?></th>
                                <th style="text-align: left;padding-left: 5px"><?=$arr_to_issue[$length_to_issue-1]->label ? $arr_to_issue[$length_to_issue-1]->label : ''?></th>
                                <th style="text-align: right;padding-right: 5px"><?=$arr_to_issue[$length_to_issue-1]->value ? $arr_to_issue[$length_to_issue-1]->value : ''?></th>
                            </tr>

                            <tr>
                                <td style="text-align: left;padding-left: 5px;"><?=$arrResponse->debt->debt_begin_month->label?></td>
                                <td style="text-align: right;padding-right: 5px"><?=$arrResponse->debt->debt_begin_month->value?></td>
                                <td style="text-align: left;padding-left: 5px;"><?=$arrResponse->debt->debt_end_month->label?></td>
                                <td style="text-align: right;padding-right: 5px"><?=$arrResponse->debt->debt_end_month->value?></td>
                            </tr>

                            <tr>
                                <th style="text-align: left;padding-left: 5px; "><b><?=$arrResponse->total_salary->total_salary->label?></b></th>
                                <th style="text-align: right;padding-right: 5px; "><b><?=$arrResponse->total_salary->total_salary->value?></b></th>
                                <th colspan="2" style="text-align: left;padding-left: 5px"></th>
                            </tr>


                        </table>

                </div>

<?php
} else {
        switch ($response->prompt) {
                case 'invalid_token':$message = '<p>Недопустимый токен</p>';break;
                case 'missing_params':$message = '<p>Отсутствующие параметры</p>';break;
                case 'no_results':$message = '<p>Нет результатов</p>';break;
                case 'month_not_opened':$message = '<p>Расчет зарплаты за '.monthName($from_month).' месяц '.$from_year.' года еще не производился.</p>';break;
        }
        echo $message;
    }



?>
</div>
<style>
td {
    border: 1px solid #cde;
    padding:2px;
}
th {
    background-color: #cde;
}
</style>
