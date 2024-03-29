<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Output\ConsoleOutput;
use Validator;

class TicketController extends Controller
{
    /**
     * Display a list of tickets by status for specific clinic
     *
     * @param []int status [0=>expired, 1=>ongoing, 2=>ended (with comma separated)]
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clinic_id = auth()->user()->clinic->id;
        $statuses = explode(",", $request->status);
        if(strlen($request->status) == 0){
            $statuses = [Ticket::ONGOING_STATUS];
        }
        return response()->json([
            "data" => Ticket::with(['clinic','schedule'])->whereIn('status',$statuses)
                ->where('clinic_id', '=',$clinic_id)->get(),
        ]);
    }

    /**
     * Display a list of tickets by status for specific customer
     *
     * @param []int status [0=>expired, 1=>ongoing, 2=>ended (with comma separated)]
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCustomer(Request $request)
    {
        $statuses = explode(",", $request->status);
        if(strlen($request->status) == 0){
            $statuses = [Ticket::ONGOING_STATUS];
        }
        return response()->json([
            "data" => Ticket::with(['clinic','schedule'])->whereIn('status',$statuses)
                ->where('user_id', '=',auth()->user()->id)->get(),
        ]);
    }

    public function approve($id){
        $ticket = Ticket::find($id);
        $clinic_id = auth()->user()->clinic_id;
        if (is_null($ticket) || $ticket->clinic_id != $clinic_id) {
            return response()->json(['error'=>'Bad request'], Response::HTTP_BAD_REQUEST);
        }
        $ticket->status = Ticket::APPROVED_STATUS;
        $ticket->status_update_date	= Carbon::now()->toDateString();
        $ticket->save();
        return response()->json([
            'message' => 'SUCCESS SUBMIT DATA',
            'ticket' => $ticket,
        ], 201);
    }

    public function reject($id){
        $ticket = Ticket::find($id);
        $clinic_id = auth()->user()->clinic_id;
        if (is_null($ticket) || $ticket->clinic_id != $clinic_id) {
            return response()->json(['error'=>'Bad request'], Response::HTTP_BAD_REQUEST);
        }
        $ticket->status = Ticket::REJECTED_STATUS;
        $ticket->status_update_date	= Carbon::now()->toDateString();
        $ticket->save();
        return response()->json([
            'message' => 'PAYMENT FAILS',
            'ticket' => $ticket,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $current_ticket = Ticket::with('clinic')
                            ->where('user_id',auth()->user()->id)
                            ->where('status',TICKET::ONGOING_STATUS)
                            ->first();
        $validator = Validator::make($request->all(), [
            'clinic_id' => 'required',
            'schedule_id' => 'required',
            'pet_type' => 'required',
            'symptoms' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        };

        //Check if customer has already made a ticket on the same schedule
        $error_message = '';

        if(!empty($current_ticket)){
            if($request->clinic_id == $current_ticket->clinic_id){
                $error_message = 'You already made an appoinment in this clinic !';
            }else{
                $error_message = 'You already made an appoinment at '.$current_ticket->clinic->clinic_name. ' !';
            }
            return response()->json(['error'=>$error_message], Response::HTTP_BAD_REQUEST);
        }

        $ticket = Ticket::create(
            array_merge(
                $validator->validated(),
                [
                    'user_id' => auth()->user()->id,
                    'status' => Ticket::ONGOING_STATUS,
                    'status_update_date' => Carbon::now()->toDateTimeString(),
                ]
            )
        );
        return response()->json([
            'message' => 'SUCCESS SUBMIT DATA',
            'ticket' => $ticket,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
