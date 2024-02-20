<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    public function resetPassword(Request $request){
        // $request->validate($this->rules(), $this->validationErrorMessages());

        // Check if the user with the given email exists
        $user = User::where('email', $request['email'])->first();
        

        if (!$user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        // Generate a password reset token and send reset password email

        $token = $user->createToken('Login')->accessToken;
        $user->password = bcrypt($request['password']);

        // $user->sendPasswordResetNotification($token);
        $response = ['token' => $token];
        $response = ['message' => 'Password reset link sent to your email'];
        
        return response($response, 200);
    }
}
