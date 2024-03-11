<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Filesystem\Filesystem,
    Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand,
    Xthiago\PDFVersionConverter\Converter\GhostscriptConverter;
use Xthiago\PDFVersionConverter\Guesser\RegexGuesser;

class PostOrder extends Model
{
    protected $connection = 'workflow_log';
    protected $table = 'post_orders';
    public $timestamps = false;
    //
    protected $fillable = [
        "diller",
        "number",
        "client",
        "modification",
        "color",
        "contract_number",
        "user_type",
        "estimated_delivery_date",
        "inn",
        "client_type",
        "pinfl",
        "region",
        "address",
        "group_code",
        "part_number",
        "status",
        "area",
        "region_id",
        "area_id",
        "model",
    ];

    public static function generatePdfDamas($id)
    {
        $order = Self::find($id);
        $months = [
            '01' => 'yanvar',
            '02' => 'fevral',
            '03' => 'mart',
            '04' => 'aprel',
            '05' => 'may',
            '06' => 'iyun',
            '07' => 'iyul',
            '08' => 'avgust',
            '09' => 'sentabr',
            '10' => 'oktabr',
            '11' => 'noyabr',
            '12' => 'dekabr',
        ];
        // 2022-09-29
        $year = substr($order->send_date, 0, 4);
        $month = substr($order->send_date, 5, 2);
        $day = substr($order->send_date, 8, 2);

        $content = Self::style();
        $content .= '<br>';
        $content .= '<img width="250" style="position:fixed;" src="' . public_path('img/burchak.png') . '" />';
        $content .= '<div style="font-size:16pt;font-weight:bold;position:fixed;top:190px;left:30px;text-align:center;width:158px;">' . $order->number . '</div>';
        $content .= '<div style="font-size:16pt;font-weight:bold;position:fixed;top:228px;left:30px;text-align:center;width:48px;">' . $day . '</div>';
        $content .= '<div style="font-size:16pt;font-weight:bold;position:fixed;top:228px;left:92px;text-align:center;width:80px;">' . $months[$month] . '</div>';
        $content .= '<div style="font-size:16pt;font-weight:bold;position:fixed;top:228px;left:176px;text-align:center;width:50px;">' . $year . '</div>';
        $content .= '<table style="width:100%;font-size:16pt;" border="0">';
        $content .= '<tr>';
        $content .= '<td style="width:60%;">';
        $content .= '</td>';
        $content .= '<td style="width:40%;text-align:center;">';
        // $content .= $order->address;
        $content .= $order->contract_number;
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td colspan="2" style="height:40px;">';
        $content .= '<td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="width:60%;">';
        $content .= '</td>';
        $content .= '<td style="width:40%;text-align:right;">';
        // $content .= $order->contract_number;
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</table>';

        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';

        $content .= '<div style="text-align:center;font-size:16pt;">';
        $content .= 'Hurmatli <b>' . $order->client . '</b>';
        $content .= '</div>';
        $content .= '<br>';
        $content .= '<br>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= '“UzAuto Motors” Kompaniyasi bizning mijozimiz ekanligingiz uchun Sizga samimiy minnatdorchilik bildiradi!';
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Maʼlumki, soʼnggi ikki yil ichida avtomobil bozori COVID-19 pandemiyasining oqibatlari, logistika va Ukrainadagi geosiyosiy vaziyat kabi bir qator global muammolarga duch keldi. Ushbu kataklizmlar dunyodagi barcha avtomobil ishlab chiqaruvchilariga o’z ta'sirini ko'rsatdi. Ishlab chiqarish hajmini sezilarli darajada kamaytirishga, opsiyalarni qisqartirishga va hatto zavodlarni to'xtatishga majbur qildi.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "“UzAuto Motors” jahon avtomobil bozorida kuzatilayotgan inqirozlarga qaramay, Ishlab chiqarishda texnologik jarayonlarni takomillashtirish va optimallashtirish natijasida ishlab chiqarish quvvati sezilarli darajada oshirildi.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "“UzAuto Motors” global inqiroz davrida ishlab chiqarish hajmi sezilarli darajada oshganiga qaramay, shu bilan birga muhim butlovchi qismlarning keskin taqchilligini boshdan kechirishda davom etmoqda. Shuningdek, “UzAuto Motors” AJ Xorazm filialida 2023 yil 12 aprel kunidan 2023 yil 5 mayga qadar ishlab chiqarish jarayonida modernizatsiya va texnik xizmat ko‘rsatish amalga oshirilayotganligi sababli ishlab chiqarishda to’xtalishlar yuzaga kelayotganligini ma’lum qilamiz.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Afsuski, Kompaniya Sizga avtomobilingiz kechikib yetkazilishi haqida xabar berishga majbur. Yuqoridagi holatlarni inobatga olgan holda, avtomobil sotib olish shartnomasining 3.1.1-bandga muvofiq, avtomobilni yetkazib berish muddati 3.1-bandda ko'rsatilgan muddatdan 45 kunga uzaytirildi.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Kompaniya yetkazilgan noqulayliklar uchun uzr so’raydi va tushunishingizni umid qiladi. Sizga shuni ishontirib aytadiki, avtomobilni tezroq topshirish uchun qo’lidan kelgan barcha sa’y-harakatlarni amalga oshiriladi.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Hurmat bilan, “UzAuto Motors” Kompaniyasi";
        $content .= '</div>';

        $pdf = \App::make('snappy.pdf.wrapper');
        $pdf->setOption('images', true)
            // ->setOption('footer-right', '[page] / [topage]')
            // ->setOption('footer-font-name', 'times')
            // ->setOption('footer-font-size', '10')
            ->setPaper('a4')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 20)
            ->setOption('margin-right', 15)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $filename = public_path('post/' . microtime(true) * 10000) . '.pdf';
            $pdf->save($filename);
            // $command = new GhostscriptConverterCommand();
            // $filesystem = new Filesystem();
            // $converter = new GhostscriptConverter($command, $filesystem);
            // $converter->convert($filename, '1.4');
            // $guesser = new RegexGuesser();
            // $guesser->guess($filename); // will print something like '1.4'
            $file = file_get_contents($filename);
            unlink($filename);
            return $file; // will print something like '1.4'
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public static function generatePdf($id)
    {
        $order = Self::find($id);
        if($order->model == 1){
            return Self::generatePdfDamas($id);
        }
        $months = [
            '01' => 'yanvar',
            '02' => 'fevral',
            '03' => 'mart',
            '04' => 'aprel',
            '05' => 'may',
            '06' => 'iyun',
            '07' => 'iyul',
            '08' => 'avgust',
            '09' => 'sentabr',
            '10' => 'oktabr',
            '11' => 'noyabr',
            '12' => 'dekabr',
        ];
        // 2022-09-29
        $year = substr($order->send_date, 0, 4);
        $month = substr($order->send_date, 5, 2);
        $day = substr($order->send_date, 8, 2);

        $content = Self::style();
        $content .= '<br>';
        $content .= '<img width="250" style="position:fixed;" src="' . public_path('img/burchak.png') . '" />';
        $content .= '<div style="font-size:16pt;font-weight:bold;position:fixed;top:190px;left:30px;text-align:center;width:158px;">' . $order->number . '</div>';
        $content .= '<div style="font-size:16pt;font-weight:bold;position:fixed;top:228px;left:30px;text-align:center;width:48px;">' . $day . '</div>';
        $content .= '<div style="font-size:16pt;font-weight:bold;position:fixed;top:228px;left:92px;text-align:center;width:80px;">' . $months[$month] . '</div>';
        $content .= '<div style="font-size:16pt;font-weight:bold;position:fixed;top:228px;left:176px;text-align:center;width:50px;">' . $year . '</div>';
        $content .= '<table style="width:100%;font-size:16pt;" border="0">';
        $content .= '<tr>';
        $content .= '<td style="width:60%;">';
        $content .= '</td>';
        $content .= '<td style="width:40%;text-align:center;">';
        // $content .= $order->address;
        $content .= $order->contract_number;
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td colspan="2" style="height:40px;">';
        $content .= '<td>';
        $content .= '</tr>';
        $content .= '<tr>';
        $content .= '<td style="width:60%;">';
        $content .= '</td>';
        $content .= '<td style="width:40%;text-align:right;">';
        // $content .= $order->contract_number;
        $content .= '</td>';
        $content .= '</tr>';
        $content .= '</table>';

        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';

        $content .= '<div style="text-align:center;font-size:16pt;">';
        $content .= 'Hurmatli <b>' . $order->client . '</b>';
        $content .= '</div>';
        $content .= '<br>';
        $content .= '<br>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= '“UzAuto Motors” Kompaniyasi bizning mijozimiz ekanligingiz uchun Sizga samimiy minnatdorchilik bildiradi!';
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Maʼlumki, soʼnggi ikki yil ichida avtomobil bozori COVID-19 pandemiyasining oqibatlari, mikrochiplar inqirozi, logistika va Ukrainadagi geosiyosiy vaziyat kabi bir qator global muammolarga duch keldi. Ushbu kataklizmlar dunyodagi barcha avtomobil ishlab chiqaruvchilariga o’z ta'sirini ko'rsatdi. Ishlab chiqarish hajmini sezilarli darajada kamaytirishga, opsiyalarni qisqartirishga va hatto zavodlarni to'xtatishga majbur qildi.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Ammo “UzAuto Motors” jahon avtomobil bozorida kuzatilayotgan inqirozlarga qaramay, mikrochiplarni o’zida mujassam etgan komponentlarning oldindan yaratilgan zaxirasi joriy yilda konveyerlarning to’xtashiga yo'l qo'ymadi. Ishlab chiqarishda texnologik jarayonlarni takomillashtirish va optimallashtirish natijasida “UzAuto Motors” ishlab chiqarish quvvati sezilarli darajada oshirildi.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "“UzAuto Motors” global inqiroz davrida ishlab chiqarish hajmi sezilarli darajada oshganiga qaramay, shu bilan birga muhim butlovchi qismlarning keskin taqchilligini boshdan kechirishda davom etmoqda. Xaridorlarga deyarli tayyor ammo birgina zaruriy butlovchi qismlarsiz avtomobillarni topshirish imkonsiz. Sizning avtomobilingiz ham deyarli tayyor holda turibdi, lekin amalga oshirilgan kompleks chora-tadbirlarga qaramay, O’zbekistonga yetkazib berilishi kerak bo’lgan mikrochiplardan iborat  zaruriy detallarni kutmoqda.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Afsuski, Kompaniya Sizga avtomobilingiz kechikib yetkazilishi haqida xabar berishga majbur. Avtomobil ishlab chiqaruvchiga bog’liq bo’lmagan holatlar tufayli,  avtomobil sotib olish shartnomasining 3.1.1-bandga muvofiq, avtomobilni yetkazib berish muddati 3.1-bandda ko'rsatilgan muddatdan 45 kunga uzaytirildi.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Kompaniya yetkazilgan noqulayliklar uchun uzr so’raydi va tushunishingizni umid qiladi. Sizga shuni ishontirib aytadiki, avtomobilni tezroq topshirish uchun qo’lidan kelgan barcha sa’y-harakatlarni amalga oshiriladi.";
        $content .= '</div>';

        $content .= '<div style="text-align:justify;font-size:16pt;text-indent: 50px;margin-bottom:20px;">';
        $content .= "Hurmat bilan, “UzAuto Motors” Kompaniyasi";
        $content .= '</div>';

        $pdf = \App::make('snappy.pdf.wrapper');
        $pdf->setOption('images', true)
            // ->setOption('footer-right', '[page] / [topage]')
            // ->setOption('footer-font-name', 'times')
            // ->setOption('footer-font-size', '10')
            ->setPaper('a4')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 20)
            ->setOption('margin-right', 15)
            ->setOption('disable-smart-shrinking', false)
            ->loadHTML($content);
        try {
            $filename = public_path('post/' . microtime(true) * 10000) . '.pdf';
            $pdf->save($filename);
            // $command = new GhostscriptConverterCommand();
            // $filesystem = new Filesystem();
            // $converter = new GhostscriptConverter($command, $filesystem);
            // $converter->convert($filename, '1.4');
            // $guesser = new RegexGuesser();
            // $guesser->guess($filename); // will print something like '1.4'
            $file = file_get_contents($filename);
            unlink($filename);
            return $file; // will print something like '1.4'
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public static function style()
    {
        return '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face{
                font-family:  times;
                font-style: normal;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bold;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: italic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Italic.ttf") format("truetype");
            }
            @font-face{
                font-family:  times;
                font-style: bolditalic;
                font-weight: normal;
                src: url("' . base_path() . '/vendor/tecnickcom/tcpdf/fonts/Times New Roman Bold Italic.ttf") format("truetype");
            }
            p[style*="text-align: justify;"]{
                text-indent: 40px;
                // margin-right:20px;
            }
            b, strong{
                font-family: "freeserif";
                letter-spacing:1px;
            }
            @page {
                padding: 0px 0px 0px;
            }
            body{
                 font-family: "times";
                //font-family: "freeserif";
                letter-spacing:1px;
                // text-rendering: auto;
                // text-rendering: optimizeSpeed;
                // text-rendering: optimizeLegibility;
                text-rendering: geometricPrecision;
            }
            #line1 {
                border-bottom:1px solid black;
                margin-top:30px;
            }
            #line10 {border-top:5px solid black;}
        </style>';
    }
}
