<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Varification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $validatedData = $validator->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = User::create($validatedData);
    
            // Generate OTP
            $otp = rand(1000,9999);
            
            // Token
            $token = $user->createToken('OTP',['verify-otp'])->plainTextToken;
            
            // Create Verification record
            $verification = Varification::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'token'=> $token
            ]);
            
            // Send Verification Email
            Mail::to($user->email)->send(new VerifyEmail($verification));
            
    
            return response()->json(['message' => 'User registered successfully','data' => $user,'token' => $token], 200);
        }
    }

    public function verifyEmail(Request $request)
    {
        $id = $request->user()->id;
        $verification = Varification::where('user_id', $id)
                                     ->where('otp', $request->otp)
                                     ->first();
    
        if (!$verification) {
            return response()->json(['message' => 'Invalid OPT Please Try Again.'], 401);
        }
    
        $user = User::find($verification->user_id);
        $user->email_verified_at = now();
        $user->save();        


        return response()->json(['message' => 'Email verified successfully'], 200);
    }
    
     //Login User
     public function login(Request $request)
     {
         $validator = Validator::make($request->all(), [
             "email"=> "required|string|email",
             "password"=> "required",
         ]);
 
         if ($validator->fails()) {
             return $validator->errors();
         }
         else
         {
             $user = User::where("email", $request->email)->first();
             if(!$user || !Hash::check($request->password, $user->password)){
                 return response()->json([
                    'message'=> 'These credentials are invalide please try again.'
                ],404);
            }
            else
            {    
                if(isset($user->email_verified_at) ){
                    $token = $user->createToken('login')->plainTextToken;
     
                    $response = [
                        'user'=> $user,
                        'token'=> $token 
                    ];
     
                    return response()->json(['message' => 'User Login successfully','user' => $user,'token' => $token], 200);
                }
                return response()->json(["message"=>"Please Verify Your Email First Before Login"] ,401);
            }
        }
    }

     //logut
     public function logout(Request $request)
     {
         $request->user()->tokens()->delete();
         return response()->json(['message' => 'Logout Successfully']);
     }
     
}
