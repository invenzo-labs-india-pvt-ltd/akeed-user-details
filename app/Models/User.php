<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'mobile_number', 'user_type'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /* To check the login credential is valid or not */
    public static function getLoginDetails($user_type, $email, $mobile_number)
    {
        $checkLogin = User::select('id', 'first_name', 'last_name', 'email', 'mobile_number', 'password')
            ->where('user_type', $user_type);
        if (!empty($email)) {
            $checkLogin = $checkLogin->where('email', '=', $email);
        }
        if (!empty($mobile_number)) {
            $checkLogin = $checkLogin->where('mobile_number', '=', $mobile_number);
        }
        return $checkLogin->first();
    }
}
