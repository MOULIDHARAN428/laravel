<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        $updatePassword = DB::table('password_resets')
                            ->where(['email' => $request->email, 'token' => $request->token])
                            ->first();
        
        if(!$updatePassword){
            return "invalid token";
        }

        User::where('email', $request->email)
            ->update(['password' => bcrypt($request->password)]);
        
        DB::table('password_resets')->where(['email'=> $request->email])->delete();
        
        return "Resetted the password successfully!";
    }
}
