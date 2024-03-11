<?php

namespace App\Http\Controllers;

use App\WorkCalendar;
use Illuminate\Http\Request;

class WorkCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($year)
    {
        $calendars = collect(WorkCalendar::where('calendar_date','like', $year.'%')->get())->groupBy(function ($item, $key) {
            return substr($item['calendar_date'], 0, 7);
        })
        ->map(function ($data) {
            $data = collect($data)->map(function ($d) {
                $n = date('D', strtotime($d->calendar_date));
                switch ($n) {
                    case 'Mon':
                    $d->week_day = 'Du';
                    break;
                    case 'Tue':
                    $d->week_day = 'Se';
                    break;
                    case 'Wed':
                    $d->week_day = 'Ch';
                    break;
                    case 'Thu':
                    $d->week_day = 'Pa';
                    break;
                    case 'Fri':
                    $d->week_day = 'Ju';
                    break;
                    case 'Sat':
                    $d->week_day = 'Sh';
                    break;
                    case 'Sun':
                    $d->week_day = 'Ya';
                    break;
                }
                
                return $d;
            });
            $data[0]->month = date('M', strtotime($data[0]->calendar_date));
            return $data;
        })->toArray();

        return $calendars;
    }

    public function generateCalendar($year){
        $tmp =  WorkCalendar::where('calendar_date', 'like', '%' . $year . '%')->get();
         if(count($tmp)<=0)
        {
            for ($m=1; $m < 13; $m++) { 
                $day_count = date('t', strtotime($year.'-'.$m.'-01'));
                for ($i = 1; $i <= $day_count; $i++) {
    
                    $workCalendar = new WorkCalendar();
                    $workCalendar->calendar_date = $year.'-'.str_pad($m,  2, "0",STR_PAD_LEFT).'-'.str_pad($i,  2, "0",STR_PAD_LEFT);
                    // dd($workCalendar->calendar_date);
    
                    if(date('N', strtotime($workCalendar->calendar_date)) != 6 && date('N', strtotime($workCalendar->calendar_date)) != 7 ){
                        $workCalendar->is_weekend = 0;
                        $workCalendar->is_work_day = 1;
                    }
                    else{
                        $workCalendar->is_weekend = 1;
                        $workCalendar->is_work_day = 0;
                    }
                    $workCalendar->work_day_sequence = date('z', strtotime($workCalendar->calendar_date));
                    $workCalendar->is_holiday = 0;
                    $workCalendar->save();
                }
            }
            print_r("Yangi yil ma'lumotlari saqlandi");
        }
        else{
            print_r("Ushbu yil ma'lumotlari kiritilgan");
        }
        
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
     * @param  \App\WorkCalendar  $workCalendar
     * @return \Illuminate\Http\Response
     */
    public function show(WorkCalendar $workCalendar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WorkCalendar  $workCalendar
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkCalendar $workCalendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WorkCalendar  $workCalendar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkCalendar $workCalendar)
    {
        $calendar_date = $request->input('calendar_date');
        $wc = WorkCalendar::where('calendar_date', $calendar_date)->first();
        $wc->holiday_name = $request->input('holiday_name');
        $wc->is_holiday = $request->input('is_holiday');
        $wc->is_weekend = $request->input('is_weekend');
        $wc->is_work_day = $request->input('is_work_day');
        $wc->save();
        $seq = 1;
        foreach (WorkCalendar::get() as $key => $value) {
            if ($value->is_work_day) {
                $value->work_day_sequence = $seq++;
                $value->save();
            }
        }
        // return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WorkCalendar  $workCalendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkCalendar $workCalendar)
    {
        //
    }
}
