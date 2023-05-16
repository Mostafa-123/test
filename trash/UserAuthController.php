<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\personResource;
use App\Http\responseTrait;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTraits;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\Providers\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UserAuthController extends Controller
{

/*     public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    } */




    use GeneralTraits;

    use responseTrait;
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

            $token = Auth::guard('user-api')->attempt($credentials);

            if (!$token){
                return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
            }
        $user = Auth::guard('user-api')->user();
        $user->api_token = $token;
        //return token
        return $this->returnData('user',new personResource($user),"data have returned");




        }



            catch (\Exception $ex) {
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }


    }

    public function getUserPhoto($user_id){
        $user=User::find($user_id);
        if($user){
            if($user->photo){
                return $this->getFile($user->photo);
            }
            return $this->response("", "This user doesn't has photo",404);
        }
        return $this->response( "", 'this user_id not found',401);
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

    public function userProfile() {
        $user=Auth::guard('user-api')->user();
        return response()->json(new personResource($user));
    }

    public function updateUser(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $photo = $request->photo;
            if ($photo && $user->photo) {
                $this->deleteFile($user->photo);
                $photo = $this->uploadFile($request, 'userImages', 'photo');
            } elseif ($photo != null && $user->photo == null) {
                $photo = $this->uploadFile($request, 'userImages', 'photo');
            } else {
                $photo = $user->photo;
            }
            $newData = [
                'name' => $request->name?$request->name:$user->name,
                'password' => $request->password?$request->password:$user->password,
                'country' => $request->country?$request->country:$user->country,
                'religion' => $request->religion?$request->religion:$user->religion,
                'gender' => $request->gender?$request->gender:$user->gender,
                'phone' => $request->phone?$request->phone:$user->phone,
                'national_id' => $request->national_id?$request->national_id:$user->national_id,
                'photo' => $photo,
            ];
            $user->update($newData);
        }
        return $this->response(new personResource($user), 'user updated successfully', 201);
    }

    public function userRegister(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:11|max:11',
            'national_id' => 'required|string|max:100',
            'role' => 'required|string|max:100',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'photo' => $this->uploadFile($request,'usersImages','photo'),
            ]
        ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => new personResource($user),
        ], 201);
    }





}
