<?php

namespace App;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
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
        'name', 'email', 'password', 'profile_picture',
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
    public function taskMappings(){
        return $this->hasMany(TaskMapping::class,'user_id','id');
    }
    public function userTaskAnalytics(){
        return $this->hasOne(UserTaskAnalytic::class,'user_id','id');
    }

    public function userTasks(){
        return $this->hasManyThrough(Task::class,TaskMapping::class,'user_id','id','id','task_id');
    }
   
    public static function deleteUserAccount(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
        self::where('id',$user_id)
                ->delete();
        $message ='User has been deleted';
        return $message;
    }

    public static function getUsersWithProfile(){
        $user = self::get(['id','name','profile_picture']);
        return $user;
    }
}
