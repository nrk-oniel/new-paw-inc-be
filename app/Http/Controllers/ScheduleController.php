<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Output\ConsoleOutput;
use Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexByClinicID(Request $request,$id)
    {
        $schedule = Schedule::with('clinic')->where('clinic_id','=',$id);
        if(strlen($request->hour)){
            $schedule = $schedule->where('hour','=',$request->hour);
        }
        if(strlen($request->minute)){
            $schedule = $schedule->where('minute','=',$request->minute);
        }
        if(strlen($request->date)){
            $schedule = $schedule->where('date','=',$request->date);
        }
        if(strlen($request->month)){
            $schedule = $schedule->where('month','=',$request->month);
        }
        if(strlen($request->year)){
            $schedule = $schedule->where('year','=',$request->year);
        }
        return response()->json([
            'data' => $schedule->get(),
        ]);
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
     * @param  \App\Http\Requests\StoreScheduleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required',
            'month' => 'required',
            'date' => 'required',
            'hour' => 'required',
            'minute' => 'required',
            'doctor_name' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'message'=>'FAILED TO sUBMIT DATA',
            ], Response::HTTP_BAD_REQUEST);
        }
        $schedule = Schedule::create(
            array_merge(
                $validator->validated(),
                ['clinic_id' => auth()->user()->clinic->id]
            )
        );
        return response()->json([
            'message' => 'SUCCESS SUBMIT DATA',
            'schedule' => $schedule
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required',
            'month' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'message'=>'FAILED TO GET DATA',
            ], Response::HTTP_BAD_REQUEST);
        }
        $schedules = Schedule::where('year','=', $request->year)
            ->where('month','=', $request->month)
            // ->where('clinic_id','=',$request->clinicId)
            ->orderBy('date')
            ->orderBy('hour')
            ->orderBy('minute')->get();

        $dates = [];
        foreach($schedules as $s) {
            $date = $s->date;
            if(!key_exists($date, $dates)){
                $dates[$date] = array();
            }
            $time = $s->hour.":".$s->minute;
            if(!key_exists($time, $dates[$date])) {
                $dates[$date][$time] = array();
            }
            array_push($dates[$date][$time], $s->doctor_name);
        }
        if(count($dates)==0){
            $dates = null;
        }
        return response()->json([
            'data' => ['schedules'=>$dates],
        ]);
    }

        /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function showById(Request $request)
    {
        // $console = new ConsoleOutput();
        $clinic = auth()->user()->clinic->id;
        // $console->write($clinic);
        $validator = Validator::make($request->all(), [
            'year' => 'required',
            'month' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'message'=>'FAILED TO GET DATA',
            ], Response::HTTP_BAD_REQUEST);
        }
        $schedules = Schedule::where('year','=', $request->year)
            ->where('month','=', $request->month)
            ->where('clinic_id','=',$clinic)
            ->orderBy('date')
            ->orderBy('hour')
            ->orderBy('minute')->get();

        $dates = [];
        foreach($schedules as $s) {
            $date = $s->date;
            if(!key_exists($date, $dates)){
                $dates[$date] = array();
            }
            $time = $s->hour.":".$s->minute;
            if(!key_exists($time, $dates[$date])) {
                $dates[$date][$time] = array();
            }
            array_push($dates[$date][$time], $s->doctor_name);
        }
        if(count($dates)==0){
            $dates = null;
        }
        return response()->json([
            'data' => ['schedules'=>$dates],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateScheduleRequest  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
