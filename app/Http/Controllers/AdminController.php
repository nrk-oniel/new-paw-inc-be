<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Output\ConsoleOutput;
use Validator;

class AdminController extends Controller
{
    /**
     * Display a list of staff with pagination
     * By default return 10 per page.
     *
     * @param int $per_page defines how many rows returned per page
     * @return \Illuminate\Http\Response
     */
    public function indexStaff(Request $request)
    {
        $perPage = $request->per_page;
        if($perPage <= 0){
            $perPage = 10;
        }
        $user = User::with('clinic')->where('role_id','=',Role::ROLE_STAFF);
        if(strlen($request->clinic_name)){
            $user = $user->whereHas('clinic', function ($q) use($request){
                $q->where('clinic_name','LIKE', '%'.$request->clinic_name.'%');
            } );
        }
        return response()->json([
            "data" => $user->paginate($perPage),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  string clinic_name
     * @param2 string email
     * @param3 string phone_number
     * @param4 string address
     *
     * @return \Illuminate\Http\Response
     */
    public function storeStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clinic_name' => 'required|unique:clinics',
            'email' => 'required|email',
            'phone_number' => 'required',
            'address' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'message'=>'FAILED TO SUBMIT DATA',
            ], Response::HTTP_BAD_REQUEST);
        }
        $clinic = Clinic::create(
            [
                "clinic_name" => $request->clinic_name,
                "clinic_address" => $request->address,
            ]
        );
        $user = User::create(
            [
                "username" => $request->clinic_name,
                "clinic_id" => $clinic->id,
                "email" => $request->email,
                "phone_number" => $request->phone_number,
                "address" => $request->address,
                "password" => bcrypt($request->clinic_name),
                "role_id" => Role::ROLE_STAFF,
            ]
        );
        return response()->json([
            'message' => 'SUCCESS SUBMIT DATA',
            'user' => $user,
            'test' => $clinic,
        ], 201);
    }

    /**
     * Display user staff by ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showStaff($id)
    {
        return response()->json([
            'data' => User::where('id','=',$id)
                ->where('role_id', Role::ROLE_STAFF)->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStaff(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'clinic_name' => 'required|unique:clinics',
            'email' => 'required|email',
            'phone_number' => 'required',
            'address' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('id','=',$id)
            ->where('role_id', Role::ROLE_STAFF)->first();
        if(is_null($user)) {
            return response()->json(['error'=>'FAILED TO SUBMIT DATA'], Response::HTTP_BAD_REQUEST);
        }

        $user->username = $request->clinic_name;

        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        $user->password = bcrypt($request->clinic_name);
        $user->clinic->clinic_name = $request->clinic_name;
        $user->clinic->clinic_address = $request->address;
        $user->push();

        return response()->json([
            'message' => 'SUCCESS SUBMIT DATA',
            'user' => $user
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyStaff($id)
    {
        $user = User::where('id','=',$id)
            ->where('role_id', Role::ROLE_STAFF)->first();
        $clinic = Clinic::find($user->clinic_id);
        $deleted = $user->delete();
        if ($deleted == 0) {
            return response()->json([
                'error' => 'FAILED TO DELETE THE ACCOUNT',
            ], Response::HTTP_BAD_REQUEST);
        }
        $clinic->delete();
        return response()->json([
            'message' => 'SUCCESS DELETE ACCOUNT',
        ], 201);
    }
}
