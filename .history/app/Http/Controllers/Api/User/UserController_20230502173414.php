<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\responseTrait;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTraits;
use App\Models\Hall;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\Providers\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{

/*     public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    } */




    use GeneralTraits;

    use responseTrait;
    public function gethall($hall_id) {
        $hall=Hall::find($hall_id);
        if($hall){
            return $this->response($this->hallResources($hall),"a hall Data",201);
        }
        return $this->response('',"this hall_id not found",401);
    }



    public function getAllHalls(){
        $halls=Hall::where('verified', 'confirmed')->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }






}
