<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    //  * Instantiate a new VerificationController instance.
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }


    public function notice(Request $request)
    {
        // Check if the user is authenticated and has verified their email
        return $request->user()->hasVerifiedEmail() 
        ? redirect()->route('home') : response()->json(['message' => 'Please verify your email']);
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('home');
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message'=> 'A fresh verification link has been sent to your email address.']);
    }

}