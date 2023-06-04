<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Pnlinh\GoogleDistance\Facades\GoogleDistance;
use Symfony\Component\Console\Output\ConsoleOutput;

class ClinicController extends Controller
{
    //Geocoder Function
    public function geocoder($address){
        $geocode = app('geocoder')->geocode($address)->get();
        $array = array (
            'lat' => $geocode[0]->getCoordinates()->getLatitude(),
            'long' => $geocode[0]->getCoordinates()->getLongitude(),
        );
        return $array;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $new_array = [];
        $console = new ConsoleOutput();
        $clinics = Clinic::query();
        $user = User::find(auth()->user()->id);
        if(strlen($request->clinic_name)){
            $clinics = $clinics->where('clinic_name','LIKE','%'.$request->clinic_name.'%')->get();
        }else{
            $clinics = $clinics->get();
        }
        // $console->writeln(User::find(auth()->user()->id));


        foreach($clinics as $clinic){
            $distance = GoogleDistance::calculate($user->address, $clinic->clinic_address);
            // $console->writeln($distance);
            $array = array (
                'id' => $clinic->id,
                'clinic_name' => $clinic->clinic_name,
                'clinic_address' => $clinic->clinic_address,
                'coordinates' => $this->geocoder($clinic->clinic_address),
                'distance' => $distance,
            );
            array_push($new_array,$array);
        }

        return response()->json([
            'data' => $new_array,
        ]);

        /*
        Example Response Data: 
        {
            "id": 1,
            "clinic_name": "Clinic Staff 1",
            "clinic_address": "Jalan Garuda Kencana",
            "coordinates": {
                "lat": -6.3210362,
                "long": 106.6857431
            },
            "distance": "3.1 km"
        },
        */
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
     * @param  \App\Models\Clinic  $clinic
     * @return \Illuminate\Http\Response
     */
    public function show(Clinic $clinic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Clinic  $clinic
     * @return \Illuminate\Http\Response
     */
    public function edit(Clinic $clinic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Clinic  $clinic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clinic $clinic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Clinic  $clinic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clinic $clinic)
    {
        //
    }
}
