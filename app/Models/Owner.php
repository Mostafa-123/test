<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\classes\hallsClasses;
use App\Http\Resources\hallResource;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Models\Hall;

use App\Models\Admin;


class Owner extends  Authenticatable  implements JWTSubject
{
    use HasFactory;

    protected $fillable = [

        'name',
        'email',
        'password',
        'country',
        'religion',
        'gender',
        'phone',
        'photo',
/*         'hall_id', */




    ];





    function hall() {

        return $this->hasMany(Hall::class);

    }


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

}
