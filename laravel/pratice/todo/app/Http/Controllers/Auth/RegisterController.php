<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\UserTaskAnalytic;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

     protected function validator(array $data)
     {
         return Validator::make($data, [
             'name' => ['required', 'string', 'max:255'],
             'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
             'password' => ['required', 'string', 'min:8', 'confirmed'],
             "profile" => ["nullable","max:5000","mimes:jpeg,jpg,png"],
         ]);
     }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    
    
     protected function create(array $data)
    {
        if($data['profile_picture'] != null){
            $filenamewithext = $data['profile_picture']->getClientOriginalName();
            $filename = pathinfo($filenamewithext, PATHINFO_FILENAME);
            $ext = pathinfo($filenamewithext, PATHINFO_EXTENSION);
            $filenametostore = $filename."_".time().".".$ext;
            $path = $data['profile_picture']->storeAs("public/profile", $filenametostore);

        }
        else{
            $filenametostore = 'user.jpg';
        }
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile_picture' => $filenametostore,
        ]);
    }


    public function registerPassport(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);
        }
        
        

        $newUser = new User();
        $newUser->name = $request['name'];
        $newUser->email = $request['email'];
        $newUser->password = bcrypt($request['password']);

        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture');
            $path = $profile_picture->store('profile_pictures', 'public');
            $profile_picture_path = $path;
            $newUser->profile_picture = $profile_picture_path;
        }
        
        
        $newUser->save();
        $userID = $newUser->id;
        
        // creating the user analytics for the user
        $task_analytics = new UserTaskAnalytic();
        $task_analytics->user_id = $userID;
        $task_analytics->save();

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function uploadProfilePicture(Request $request){
        $user = Auth::user();

        $profile_picture = $request->file('profile_picture');
        $path = $profile_picture->store('profile_pictures', 'public');
        $profile_picture_path = $path;
        
        User::where('email', $user->email)
            ->update(['profile_picture' => $profile_picture_path]);
        return response()->json(['messgae' => 'User profile picture has been updated!'],200);
    }
}