<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8|confirmed'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->make(response()->json($validator->errors(), 422)->getContent(), 422, ['Content-Type' => 'application/json']);
        }

        //create user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        //return response JSON user is created
        if($user) {
            return response()->make(
                response()->json([
                    'success' => true,
                    'user'    => $user,  
                ], 201)->getContent(),
                201,
                ['Content-Type' => 'application/json']
            );
        }

        //return JSON process insert failed 
        return response()->make(
            response()->json([
                'success' => false,
            ], 409)->getContent(),
            409,
            ['Content-Type' => 'application/json']
        );
    }
}
