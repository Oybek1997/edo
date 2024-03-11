<?php

namespace App\Http\Controllers;

//use App\AS400\Oyliksts;

use App\AS400\Oyliksts;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class KoreshokController extends Controller
{
    public function index(Request $request)
    {
        return "Koreshok haqida ma'lumotlarni bugalteriya bo'limidan olishingiz mumkin.";
        $from_year = $request->input('from_year');
        $from_month = $request->input('from_month');
        $to_year = $request->input('to_year');
        $to_month = $request->input('to_month');
        $seq = $request->input('seq');
        $user = Auth::user();
        $tabel = $request->input('tabel');
        
        if (strtotime($from_year.'-'.$from_month) > strtotime($to_year.'-'.$to_month)) {
            return [
                'base64' => '',
                'status' => 0,
                'from_year' => $from_year,
                'from_month' => $from_month,
                'to_year' => $to_year,
                'to_month' => $to_month,
                'seq' => $seq,
            ];
        }
        
        $oylik = 1;//Oyliksts::where('YILOY', $from_year.$from_month)->where('STS', 1)->first();
        if ($seq == 0 && !$oylik) {
            $date = date('Ym', strtotime("-1 month", strtotime($from_year.'-'.$from_month)));
            $from_year = substr($date, 0, 4);
            $from_month = substr($date, 4, 2);
            // dd($from_year,
            // $from_month);
            $data = [
                'tn' => $user->hasRole('koreshok_admin') && isset($tabel) ? $tabel : Auth::user()->employee->tabel,
                'from_year' => $from_year,
                'from_month' => $from_month,
            ];
            $html = view('koreshok.index', compact('data'))->render();
        } elseif ($from_year == $to_year && $from_month == $to_month) {
            $oylik = 1;//Oyliksts::where('YILOY', $from_year.$from_month)->where('STS', 1)->first();
            if (!$oylik && $tabel != '8630' && $tabel != '5399' && $tabel != '6778' && $tabel != '9592' && $tabel != 'T073') {
                return [
                    'base64' => '',
                    'status' => 0,
                    'from_year' => $from_year,
                    'from_month' => $from_month,
                    'to_year' => $to_year,
                    'to_month' => $to_month,
                    'seq' => $seq,
                ];
            }
            $data = [
                'tn' => $user->hasRole('koreshok_admin') && isset($tabel) ? $tabel : Auth::user()->employee->tabel,
                'from_year' => $from_year,
                'from_month' => $from_month,
            ];
            // print_r(compact('data'));die;
            $html = view('koreshok.index', compact('data'))->render();
        } else {
            $oylik1 = 1;//Oyliksts::where('YILOY', $from_year.$from_month)->where('STS', 1)->first();
            $oylik2 = 1;//Oyliksts::where('YILOY', $to_year.$to_month)->where('STS', 1)->first();
            if (!$oylik1 && !$oylik2) {
                return [
                    'base64' => '',
                    'status' => 0,
                    'from_year' => $from_year,
                    'from_month' => $from_month,
                    'to_year' => $to_year,
                    'to_month' => $to_month,
                    'seq' => $seq,
                ];
            }
            $data = [
                'tn' => $user->hasRole('koreshok_admin') && isset($tabel) ? $tabel : Auth::user()->employee->tabel,
                'from_year' => $from_year,
                'from_month' => $from_month,
                'to_year' => $to_year,
                'to_month' => $to_month,
            ];
            $html = view('koreshok.period', compact('data'))->render();
        }

        $pdf = App::make('snappy.pdf.wrapper');
        // $pdf->setOrientation('landscape');
        $pdf->setOption('images', true)
            ->setOption('footer-right', '[page] / [topage]')
            ->setPaper('a4')
            ->loadHTML($html);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return [
            'base64' => $base64,
            'status' => 1,
            'from_year' => $from_year,
            'from_month' => $from_month,
            'to_year' => $to_year,
            'to_month' => $to_month,
            'seq' => $seq,
        ];
    }

    public function test($month,$tabel)
    {

        // return "Using ". (memory_get_peak_usage()). " bytes of ram.";
        $from_year = 2021;
        $from_month = $month;
        $to_year = 2021;
        $to_month = $month;
        $seq = 1;
        $user = User::find(1);
        // $tabel = "8630";

        if (strtotime($from_year.'-'.$from_month) > strtotime($to_year.'-'.$to_month)) {
            return [
                'base64' => '',
                'status' => 0,
                'from_year' => $from_year,
                'from_month' => $from_month,
                'to_year' => $to_year,
                'to_month' => $to_month,
                'seq' => $seq,
            ];
        }

        $oylik = Oyliksts::where('YILOY', $from_year.$from_month)->where('STS', 1)->first();
        if ($seq == 0 && !$oylik) {
            $date = date('Ym', strtotime("-1 month", strtotime($from_year.'-'.$from_month)));
            $from_year = substr($date, 0, 4);
            $from_month = substr($date, 4, 2);
            // dd($from_year,
            // $from_month);
            $data = [
                'tn' => $user->hasRole('koreshok_admin') && isset($tabel) ? $tabel : Auth::user()->employee->tabel,
                'from_year' => $from_year,
                'from_month' => $from_month,
            ];
            $html = view('koreshok.index', compact('data'))->render();
        } elseif ($from_year == $to_year && $from_month == $to_month) {
            $oylik = Oyliksts::where('YILOY', $from_year.$from_month)->where('STS', 1)->first();
            if (!$oylik && $tabel != '8630' && $tabel != '5399' && $tabel != '6778' && $tabel != '9592' && $tabel != 'T073') {
                return [
                    'base64' => '',
                    'status' => 0,
                    'from_year' => $from_year,
                    'from_month' => $from_month,
                    'to_year' => $to_year,
                    'to_month' => $to_month,
                    'seq' => $seq,
                ];
            }
            $data = [
                'tn' => $user->hasRole('koreshok_admin') && isset($tabel) ? $tabel : Auth::user()->employee->tabel,
                'from_year' => $from_year,
                'from_month' => $from_month,
            ];
            $html = view('koreshok.index', compact('data'))->render();
        } else {
            $oylik1 = Oyliksts::where('YILOY', $from_year.$from_month)->where('STS', 1)->first();
            $oylik2 = Oyliksts::where('YILOY', $to_year.$to_month)->where('STS', 1)->first();
            if (!$oylik1 && !$oylik2) {
                return [
                    'base64' => '',
                    'status' => 0,
                    'from_year' => $from_year,
                    'from_month' => $from_month,
                    'to_year' => $to_year,
                    'to_month' => $to_month,
                    'seq' => $seq,
                ];
            }
            $data = [
                'tn' => $user->hasRole('koreshok_admin') && isset($tabel) ? $tabel : Auth::user()->employee->tabel,
                'from_year' => $from_year,
                'from_month' => $from_month,
                'to_year' => $to_year,
                'to_month' => $to_month,
            ];
            $html = view('koreshok.period', compact('data'))->render();
        }

        return $html;

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOrientation('landscape');
        $pdf->setOption('images', true)
            ->setOption('footer-right', '[page] / [topage]')
            ->setPaper('a4')
            ->loadHTML($html);
        try {
            $base64 = base64_encode($pdf->inline());
        } catch (\Throwable $th) {
            dd($th);
        }
        return [
            'base64' => $base64,
            'status' => 1,
            'from_year' => $from_year,
            'from_month' => $from_month,
            'to_year' => $to_year,
            'to_month' => $to_month,
            'seq' => $seq,
        ];
    }

    public function getMonthName($month)
    {
        return $this->oylar[$month];
    }

    public $oylar = [
        "01" => "Yan",
        "02" => "Fev",
        "03" => "Mar",
        "04" => "Apr",
        "05" => "May",
        "06" => "Iyun",
        "07" => "Iyul",
        "08" => "Avg",
        "09" => "Sen",
        "10" => "Okt",
        "11" => "Noy",
        "12" => "Dek",
    ];
    public function getOylik(Request $request)
    {
        if ($request->all() != null) {
            $last = Oyliksts::orderBy('yiloy', 'desc')->pluck('yiloy')->first();
            $last = substr($last, 0, 4) . '-' . substr($last, -2, 2);
            $last = date('Ym', strtotime("+1 month", strtotime($last)));
            $model = new Oyliksts();
            $model->YILOY = $last;
            $model->STS = 0;
            $model->save();
        }

        $oyliks = Oyliksts::where('yiloy', '>', 0)->get();
        foreach ($oyliks as $value) {
            $month_name= substr($value->yiloy, -2, 2);
            $value['month_name'] = $this->getMonthName($month_name);
        }

        $grouped = collect($oyliks)->groupBy(function ($item, $key) {
            return substr($item['yiloy'], 0, 4);
        });

        $i = count($grouped);
        $gr=[];
        foreach ($grouped as $key=>$value) {
            $gr[$i]= $value;
            $i--;
        }
        return $gr;
    }

    public function statusUpdate(Request $request)
    {
        $item =  $request->input('yiloy');
        $model = Oyliksts::where('YILOY', $item)->first();
        if ($model) {
            if ($model->sts == 1) {
                Oyliksts::where('YILOY', $item)->update(['STS' => 0]);
            } else {
                Oyliksts::where('YILOY', $item)->update(['STS' => 1]);
            }
        }
    }
}
