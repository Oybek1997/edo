<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Centrum;
use App\Http\Models\Document;
use App\Http\Models\DocumentTemplate;
use App\Http\Models\DocumentDetail;
use App\Http\Models\DocumentDetailAttribute;
use App\Http\Models\DocumentDetailAttributeValue;
use App\Http\Models\DocumentDetailContent;
use App\Http\Models\DocumentSigner;
use App\Http\Models\File;
use App\Http\Models\DocumentSignerEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Hidehalo\Nanoid\Client;

class CentrumController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $uploadedFile = $request->file('file');
            $part = microtime(true);
            $original_name = $request->file->getClientOriginalName();
            $fileName = microtime(true) . '.' . $request->file->extension();
            $newFileName = time() . rand();
            $request->file->move(public_path('centrum_tmp'), $fileName);
            $reader = new Xlsx();
            $spreadsheet = $reader->load(public_path('centrum_tmp') . '/' . $fileName);
            $data = $spreadsheet->getSheet(0)->toArray();
            // return 123;
            $document_template_id = $data[0][0];

            $contract_num = $data[0][9];
            $act_date = $data[0][7];
            // return $document_template_id;
            $document_template_name = $data[0][7];
            $act_number = $data[0][3];
            $data0 = $data[0];
            $data1 = $data[1];
            $data2 = $data[2];
            $data3 = $data[3];
            $sum = [];
            foreach ($data2 as $key => $value) {
                $v = $data3[$key];
                if ($v != null && $key != 0) {
                    $sum[] = [$value, $v];
                }
            }
            unset($data[0]);
            unset($data[1]);
            unset($data[2]);
            unset($data[3]);
            $colCount = array_search(null, $data0);
            if ($document_template_id == 507) {
                // return $data;
            }
            $checkingTemplates = [
                488 => [[3, 1643], [5, 1645]], 489 => [[1, 1664], [26, 1689]], 490 => [[2, 1691], [29, 1717]], 491 => [[4, 1721], [6, 1723], [10, 1725], [15, 1729]], 492 => [[5, 1742],[6, 1743],[8, 1744]], 493 => [[6, 1762],[9, 1764],[11, 1766]], 494 => [[5, 1779,], [10, 1786]], 496 => [[9, 1818]], 497 => [[3, 1832], [6, 1835]], 498 => [[3, 1847]], 500 => [[1, 1865]], 506 => [[5, 1902], [13, 1910]], 507 => [[5, 1941], [6, 1942], [10, 1946]], 510 => [[4, 1979], [5, 1980],[7, 1982]], 511 => [[2, 1987],[3, 1988], [4, 1989]], 512 => [[3, 1998],[4, 1999], [5, 2000]], 513 => [[1, 2010],[2, 2011],[3, 2012]], 514 => [[3, 2031],[4, 2032],[6, 2034]], 521 => [[6, 2096], [11, 2109]], 527 => [[5, 2136]], 530 => [[5, 2166]], 532 => [[6, 2199], [12, 2205]],  545 => [[1, 2283]], 595 => [[6, 2578],[7, 2579],[8, 2580]]];
            $res = [];
            // dd($data);
            if (isset($checkingTemplates[$document_template_id]) && $checkingTemplates[$document_template_id]) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $query = "SELECT d.id id from documents d
                            inner join document_templates dt on dt.id = d.document_template_id
                            inner join document_details dd on dd.document_id=d.id
                            where d.status not in (0,6) and
                        ";
                        foreach ($checkingTemplates[$document_template_id] as $key2 => $row) {
                            $attr_value = $value[$row[0]];
                            $attr_id = $row[1];
                            // $query = DB::select("SELECT dd.document_id doc_id FROM 
                            // document_detail_contents ddc
                            // INNER JOIN document_details dd on dd.id = ddc.document_detail_id
                            // INNER JOIN document_detail_attributes dda on dda.id = ddc.d_d_attribute_id
                            // INNER join document_detail_templates ddt on ddt.id = dda.document_detail_template_id
                            // inner join document_templates dt on dt.id = ddt.document_template_id
                            // where dt.id = '$document_template_id' and dda.id='$att_id' and ddc.value like '%' '$att_value' '%'
                            // limit 1
                            // ");
                            if ($key2 != 0) {
                                $query .= " and ";
                            }
                            if ($attr_value == null) {
                                $query .= " exists (SELECT ddc.id FROM document_detail_contents ddc WHERE ddc.document_detail_id = dd.id and ddc.d_d_attribute_id  = '" . $attr_id . "' and ddc.value is null)";
                            } else {
                                $query .= " exists (SELECT ddc.id FROM document_detail_contents ddc WHERE ddc.document_detail_id = dd.id and ddc.d_d_attribute_id  = '" . $attr_id . "' and ddc.value = '" . $attr_value . "')";
                            }
                        }
                        // dd($query);
                        $res = DB::select($query);
                        if (count($res)) {
                            $doc = Document::find($res[0]->id);
                            $employee = $doc->employee->getShortname($doc->locale);
                            $document_detail = DocumentDetailAttribute::where('id', $attr_id)->first();
                            if($doc->status==0){
                                $status = ' Qoralama ';
                            }
                            if($doc->status==1||$doc->status==2){
                                $status = ' Jarayonda ';
                            }
                            if($doc->status==3||$doc->status==4||$doc->status==5){
                                $status = ' Imzolangan ';
                            }
                        // return [$res[0]->id, $employee, $status, $document_detail->attribute_name_uz_latin, $attr_value];
                            return response()->json(["message" => "Aktda duplikat aniqlandi. Hujjat raqami: ", "doc_id" => $res[0]->id, "employee" => $employee, "status" => $status, "attr" => $document_detail->attribute_name_uz_latin, "attr_value" => $attr_value], 200);
                        }
                    }
                }
            }
            
            $result = $this->createDocument($data, $colCount, $document_template_id, $fileName, $newFileName, $original_name, $document_template_name, $act_number, $sum, $contract_num, $act_date);
            return $result;
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(["message" => "Notog'ri shablon yuklandi.", "id" => null, "pdf_file_name" => null], 200);
        }
    }
    public function checkAct($id, $document_template_id, $document_details)
    {
        // return "Bu CentrumControllerdan kelyapti";
        $checkingTemplates = [
            488 => [[3, 1643], [5, 1645]], 489 => [[1, 1664], [26, 1689]], 490 => [[2, 1691], [29, 1717]], 491 => [[4, 1721], [6, 1723], [10, 1725], [15, 1729]], 492 => [[5, 1742],[6, 1743],[8, 1744]], 493 => [[6, 1762],[9, 1764],[11, 1766]], 494 => [[5, 1779,], [10, 1786]], 496 => [[9, 1818]], 497 => [[3, 1832], [6, 1835]], 498 => [[3, 1847]], 500 => [[1, 1865]], 506 => [[5, 1902], [13, 1910]], 507 => [[5, 1941], [6, 1942], [10, 1946]], 510 => [[4, 1979], [5, 1980],[7, 1982]], 511 => [[2, 1987],[3, 1988], [4, 1989]], 512 => [[3, 1998],[4, 1999], [5, 2000]], 513 => [[1, 2010],[2, 2011],[3, 2012]], 514 => [[3, 2031],[4, 2032],[6, 2034]], 521 => [[6, 2096], [11, 2109]], 527 => [[5, 2136]], 530 => [[5, 2166]], 532 => [[6, 2199], [12, 2205]],  545 => [[1, 2283]], 595 => [[6, 2578],[7, 2579],[8, 2580]]];
//             543 => [[5, 2251]],
// 543 => [[5, 2251]], 
        // return $checkingTemplates[$document_template_id];
        if(isset($checkingTemplates[$document_template_id])){
            // return 'sasa';
            foreach ($document_details as $key => $doc_detail) {
                $query = "SELECT d.id id from documents d
                                inner join document_templates dt on dt.id = d.document_template_id
                                inner join document_details dd on dd.document_id=d.id
                                where d.status not in (0,6) and d.id != " . $id . "
                        ";
                foreach ($doc_detail['document_detail_attribute_values'] as $key2 => $doc) {
    
                    $abc = [];
                    // dd([$document_template_id]);
                    foreach ($checkingTemplates[$document_template_id] as $key3 => $row) {
                        if ($doc['d_d_attribute_id'] == $row[1]) {
                            $attr_value = $doc['attribute_value'];
                            $attr_id = $row[1];
                            // if ($attr_value == null) {
                            //     $query .= " and exists (SELECT ddc.id FROM document_detail_contents ddc WHERE ddc.document_detail_id = dd.id and ddc.d_d_attribute_id  = '" . $attr_id . "' and ddc.value is null)";
                            // } else {
                            // }
                            $query .= " and exists (SELECT * FROM document_detail_contents ddc WHERE ddc.document_detail_id = dd.id and ddc.d_d_attribute_id  = '" . $attr_id . "' and ddc.value = '" . $attr_value . "')";
                        }
                    }
                }
                $res = DB::select($query);
                if (count($res)) {
                    // if($id ==2449149){
                        $doc = Document::find($res[0]->id);
                        $employee = $doc->employee->getShortname($doc->locale);
                        $document_detail = DocumentDetailAttribute::where('id', $attr_id)->first();
                        if($doc->status==0){
                            $status = ' Qoralama ';
                        }
                        if($doc->status==1||$doc->status==2){
                            $status = ' Jarayonda ';
                        }
                        if($doc->status==3||$doc->status==4||$doc->status==5){
                            $status = ' Imzolangan ';
                        }
                        // dd([$res[0]->id, $employee, $status, $document_detail->attribute_name_uz_latin, $attr_value] );
                    // }
                    // return $res[0]->id;
                    return [$res[0]->id, $employee, $status, $document_detail->attribute_name_uz_latin, $attr_value];
                }
            }
        }
        return 1;
    }
    public function createDocument($data, $colCount, $document_template_id, $fileName, $newFileName, $original_name, $document_template_name, $act_number, $sum, $contract_num, $act_date)
    {
        // return [$contract_num, $act_date];
        $locale = 'uz_latin';
        DB::beginTransaction();
        try {
            $document_template = DocumentTemplate::find($document_template_id);
            $model = new Document();
            $model->document_template_id = $document_template_id;
            $model->created_employee_id = Auth::user()->employee_id;
            $model->department_id = $document_template->department_id;
            $model->document_type_id = $document_template->document_type_id;
            $model->locale = $locale;
            // $model->title = $document_template_name;
            $model->title = $act_number;
            $model->responsible_contact = $act_number;
            $model->document_date = date('Y-m-d H:i:s');
            $model->pdf_file_name = $this->generateNanoId();
            $model->save();
            $leng = $model->locale == 'uz_latin' ? 'uz_latin' : 'uz_cyril';
            $firstLetter = $leng == 'uz_latin' ? 1 : 2;
            $content = '';
            if($document_template_id==488){
                // return [$contract_num, $act_date];
                $content .= '<div style = "text-align: center; font-weight: bold; font-size: 16pt" >';
                $content .= $act_number;
                $content .= '</div>';
                $content .= '<div style = "text-align: center; font-weight: bold; font-size: 16pt" >согласно Контракту ';
                $content .= $contract_num;
    
                $content .= ' . между СП ИИ ООО "UZLOGISTIC" и АО "UzAuto Motors"</div>';
                $content .= '<div style = "text-align: center; font-weight: bold; font-size: 16pt" >Дата подписания акта ';
                $content .= $act_date;
                $content .= 'г.</div>';
            }
            $content .= '<table style="border:1px solid black; border-collapse:collapse;" border="1">';
            $content .= '<tr>';
            $content .= '<th style="text-align:center;" colspan="2">';
            $content .= $model->locale == 'uz_latin' ? 'Summa' : 'Сумма';
            $content .= '</th>';
            $content .= '</tr>';
            foreach ($sum as $key => $value) {
                $content .= '<tr>';
                $content .= '<td style="width:50%;padding:2px; text-align:right;">';
                $content .= $value[0];
                $content .= '</td>';
                $content .= '<td style="width:50%;padding:2px;">';
                $content .= $value[1];
                $content .= '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';

            if ($document_template_id == 488) {
                foreach ($data as $key => $value) {
                    // $invoices = explode(' ', $value[8]);
                    // foreach ($invoices as $a => $b) {
                    //     # code...
                    // }
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1640) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1641) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1643) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1644) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1645) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1646) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1885) {
                                $sequence = 7;
                                $currency = $value[7] ? $value[7] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            } elseif ($v->id == 2122) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2123) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1647) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1648) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1649) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1650) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1651) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1652) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1653) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1654) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1655) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1657) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1658) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1659) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1660) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1661) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1662) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1663) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1885) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 489) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            if ($v->id == 1664) {
                                $document_detail_attribute_value->attribute_value = $value[1] ? $value[1] : null;
                            } elseif ($v->id == 1665) {
                                $document_detail_attribute_value->attribute_value = $value[2] ? $value[2] : null;
                            } elseif ($v->id == 1666) {
                                $document_detail_attribute_value->attribute_value = $value[3] ? $value[3] : null;
                            } elseif ($v->id == 1667) {
                                $document_detail_attribute_value->attribute_value = $value[4] ? $value[4] : null;
                            } elseif ($v->id == 1668) {
                                $document_detail_attribute_value->attribute_value = $value[5] ? $value[5] : null;
                            } elseif ($v->id == 1669) {
                                $document_detail_attribute_value->attribute_value = $value[6] ? $value[6] : null;
                            } elseif ($v->id == 1670) {
                                $document_detail_attribute_value->attribute_value = $value[7] ? $value[7] : null;
                            } elseif ($v->id == 1671) {
                                $document_detail_attribute_value->attribute_value = $value[8] ? $value[8] : null;
                            } elseif ($v->id == 1672) {
                                $document_detail_attribute_value->attribute_value = $value[9] ? $value[9] : null;
                            } elseif ($v->id == 1673) {
                                $document_detail_attribute_value->attribute_value = $value[10] ? $value[10] : null;
                            } elseif ($v->id == 1674) {
                                $document_detail_attribute_value->attribute_value = $value[11] ? $value[11] : null;
                            } elseif ($v->id == 1675) {
                                $document_detail_attribute_value->attribute_value = $value[12] ? $value[12] : null;
                            } elseif ($v->id == 1676) {
                                $document_detail_attribute_value->attribute_value = $value[13] ? $value[13] : null;
                            } elseif ($v->id == 1677) {
                                $document_detail_attribute_value->attribute_value = $value[14] ? $value[14] : null;
                            } elseif ($v->id == 1678) {
                                $document_detail_attribute_value->attribute_value = $value[15] ? $value[15] : null;
                            } elseif ($v->id == 1679) {
                                $document_detail_attribute_value->attribute_value = $value[16] ? $value[16] : null;
                            } elseif ($v->id == 1680) {
                                $document_detail_attribute_value->attribute_value = $value[17] ? $value[17] : null;
                            } elseif ($v->id == 1681) {
                                $document_detail_attribute_value->attribute_value = $value[18] ? $value[18] : null;
                            } elseif ($v->id == 1682) {
                                $document_detail_attribute_value->attribute_value = $value[19] ? $value[19] : null;
                            } elseif ($v->id == 1683) {
                                $document_detail_attribute_value->attribute_value = $value[20] ? $value[20] : null;
                            } elseif ($v->id == 1684) {
                                $document_detail_attribute_value->attribute_value = $value[21] ? $value[21] : null;
                            } elseif ($v->id == 1685) {
                                $document_detail_attribute_value->attribute_value = $value[22] ? $value[22] : null;
                            } elseif ($v->id == 1686) {
                                $document_detail_attribute_value->attribute_value = $value[23] ? $value[23] : null;
                            } elseif ($v->id == 1687) {
                                $document_detail_attribute_value->attribute_value = $value[24] ? $value[24] : null;
                            } elseif ($v->id == 1688) {
                                $document_detail_attribute_value->attribute_value = $value[25] ? $value[25] : null;
                            } elseif ($v->id == 1689) {
                                $document_detail_attribute_value->attribute_value = $value[26] ? $value[26] : null;
                            } // 489 shablonda valyuta kerakmas

                            $document_detail_attribute_value->save();
                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 490) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        $sequence = 1;
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            if ($v->id == 1690) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1691) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1692) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1693) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1694) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1695) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1696) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1697) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1698) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1699) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1700) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1701) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1702) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1703) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1704) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1705) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1706) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1707) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1708) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1709) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1985) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1710) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1711) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1712) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1713) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1714) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1715) {
                                $sequence = 28;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1716) {
                                $sequence = 29;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1717) {
                                $sequence = 30;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1887) {
                                $sequence = 19;
                                $currency = $value[19] ? $value[19] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1887) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 491) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1718) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1719) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1720) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2439) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1721) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1722) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1723) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1960) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1961) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1724) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1725) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1726) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1935) {
                                $sequence = 13;
                                $currency = $value[$sequence] ? $value[$sequence] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            } elseif ($v->id == 1727) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1728) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1729) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1730) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1731) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1732) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1733) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1734) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2440) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1735) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1736) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1737) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1888) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1934) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1935) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 492) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        $sequence = 1;
                        $currency = '';
                        $currency1 = '';
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            if ($v->id == 1738) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[1] ? $value[1] : null;
                            } elseif ($v->id == 1739) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[2] ? $value[2] : null;
                            } elseif ($v->id == 1740) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[3] ? $value[3] : null;
                            } elseif ($v->id == 1741) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[4] ? $value[4] : null;
                            } elseif ($v->id == 1742) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[5] ? $value[5] : null;
                            } elseif ($v->id == 1743) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[6] ? $value[6] : null;
                            } elseif ($v->id == 1889) {
                                $sequence = 7;
                                $currency = $value[7] ? $value[7] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            } elseif ($v->id == 1744) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[8] ? $value[8] : null;
                            } elseif ($v->id == 1745) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[9] ? $value[9] : null;
                            } elseif ($v->id == 1746) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[10] ? $value[10] : null;
                            } elseif ($v->id == 1747) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[11] ? $value[11] : null;
                            } elseif ($v->id == 1748) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[12] ? $value[12] : null;
                            } elseif ($v->id == 1749) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[13] ? $value[13] : null;
                            } elseif ($v->id == 1750) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[14] ? $value[14] : null;
                            } elseif ($v->id == 1751) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[15] ? $value[15] : null;
                            } elseif ($v->id == 2019) {
                                $sequence = 16;
                                $currency1 = $value[16] ? $value[16] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency1);
                            } elseif ($v->id == 1752) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[17] ? $value[17] : null;
                            } elseif ($v->id == 1753) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[18] ? $value[18] : null;
                            } elseif ($v->id == 1754) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[19] ? $value[19] : null;
                            } elseif ($v->id == 1755) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[20] ? $value[20] : null;
                            } elseif ($v->id == 1756) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[21] ? $value[21] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1889) {
                                $documentDetailContent->value = $currency;
                            } elseif ($v->id == 2019) {
                                $documentDetailContent->value = $currency1;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 493) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        $sequence = 1;
                        $currency = '';
                        $currency1 = '';
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            if ($v->id == 1757) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[1] ? $value[1] : null;
                            } elseif ($v->id == 1758) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[2] ? $value[2] : null;
                            } elseif ($v->id == 1759) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[3] ? $value[3] : null;
                            } elseif ($v->id == 1760) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[4] ? $value[4] : null;
                            } elseif ($v->id == 1761) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[5] ? $value[5] : null;
                            } elseif ($v->id == 1762) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[6] ? $value[6] : null;
                            } elseif ($v->id == 1763) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[7] ? $value[7] : null;
                            } elseif ($v->id == 1890) {
                                $sequence = 8;
                                $currency = $value[8] ? $value[8] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            } elseif ($v->id == 1764) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[9] ? $value[9] : null;
                            } elseif ($v->id == 1765) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[10] ? $value[10] : null;
                            } elseif ($v->id == 1766) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[11] ? $value[11] : null;
                            } elseif ($v->id == 1767) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[12] ? $value[12] : null;
                            } elseif ($v->id == 1768) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[13] ? $value[13] : null;
                            } elseif ($v->id == 1769) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[14] ? $value[14] : null;
                            } elseif ($v->id == 1770) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[15] ? $value[15] : null;
                            } elseif ($v->id == 1771) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[16] ? $value[16] : null;
                            } elseif ($v->id == 2020) {
                                $sequence = 17;
                                $currency1 = $value[17] ? $value[17] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency1);
                            } elseif ($v->id == 1772) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[18] ? $value[18] : null;
                            } elseif ($v->id == 1773) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[19] ? $value[19] : null;
                            } elseif ($v->id == 1774) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[20] ? $value[20] : null;
                            } elseif ($v->id == 1775) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[21] ? $value[21] : null;
                            } elseif ($v->id == 1776) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[22] ? $value[22] : null;
                            } elseif ($v->id == 1777) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[23] ? $value[23] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1890) {
                                $documentDetailContent->value = $currency;
                            } elseif ($v->id == 2020) {
                                $documentDetailContent->value = $currency1;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 494) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1778) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1781) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1782) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1783) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1779) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1780) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2124) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1784) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1785) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1786) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1787) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1788) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1789) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1790) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1791) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1792) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1793) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1794) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1795) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2126) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2127) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2129) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2128) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1797) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1798) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1799) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2622) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1800) {
                                $sequence = 28;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1801) {
                                $sequence = 29;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 495) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            if ($v->id == 1802) {
                                $document_detail_attribute_value->attribute_value = $value[1] ? $value[1] : null;
                            } elseif ($v->id == 1803) {
                                $document_detail_attribute_value->attribute_value = $value[2] ? $value[2] : null;
                            } elseif ($v->id == 1804) {
                                $document_detail_attribute_value->attribute_value = $value[3] ? $value[3] : null;
                            } elseif ($v->id == 1805) {
                                $document_detail_attribute_value->attribute_value = $value[4] ? $value[4] : null;
                            } elseif ($v->id == 1806) {
                                $document_detail_attribute_value->attribute_value = $value[5] ? $value[5] : null;
                            } elseif ($v->id == 1807) {
                                $document_detail_attribute_value->attribute_value = $value[6] ? $value[6] : null;
                            } elseif ($v->id == 1808) {
                                $document_detail_attribute_value->attribute_value = $value[7] ? $value[7] : null;
                            } elseif ($v->id == 1809) {
                                $document_detail_attribute_value->attribute_value = $value[8] ? $value[8] : null;
                            } elseif ($v->id == 1810) {
                                $document_detail_attribute_value->attribute_value = $value[9] ? $value[9] : null;
                            } elseif ($v->id == 1893) {
                                $currency = $value[10] ? $value[10] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1893) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 496) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1811) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1812) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1813) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1814) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1815) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1816) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1817) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1818) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1819) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1820) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1821) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1822) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1823) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1824) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1825) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1826) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1827) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1828) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1829) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1892) {
                                $sequence = 5;
                                $currency = $value[$sequence] ? $value[$sequence] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1892) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 497) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1830) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1831) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1832) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1833) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1834) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1835) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1836) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1837) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1838) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1839) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1840) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1841) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1842) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1843) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1844) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1894) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2188) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2189) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2190) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 498) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            if ($v->id == 1845) {
                                $document_detail_attribute_value->attribute_value = $value[1] ? $value[1] : null;
                            } elseif ($v->id == 1846) {
                                $document_detail_attribute_value->attribute_value = $value[2] ? $value[2] : null;
                            } elseif ($v->id == 1847) {
                                $document_detail_attribute_value->attribute_value = $value[3] ? $value[3] : null;
                            } elseif ($v->id == 1848) {
                                $document_detail_attribute_value->attribute_value = $value[4] ? $value[4] : null;
                            } elseif ($v->id == 1849) {
                                $document_detail_attribute_value->attribute_value = $value[5] ? $value[5] : null;
                            } elseif ($v->id == 1850) {
                                $document_detail_attribute_value->attribute_value = $value[6] ? $value[6] : null;
                            } elseif ($v->id == 1851) {
                                $document_detail_attribute_value->attribute_value = $value[7] ? $value[7] : null;
                            } elseif ($v->id == 1852) {
                                $document_detail_attribute_value->attribute_value = $value[8] ? $value[8] : null;
                            } elseif ($v->id == 1853) {
                                $document_detail_attribute_value->attribute_value = $value[9] ? $value[9] : null;
                            } elseif ($v->id == 1854) {
                                $document_detail_attribute_value->attribute_value = $value[10] ? $value[10] : null;
                            } elseif ($v->id == 1855) {
                                $document_detail_attribute_value->attribute_value = $value[11] ? $value[11] : null;
                            } elseif ($v->id == 1856) {
                                $document_detail_attribute_value->attribute_value = $value[12] ? $value[12] : null;
                            } elseif ($v->id == 1895) {
                                $currency = $value[13] ? $value[13] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = 1;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1895) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 499) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1857) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1858) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1859) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1860) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1861) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1862) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1863) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1864) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 500) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1865) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1866) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1867) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1868) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1869) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1870) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1871) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1872) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1873) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1874) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            //     $currency = $value[20] ? $value[20] : null;
                            //     $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);

                            // if ($v->id == 1897) {
                            //     $documentDetailContent->value = $currency;
                            // } else {
                            //     $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            // }

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 506) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1898) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1899) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1900) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1901) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1902) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1903) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1904) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1905) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1906) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1907) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1908) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1909) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1910) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1911) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1933) {
                                $currency = $value[15] ? $value[15] : null;
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            } elseif ($v->id == 1912) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1913) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1914) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1915) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1916) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1917) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1918) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1919) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1920) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1921) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1922) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1927) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1928) {
                                $sequence = 28;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1929) {
                                $sequence = 29;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1930) {
                                $sequence = 30;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1931) {
                                $sequence = 31;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1932) {
                                $sequence = 32;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1933) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            // $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 507) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1937) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1938) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1939) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1940) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1941) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1942) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1943) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            // elseif ($v->id == 1885) {
                            //     $currency = $value[8] ? $value[8] : null;
                            //     $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            //     $sequence = 8;
                            // } 
                            elseif ($v->id == 1944) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1945) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1946) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1947) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1948) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1949) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1950) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1951) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1952) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1953) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1954) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1955) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1956) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1957) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1958) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1959) {
                                $sequence = 22;
                                $currency = $value[$sequence] ? $value[$sequence] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 1959) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            // $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 508) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1962) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1963) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1964) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1965) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1966) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1967) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1968) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1969) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1970) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1971) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1972) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1973) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2041) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1974) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1975) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2021) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2023) {
                                $currency = $value[16] ? $value[16] : null;
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2023) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 510) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1976) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1977) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1978) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1979) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1980) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1981) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1982) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2042) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1983) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1984) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2024) {
                                $currency = $value[11] ? $value[11] : null;
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            } elseif ($v->id == 2022) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2024) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 511) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1986) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1987) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1988) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1989) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1990) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1991) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1992) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1993) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1994) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1995) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2025) {
                                $currency = $value[11] ? $value[11] : null;
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2025) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 512) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 1996) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1997) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1998) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 1999) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2000) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2001) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2002) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2003) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2004) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2005) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2006) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2007) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2008) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2009) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2026) {
                                $currency = $value[15] ? $value[15] : null;
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2026) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 513) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2010) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2011) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2012) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2013) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2014) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2015) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2016) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2017) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2018) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2027) {
                                $currency = $value[10] ? $value[10] : null;
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2027) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 514) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2029) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2030) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2031) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2032) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2033) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2034) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2035) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2036) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2037) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2038) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2039) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2040) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2065) {
                                $currency = $value[13] ? $value[13] : null;
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2065) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 515) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2043) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2044) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2045) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2046) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2047) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2048) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2049) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2050) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2051) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2052) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2053) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2054) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2055) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2056) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2057) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2058) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2059) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2060) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2061) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2062) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2063) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2064) {
                                $currency = $value[22] ? $value[22] : null;
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2064) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 521) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2091) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2092) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2093) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2094) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2095) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2096) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2097) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2098) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2099) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2100) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2109) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2101) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2102) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2103) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2104) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2105) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2106) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2107) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2108) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2109) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2110) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2111) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2112) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2113) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2114) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2115) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2116) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2623) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2624) {
                                $sequence = 28;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2117) {
                                $sequence = 29;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2118) {
                                $sequence = 30;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2119) {
                                $sequence = 31;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2625) {
                                $sequence = 32;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2120) {
                                $sequence = 33;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2121) {
                                $sequence = 34;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 527) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2132) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2133) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2134) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2135) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2136) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2137) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2138) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2139) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2140) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2141) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2142) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2143) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2144) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2145) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2146) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2147) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2148) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2149) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2150) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2151) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2152) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2153) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2154) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2155) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2156) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2157) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2158) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2159) {
                                $sequence = 28;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2160) {
                                $sequence = 29;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2161) {
                                $sequence = 30;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 530) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2162) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2163) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2164) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2165) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2166) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2167) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2168) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2169) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2170) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2171) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2172) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2173) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2174) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2175) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2176) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2177) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2178) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2179) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2180) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2181) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2182) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2183) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2184) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2185) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2186) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2187) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 532) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2194) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2195) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2196) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2197) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2198) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2199) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2200) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2201) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2202) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2203) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2204) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2205) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2206) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2207) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2208) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2209) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2210) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2211) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2212) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2213) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2214) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2215) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2216) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2217) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2218) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2219) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2220) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2221) {
                                $sequence = 28;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2626) {
                                $sequence = 29;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2222) {
                                $sequence = 30;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2223) {
                                $sequence = 31;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2224) {
                                $sequence = 32;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2627) {
                                $sequence = 33;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2225) {
                                $sequence = 34;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2226) {
                                $sequence = 35;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 543) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2247) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2248) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2249) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2250) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2251) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2252) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2254) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2255) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2256) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2257) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2258) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2259) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2260) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2261) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2262) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2263) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2264) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2265) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2266) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2267) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2268) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2269) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2270) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2271) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2272) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2273) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2274) {
                                $sequence = 28;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2275) {
                                $sequence = 29;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2276) {
                                $sequence = 30;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2277) {
                                $sequence = 31;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2253) {
                                $currency = $value[7] ? $value[7] : null;
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2253) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 545) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2283) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2284) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2285) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2286) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2287) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2288) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2289) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2290) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2291) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2292) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2293) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2294) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2295) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2296) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2297) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2298) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2299) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2300) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2301) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2302) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2303) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2304) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2305) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2306) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2307) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } 
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 546) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2308) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2309) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2310) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2311) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2312) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2313) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2314) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2315) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2316) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2317) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2318) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2319) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2320) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2321) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2322) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2323) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2324) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2325) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2326) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2327) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2328) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2329) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2330) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2331) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2332) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }elseif ($v->id == 2333) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2334) {
                                $sequence = 27;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2335) {
                                $sequence = 28;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2336) {
                                $sequence = 29;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } 
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 547) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2337) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2338) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2339) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2340) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2341) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2342) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2343) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2344) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2345) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2346) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2347) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2348) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2349) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2350) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2351) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2352) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2353) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2354) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2355) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2356) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2357) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2358) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2359) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2360) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2361) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2362) {
                                $sequence = 26;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } 
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 548) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2363) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2364) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2365) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2366) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2367) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2368) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2369) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2370) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2371) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2372) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2373) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2374) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2375) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2376) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } 
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 583) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2502) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2503) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2504) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2505) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2506) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2507) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2508) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2509) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2510) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2511) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } 
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 588) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2517) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2518) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2519) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2520) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2521) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2522) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2523) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2524) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2525) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2526) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }  elseif ($v->id == 2527) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }  elseif ($v->id == 2528) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }  elseif ($v->id == 2529) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }  elseif ($v->id == 2530) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }  elseif ($v->id == 2531) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }  elseif ($v->id == 2532) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }  elseif ($v->id == 2533) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }  elseif ($v->id == 2534) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } 
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 595) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        $sequence = 1;
                        $currency = '';
                        $currency1 = '';
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            if ($v->id == 2573) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2574) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2575) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2576) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2577) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2578) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2579) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2580) {
                                $sequence = 8;
                                $currency = $value[$sequence] ? $value[$sequence] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            } elseif ($v->id == 2581) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2885) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2582) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2583) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2584) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2585) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2586) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2587) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2588) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2589) {
                                $sequence = 18;
                                $currency1 = $value[$sequence] ? $value[$sequence] : null;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency1);
                            } elseif ($v->id == 2590) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2591) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2592) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2593) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2594) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2580) {
                                $documentDetailContent->value = $currency;
                            } elseif ($v->id == 2589) {
                                $documentDetailContent->value = $currency1;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 609) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2690) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2691) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2692) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2693) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2694) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2695) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2696) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2697) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2698) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2699) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2700) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2701) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2702) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2703) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2704) {
                                $currency = $value[15] ? $value[15] : null;
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $this->getCurrencyId($currency);
                            }
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            if ($v->id == 2704) {
                                $documentDetailContent->value = $currency;
                            } else {
                                $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            }
                            $documentDetailContent->save();
                        }
                    }
                }
            } elseif ($document_template_id == 618) {
                foreach ($data as $key => $value) {
                    if ($value[0]) {
                        $document_detail = new DocumentDetail();
                        $document_detail->document_id = $model->id;
                        $document_detail->content = $document_template->documentDetailTemplates[0]->content;
                        $document_detail->content .= $content;
                        $document_detail->save();
                        // dd($document_template->documentDetailTemplates[0]->documentDetailAttributes);
                        foreach ($document_template->documentDetailTemplates[0]->documentDetailAttributes as $k => $v) {
                            $document_detail_attribute_value = new DocumentDetailAttributeValue();
                            $document_detail_attribute_value->document_detail_id = $document_detail->id;
                            $document_detail_attribute_value->d_d_attribute_id = $v->id;
                            $sequence = 1;
                            if ($v->id == 2766) {
                                $sequence = 1;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2767) {
                                $sequence = 2;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2768) {
                                $sequence = 3;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2769) {
                                $sequence = 4;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2770) {
                                $sequence = 5;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2771) {
                                $sequence = 6;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2772) {
                                $sequence = 7;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2773) {
                                $sequence = 8;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2774) {
                                $sequence = 9;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2775) {
                                $sequence = 10;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2776) {
                                $sequence = 11;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2777) {
                                $sequence = 12;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2778) {
                                $sequence = 13;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2779) {
                                $sequence = 14;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2780) {
                                $sequence = 15;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2781) {
                                $sequence = 16;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2782) {
                                $sequence = 17;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2783) {
                                $sequence = 18;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2784) {
                                $sequence = 19;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2785) {
                                $sequence = 20;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2786) {
                                $sequence = 21;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2787) {
                                $sequence = 22;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2788) {
                                $sequence = 23;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2789) {
                                $sequence = 24;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } elseif ($v->id == 2790) {
                                $sequence = 25;
                                $document_detail_attribute_value->attribute_value = $value[$sequence] ? $value[$sequence] : null;
                            } 
                            $document_detail_attribute_value->save();

                            $documentDetailContent = new DocumentDetailContent();
                            $documentDetailContent->document_detail_id = $document_detail->id;
                            $documentDetailContent->d_d_attribute_id = $v->id;
                            $documentDetailContent->group_sequence = 1;
                            $documentDetailContent->sequence = $sequence;
                            $documentDetailContent->attribute_name = $v['attribute_name_' . $model->locale];
                            $documentDetailContent->value = $document_detail_attribute_value->attribute_value;
                            $documentDetailContent->save();
                        }
                    }
                }
            } else {
                DB::rollback();
                return response()->json(["message" => "Notog'ri shablon yuklandi.", "id" => null, "pdf_file_name" => null], 200);
            }

            $document_signer = new DocumentSigner();
            $document_signer->document_id = $model->id;
            $document_signer->staff_id = Auth::user()->employee->mainStaff[0]->id;
            $document_signer->taken_datetime = date('Y-m-d H:i:s');
            $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+1 day'));
            $document_signer->action_type_id = 6;
            $document_signer->sequence = 100;
            $document_signer->signer_employee_id = Auth::user()->employee_id;
            $document_signer->status = 1;
            $document_signer->department = Auth::user()->employee->mainStaff[0]->department['name_' . $model->locale];
            $document_signer->position = Auth::user()->employee->mainStaff[0]->position['name_' . $model->locale];
            $document_signer->fio = Auth::user()->employee->getShortName($model->locale);

            $document_signer->save();

            $document_signer_event = new DocumentSignerEvent();
            $document_signer_event->document_signer_id = $document_signer->id;
            $document_signer_event->action_type_id = 6;
            $document_signer_event->comment = 'created';
            $document_signer_event->status = 0;
            $document_signer_event->signer_employee_id = $document_signer->signer_employee_id;
            $document_signer_event->fio = $document_signer->fio;
            $document_signer_event->save();

            foreach ($document_template->documentSignerTemplates as $key => $value) {
                $document_signer = new DocumentSigner();
                $document_signer->document_id = $model->id;
                $document_signer->staff_id = $value->staff_id;
                // $document_signer->taken_datetime = date('Y-m-d H:i:s');
                // $document_signer->due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'+1 day'));
                $document_signer->action_type_id = $value->action_type_id;
                $document_signer->sequence = $value->sequence;
                $document_signer->status = 0;
                $document_signer->sign_type = $value->sign_type;
                $document_signer->save();
            }
            rename(public_path('centrum_tmp') . '/' . $fileName, storage_path('app') . '/documents_new/' . $newFileName);
            $file = new File();
            $file->object_id = $model->id;
            $file->object_type_id = 5;
            $file->physical_name = $newFileName;
            $file->file_name = $original_name;
            $file->created_by = Auth::id();
            $file->save();

            DB::commit();
            return response()->json(["message" => "Successfully saved!", "id" => $model->id, "pdf_file_name" => $model->pdf_file_name], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
        }
        return $model->pdf_file_name;
    }

    public function getCurrencyId($name)
    {
        $arr = [
            'UZS' => 1,
            'CNY' => 2,
            'EUR' => 3,
            'JPY' => 4,
            'KRW' => 5,
            'RUB' => 6,
            'USD' => 7,
            'CHF' => 8,
        ];

        return isset($arr[$name]) ? $arr[$name] : null;
    }

    public function generateNanoId()
    {
        $client = new Client();
        return $client->generateId($size = 21, $mode = Client::MODE_DYNAMIC);
    }
    public function getReportTest(Request $request)
    {
        $templates = [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543, 545, 546, 547, 548, 583, 588, 595, 609, 618];

        $startDate = $request->input('startdate');
        $endDate = $request->input('enddate');
        // return $summa;

        // return $summa;
        if ($startDate && $endDate) {
            $cancelled = Document::whereIn('document_template_id', $templates)->where('status', 6)->whereBetween('document_date', [$startDate, $endDate])->groupBy('document_template_id')->select('document_template_id', DB::raw('count(*) as cancel'))->with('documentTemplate')->get();
            $approved = Document::whereIn('document_template_id', $templates)->whereIn('status', [3, 4, 5])->whereBetween('document_date', [$startDate, $endDate])->groupBy('document_template_id')->select('document_template_id', DB::raw('count(*) as approve'))->get();
            $processing = Document::whereIn('document_template_id', $templates)->whereIn('status', [1, 2])->whereBetween('document_date', [$startDate, $endDate])->groupBy('document_template_id')->select('document_template_id', DB::raw('count(*) as process'))->get();
            $summa = DB::select("select d.document_template_id, ddc.attribute_name,  SUM(ddc.value) as sumakt
            from documents d
            inner join document_details dd on dd.document_id = d.id
            inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            inner join document_detail_attributes dda on dda.id = ddc.d_d_attribute_id
            where d.document_template_id in (" . implode(',', $templates) . ") and  dda.is_summa=1 and d.document_date BETWEEN '$startDate' and '$endDate' group by d.document_template_id
            ");
            $summa = Document::hydrate($summa);
        } else {
            return null;
        }

        $collection3 = $cancelled->map(function ($item) use ($approved) {
            $found = $approved->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['approve'] = $found->first()['approve'];
            }
            return $item;
        });

        $repsum = $collection3->map(function ($item) use ($summa) {
            $found = $summa->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['summa'] = $found->first()['sumakt'];
            }
            return $item;
        });
        $report = $repsum->map(function ($item) use ($processing) {
            $found = $processing->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['process'] = $found->first()['process'];
            }
            return $item;
        });
        return $report;

        // $group_data = collect($result)->groupBy('dd_id');
        // $total = count($group_data);
        // return count($group_data);

    }

    public function getReport(Request $request)
    {
        $templates = [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543, 545, 546, 547, 548, 583, 588, 595, 609, 618];

        // $startDate = $request->input('startdate');
        // $endDate = $request->input('enddate');
        $period = $request->input('act_month');
        // return $summa;

        // return $summa;
        if ($period) {
            $all = Document::whereIn('document_template_id', $templates)
                ->where('status', '!=', 0)
                ->whereHas('actDate', function($query) use($period){
                    $query->where('act_date',$period);
                })
                ->groupBy('document_template_id')->select('document_template_id', DB::raw('count(*)'))
                ->with('documentTemplate')
                ->get();
            $cancelled = Document::whereIn('document_template_id', $templates)
                ->where('status', 6)
                ->whereHas('actDate', function($query) use($period){
                    $query->where('act_date',$period);
                })
                ->groupBy('document_template_id')
                ->select('document_template_id', DB::raw('count(*) as cancel'))
                ->get();
            $approved = Document::whereIn('document_template_id', $templates)
                ->whereIn('status', [3, 4, 5])->whereHas('actDate', function($query) use($period){
                    $query->where('act_date',$period);
                })
                ->groupBy('document_template_id')
                ->select('document_template_id', DB::raw('count(*) as approve'))
                ->get();
            $processing = Document::whereIn('document_template_id', $templates)
                ->whereIn('status', [1, 2])->whereHas('actDate', function($query) use($period){
                    $query->where('act_date',$period);
                })
                ->groupBy('document_template_id')
                ->select('document_template_id', DB::raw('count(*) as process'))
                ->get();


        //         $processSum = DB::select("select d.document_template_id, MIN(ddc.attribute_name),  SUM(CASE 
        //     WHEN ddc.value ~ '^[0-9]+([,.][0-9]+)?$' THEN CAST(REPLACE(TRIM(ddc.value), ',', '') AS NUMERIC) 
        //     ELSE 0 
        // END) AS sumakt
        //         from documents d
        //         inner join document_details dd on dd.document_id = d.id
        //     inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
        //     inner join document_detail_attributes dda on dda.id = ddc.d_d_attribute_id
        //     inner join act_dates ad on ad.document_id = d.id
        //     where d.document_template_id in (" . implode(',', $templates) . ")
        //     and  dda.is_summa=1 and d.status in (1,2) and 
        //     ad.act_date = '$period' group by d.document_template_id
        //     ");

            $processSum = DB::select("select d.document_template_id, ddc.attribute_name,  SUM(CAST(REPLACE(ddc.value, ',', '') AS NUMERIC)) AS sumakt
                from documents d
                inner join document_details dd on dd.document_id = d.id
            inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            inner join document_detail_attributes dda on dda.id = ddc.d_d_attribute_id
            inner join act_dates ad on ad.document_id = d.id
            where d.document_template_id in (" . implode(',', $templates) . ") 
            and  dda.is_summa=1 and d.status in (1,2) and 
            ad.act_date = '$period' group by d.document_template_id, ddc.attribute_name
            ");
            $processSum = Document::hydrate($processSum);

            $approvedSum = DB::select("select d.document_template_id, ddc.attribute_name,  SUM(CASE WHEN ddc.value ~ E'^\\d+(\\.\\d+)?$' THEN CAST(REPLACE(ddc.value, ',', '') AS NUMERIC) ELSE 0 END) AS sumakt
            from documents d
            inner join document_details dd on dd.document_id = d.id
            inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            inner join document_detail_attributes dda on dda.id = ddc.d_d_attribute_id
            inner join act_dates ad on ad.document_id = d.id
            where d.document_template_id in (" . implode(',', $templates) . ") and  
            dda.is_summa=1 and d.status in (3,4,5) and 
            ad.act_date = '$period' group by d.document_template_id, ddc.attribute_name
            ");
            $approvedSum = Document::hydrate($approvedSum);
        } else {
            return null;
        }

        // return $approvedSum;

        $collection2 = $all->map(function ($item) use ($cancelled) {
            $found = $cancelled->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['cancel'] = $found->first()['cancel'];
            }
            return $item;
        });
        // return $collection2;
        $collection3 = $collection2->map(function ($item) use ($approved) {
            $found = $approved->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['approve'] = $found->first()['approve'];
            }
            return $item;
        });

        $repsum = $collection3->map(function ($item) use ($processSum) {
            $found = $processSum->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['processingSumma'] = $found->first()['sumakt'];
            }
            return $item;
        });

        $repsum2 = $repsum->map(function ($item) use ($approvedSum) {
            $found = $approvedSum->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['approvedSumma'] = $found->first()['sumakt'];
            }
            return $item;
        });

        $report = $repsum2->map(function ($item) use ($processing) {
            $found = $processing->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['process'] = $found->first()['process'];
            }
            return $item;
        });
        return $report;

        // $group_data = collect($result)->groupBy('dd_id');
        // $total = count($group_data);
        // return count($group_data);
    }

    public function getReport1(Request $request)
    {
        $templates = [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543, 545, 546, 547, 548, 583, 588, 595, 609, 618];

        $startDate = $request->input('startdate');
        $endDate = $request->input('enddate');
        // return $summa;

        // return $summa;
        if ($startDate && $endDate) {
            $cancelled = Document::whereIn('document_template_id', $templates)->where('status', 6)->whereBetween('document_date', [$startDate, $endDate])->groupBy('document_template_id')->select('document_template_id', DB::raw('count(*) as cancel'))->with('documentTemplate')->get();
            $approved = Document::whereIn('document_template_id', $templates)->whereIn('status', [3, 4, 5])->whereBetween('document_date', [$startDate, $endDate])->groupBy('document_template_id')->select('document_template_id', DB::raw('count(*) as approve'))->get();
            $processing = Document::whereIn('document_template_id', $templates)->whereIn('status', [1, 2])->whereBetween('document_date', [$startDate, $endDate])->groupBy('document_template_id')->select('document_template_id', DB::raw('count(*) as process'))->get();



            $processSum = DB::select("select d.document_template_id, ddc.attribute_name,  SUM(REPLACE(ddc.value, ',', '')) as sumakt
            from documents d
            inner join document_details dd on dd.document_id = d.id
            inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            inner join document_detail_attributes dda on dda.id = ddc.d_d_attribute_id
            where d.document_template_id in (" . implode(',', $templates) . ") and  dda.is_summa=1 and d.status in (1,2) and d.document_date BETWEEN '$startDate' and '$endDate' group by d.document_template_id
            ");
            $processSum = Document::hydrate($processSum);

            $approvedSum = DB::select("select d.document_template_id, ddc.attribute_name,  SUM(REPLACE(ddc.value, ',', '')) as sumakt
            from documents d
            inner join document_details dd on dd.document_id = d.id
            inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            inner join document_detail_attributes dda on dda.id = ddc.d_d_attribute_id
            where d.document_template_id in (" . implode(',', $templates) . ") and  dda.is_summa=1 and d.status in (3,4,5) and d.document_date BETWEEN '$startDate' and '$endDate' group by d.document_template_id
            ");
            $approvedSum = Document::hydrate($approvedSum);
        } else {
            return null;
        }

        $collection3 = $cancelled->map(function ($item) use ($approved) {
            $found = $approved->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['approve'] = $found->first()['approve'];
            }
            return $item;
        });

        $repsum = $collection3->map(function ($item) use ($processSum) {
            $found = $processSum->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['processingSumma'] = $found->first()['sumakt'];
            }
            return $item;
        });

        $repsum2 = $repsum->map(function ($item) use ($approvedSum) {
            $found = $approvedSum->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['approvedSumma'] = $found->first()['sumakt'];
            }
            return $item;
        });

        $report = $repsum2->map(function ($item) use ($processing) {
            $found = $processing->where('document_template_id', $item['document_template_id']);
            if ($found) {

                $item['process'] = $found->first()['process'];
            }
            return $item;
        });
        return $report;

        // $group_data = collect($result)->groupBy('dd_id');
        // $total = count($group_data);
        // return count($group_data);
    }
    public function getAttributeReport(Request $request)
    {
        $template = $request->input('template');
        $search = $request->input('search');
        $reaction = $request->input('reaction');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['per_page'];
        $offset = ($page * $itemsPerPage) - $itemsPerPage;
        // $user = Auth::user()->employee_id;
        if (in_array(2, $reaction)) {
            array_push($reaction, 4, 5);
        }

        $result = DB::select("select d.document_template_id, d.pdf_file_name, d.document_number, dd.id as dd_id, ddc.attribute_name, ddc.value 
        from documents d
        inner join document_details dd on dd.document_id = d.id
        inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
        where d.document_template_id = '$template' and  d.status in (" . implode(',', $reaction) . ")
        and ddc.value like '%' || '$search' || '%'  order by dd_id DESC
        ");
        $result =  collect($result)->pluck('dd_id')->toArray();
        $result1 = DB::select("select d.pdf_file_name, d.status, d.document_number, dd.id as dd_id, ddc.attribute_name, ddc.value 
        from documents d
        inner join document_details dd on dd.document_id = d.id
        inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
        where dd.id in (" . implode(',', $result) . ")  order by dd_id DESC
        ");
        // LIMIT $itemsPerPage OFFSET $offset");

        // $group_data = collect($result);
        $group_data = collect($result1)->groupBy('dd_id');
        // return $group_data;
        $total = count($group_data);
        $group_data = $group_data->skip($offset)->take($itemsPerPage);
        // return $group_data;
        if (count($group_data) > 0) {
            // $key = array_keys( $group_data);
            // return $key;
            $header = collect($group_data->first())->map(function ($item, $key) {
                return $item->attribute_name;
            })->all();
            return [$header, $group_data, $total, $offset];
        }
    }
    public function report123(Request $request){
        $columns = [488=>[1652,1658,1659,1660],489=>[null,1686,1687,1688],490=>[null,1714,1715,1716],491=>[1730,1736,1737,1735],
        492=>[1747,1752,1753,1754],493=>[1768,1773,1774,1775],494=>[1791,1798,1799,1800],495=>[null,null,null,1810],496=>[1820,null,1824,1827],
        497=>[1837,1894,2188,2189],498=>[null,null,null,1856],499=>[null,null,null,1864],500=>[null,null,null,1874],506=>[1907,1928,1929,1930],
        507=>[1950,1954,1955,1956],508=>[null,null,null,1975],510=>[null,null,null,1984],511=>[null,null,null,1995],512=>[null,null,null,2008],
        513=>[null,null,null,2018],514=>[null,null,null,2040],515=>[null,null,null,2063],521=>[2106,2118,2119,2120],527=>[null,2158,2159,2160],
        530=>[2177,2182,2183,2184],532=>[2211,2223,2224,2225],543=>[2262,2272,2273,2274],545=>[null,2305,2306,2307],546=>[2312,2334,2335,2336],
        547=>[null,2360,2361,2362],548=>[2368,2373,2374,2375],583=>[null,null,null,2511],588=>[null,null,2532,2533],595=>[2584,2590,2591,2592]];

        $templates = [488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 506, 507, 508, 510, 511, 512, 513, 514, 515, 521, 527, 530, 532, 543, 545, 546, 547, 548, 583, 588, 595, 609, 618];
        $act_month = $request->input('act_month');
        // dd($act_month);
        $arrival =[];
        $sign_date =[];
        $bank_rate =[];
        $amount =[];
        foreach ($columns as $key => $col) {
            if($col[0]){
                array_push($arrival, $col[0]);
            }
            if($col[1]){
                array_push($sign_date, $col[1]);
            }
            if($col[2]){
                array_push($bank_rate, $col[2]);
            }
            if($col[3]){
                array_push($amount, $col[3]);
            }
        }
        // return [$arrival, $sign_date, $bank_rate, $amount];
        if(isset($act_month)){
            // dd($act_month);
            $query = DB::select("select id,title, document_date, status, max(arrival),max(sign_date),max(bank_rate), sum(CAST(REPLACE(amount, ',','') as NUMERIC)) as allsum
            from (SELECT d.id, d.title, d.document_date, d.status,
            case when ddc.d_d_attribute_id in (" . implode(',', $arrival) . ") then ddc.value else '' end arrival,
            case when ddc.d_d_attribute_id in (" . implode(',', $sign_date) . ") then ddc.value else '' end sign_date,
            case when ddc.d_d_attribute_id in (" . implode(',', $bank_rate) . ") then ddc.value else '' end bank_rate,
            case when ddc.d_d_attribute_id in (" . implode(',', $amount) . ") then ddc.value else '' end amount
            FROM documents d
            inner join document_details dd on dd.document_id=d.id
            inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            inner join act_dates ad on ad.document_id = d.id
            where d.document_template_id in (" . implode(',', $templates) . ") and d.status != 0 and ad.act_date = '$act_month' ) t   group by id  order by document_date desc
            ");
        }
        else{

            $query = DB::select("select id, min(title), min(document_date) min_document_date, min(status), max(arrival),max(sign_date),max(bank_rate), sum(CAST(REPLACE(amount, ',','') as NUMERIC)) as allsum
            from (SELECT d.id, d.title, d.document_date, d.status,
            case when ddc.d_d_attribute_id in (" . implode(',', $arrival) . ") then ddc.value else '' end arrival,
            case when ddc.d_d_attribute_id in (" . implode(',', $sign_date) . ") then ddc.value else '' end sign_date,
            case when ddc.d_d_attribute_id in (" . implode(',', $bank_rate) . ") then ddc.value else '' end bank_rate,
            case when ddc.d_d_attribute_id in (" . implode(',', $amount) . ") then ddc.value else '' end amount
            FROM documents d
            inner join document_details dd on dd.document_id=d.id
            inner join document_detail_contents ddc on ddc.document_detail_id = dd.id
            where d.document_template_id in (" . implode(',', $templates) . ") and d.status != 0  ) t   group by id  order by min_document_date desc
            ");
        }
        return $query;
    }
}
