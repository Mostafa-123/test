<?php

namespace App\Models;

use App\Http\Resources\PlanResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\plan;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;



class Planner extends  Authenticatable  implements JWTSubject
{
    use HasFactory;


    protected $fillable = [

        'name',
        'email',
        'password',
        'plan_id',
        'country',
        'religion',
        'gender',
        'phone',
        'photo',
/*         'hall_id', */




    ];




    function plan() {

        return $this->hasMany(Plan::class);

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
