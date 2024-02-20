<?php

namespace App;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function task_mappings(){
        return $this->hasMany(TaskMapping::class,'id','user_id');
    }
    public function delete_user_account(Request $request){
        $user = Auth::user();
        if(!isset($user)){
            $response = ["message" =>'User hasn\'t logged in'];
            return response($response, 401);
        }
        $user = $user->id;
        self::where('id',$user)
                ->delete();
        $response = ["message" =>'User has been deleted'];
        return response($response, 200);
    }
}
