<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 400);
        }
        
        

        $newUser = new User();
        $newUser->email = $request['email'];
        $newUser->password = bcrypt($request['password']);
        if($request['profile_picture']){
            $profile_picture = $request['profile_picture'];
            $path = $profile_picture->store('profile_pictures', 'public');
            $profile_picture = $path;
        }
        $newUser->save();

        return response()->json(['message' => 'User registered successfully'], 201);
    }
}