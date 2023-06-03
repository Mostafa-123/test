<?php

namespace App\Http\Controllers\Api\Planner;

use App\Http\Controllers\Controller;
use App\Http\Resources\plannersResource;
use App\Http\Resources\PlanResource;
use App\Http\responseTrait;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTraits;
use App\Models\Planner;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\Providers\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class PlannerAuthController extends Controller
{

/*     public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    } */




    use GeneralTraits;
    use responseTrait;
    public function registerPlanner(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:11|max:11',
            'photo'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        //$x = $request->only(['email', 'password']);
        $planner = Planner::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'photo' => $this->uploadFile($request,'plannersImages','photo'),
            ]
        ));
        return response()->json([
            'message' => 'planner successfully registered',
            'planner' => new plannersResource($planner),
        ], 201);
    }
    public function getPlannerPhoto($planner_id){
        $planner=Planner::find($planner_id);
        if($planner){
            if($planner->photo){
                return $this->getFile($planner->photo);
            }
            return $this->response("", "This planner doesn't has photo",404);
        }
        return $this->response( "", 'this Planner_id not found',401);
    }

    public function login(Request $request){


        try {
            $rules = [
                'email' => 'required|email',
                'password' => 'required|string',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            //login

            $credentials = $request->only(['email', 'password']);

            $token = Auth::guard('planner-api')->attempt($credentials);

            if (!$token){
                return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
            }
        $planner = Auth::guard('planner-api')->user();
        $planner->api_token = $token;
        //return token
        return $this->returnData('planner',new plannersResource($planner),"data have returned");




        }



            catch (\Exception $ex) {
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }


    }

    public function plannerProfile() {
        $planner=Auth::guard('planner-api')->user();
        return response()->json(new plannersResource($planner));
    }

    public function updatePlanner(Request $request, $planner_id)
    {
        $planner = Planner::find($planner_id);
        if ($planner) {
            $photo = $request->photo;
            if ($photo && $planner->photo) {
                $this->deleteFile($planner->photo);
                $photo = $this->uploadFile($request, 'plannersImages', 'photo');
            } elseif ($photo != null && $planner->photo == null) {
                $photo = $this->uploadFile($request, 'plannersImages', 'photo');
            } else {
                $photo = $planner->photo;
            }
            $newData = [
                'name' => $request->name?$request->name:$planner->name,
                'password' => $request->password?$request->password:$planner->password,
                'country' => $request->country?$request->country:$planner->country,
                'religion' => $request->religion?$request->religion:$planner->religion,
                'gender' => $request->gender?$request->gender:$planner->gender,
                'phone' => $request->phone?$request->phone:$planner->phone,
                'photo' => $photo,
            ];
            $planner->update($newData);
        }
        return $this->response(new plannersResource($planner), 'planner updated successfully', 201);
    }
    public function logout(Request $request)
    {
        $token = $request -> header('auth-token');
        if($token){
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('','some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        }else{
            $this -> returnError('','some thing went wrongs');
        }

    }







}
