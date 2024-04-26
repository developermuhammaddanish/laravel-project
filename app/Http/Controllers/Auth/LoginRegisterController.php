<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class LoginRegisterController extends Controller
{
    //  * Instantiate a new LoginRegisterController instance.
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'home']);
        $this->middleware('auth')->only('logout', 'home');
        $this->middleware('verified')->only('home');
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'email' => 'required|string|email:rfc,dns|max:250|unique:users,email',
            'password' => 'required|string|min:8'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    
        event(new Registered($user));

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        return redirect()->route('verification.notice');
    }

 
      //Login User
      public function login(Request $request)
      {
        // dd($request->all());
          $validator = Validator::make($request->all(), [
              "email"=> "required|email",
              "password"=> "required",
          ]);
  
          if ($validator->fails()) 
          {
              return response()->json($validator->errors(),401);
          }
          else
          {
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
      
              return response()->json($response, 200);
          }
      }



    public function logout(Request $request)
    {
        // dd($request->all());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message'=>'You have logged out successfully!']); 
    }    

    public function dashboard()
        {
            return response()->json(['message'=> 'Welcome to dashboard'],200);
        }

   
}