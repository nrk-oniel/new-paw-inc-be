<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Output\ConsoleOutput;
use Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware(['api','authOnly'], ['except' => ['login', 'register']]);
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json([
                'error' => 'Account not found',
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a user as a Customer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|email',
            'password' => 'required|string|confirmed',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User Info by token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        $console = new ConsoleOutput();
        $user = User::with(['clinic','role'])->find(auth()->user()->id);
        
        $geocode = app('geocoder')->geocode($user->address)->get();
        $array = array (
            'lat' => $geocode[0]->getCoordinates()->getLatitude(),
            'long' => $geocode[0]->getCoordinates()->getLongitude(),
        );
        // return response()->json([
        //     "data" =>User::with(['clinic','role'])->find(auth()->user()->id),
        // ]);
        $new_user_array = array (
            "id" => $user->id,
            "username" => $user->username,
            "email" => $user->email,
            "phone_number" => $user->phone_number,
            "address" => $user->address,
            "role_id" => $user->role_id,
            "created_at" => $user->created_at,
            "updated_at" => $user->updated_at,
            "clinic" => $user->clinic,
            "role" => $user->role,
            "coordinates" => $array,
        ) ;
        // $console->writeln($new_user_array);
        return response()->json([
            "data" =>$new_user_array,
        ]);

        /*
        Example Dummy Data
            {
                "id": 3,
                "username": "customer",
                "email": "customer@gmail.com",
                "phone_number": "",
                "address": "Jalan Aru Rawa Mekar Jaya",
                "clinic_id": null,
                "role_id": 1,
                "created_at": "2023-06-03T15:17:01.000000Z",
                "updated_at": "2023-06-03T15:17:01.000000Z",
                "clinic": null,
                "role": {
                    "id": 1,
                    "role_name": "Customer",
                    "created_at": "2023-06-03T15:17:01.000000Z",
                    "updated_at": "2023-06-03T15:17:01.000000Z"
                },
                "coordinates": {
                    "lat": -6.312107,
                    "long": 106.6972741
                }
            }
        */

    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        if(auth()->user() == null){
            return response()->json(['error'=>'Please login first!'], Response::HTTP_UNAUTHORIZED);
        }
        $user_id = auth()->user()->id;
        $user = User::where('id','=',$user_id)->first();

        if(is_null($user)) {
            return response()->json(['error'=>'FAILED TO SUBMIT DATA'], Response::HTTP_BAD_REQUEST);
        }

        // Compare old password with hashed password in database
        if(!Hash::check($request->old_password, $user->password)){
            return response()->json([
                'error'=> 'Wrong old password',
                'message'=>'FAILED TO SUBMIT DATA',
            ], Response::HTTP_BAD_REQUEST);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json([
            'message' => 'SUCCESS SUBMIT DATA',
            'user' => $user
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $required_field = 'username';

        if($user->isStaff()){
            $required_field = 'clinic_name';
        }
        $validator = Validator::make($request->all(), [
            $required_field => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'address' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        if($user->isStaff()) {
            $user->clinic->clinic_name = $request->clinic_name;
            $user->clinic->clinic_address = $request->address;
            $user->username = $request->clinic_name;
        }else if($user->isCustomer()){
            $user->username = $request->username;
        }
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        $user->push();
        return response()->json([
            'message' => 'SUCCESS SUBMIT DATA',
            'user' => $user
        ], 201);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
