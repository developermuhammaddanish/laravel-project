<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    //Register User
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } 
        else 
        {
            $validatedData = $validator->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = User::create($validatedData);
            return response()->json(['message' => 'User registered successfully'], 200);
        }
    }
    

    //Login User
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email"=> "required|email",
            "password"=> "required",
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        else{
            $user = User::where("email", $request->email)->first();
            if(!$user || !Hash::check($request->password, $user->password)){
                return response()->json([
                    "message"=> ['These credentials are invalide please try again.']
                ],404);
            }
    
            $token = $user->createToken('my-app-token')->plainTextToken;
    
            $response = [
                'user'=> $user,
                'token'=> $token 
            ];
    
            return response()->json($response, 201);
        }
    }

    //logut
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

}
