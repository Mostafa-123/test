<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Booking;
use App\Models\User;
use App\Models\Hall;

class Admin extends Authenticatable  implements JWTSubject
{

    protected $table = 'admins';


    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name', 'email','password','photo','created_at', 'updated_at'
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    function bookings() {

        return $this->hasMany(Booking::class);

    }


    public function likes()
    {
        return $this->hasMany(like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}



//Omar
