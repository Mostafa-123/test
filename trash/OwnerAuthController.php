<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\ownerResource;
use App\Http\responseTrait;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTraits;
use App\Models\Owner;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\Providers\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class OwnerAuthController extends Controller
{






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

            $token = Auth::guard('owner-api')->attempt($credentials);

            if (!$token){
                return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
            }
        $owner = Auth::guard('owner-api')->user();
        $owner->api_token = $token;
        //return token
        return $this->returnData('owner', new ownerResource($owner),"data have returned");


        }

            catch (\Exception $ex) {
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }


    }



    public function logout(Request $request){
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
    public function ownerProfile() {
        $owner=Auth::guard('owner-api')->user();
        return response()->json(new ownerResource($owner));
    }
    public function registerOwner(Request $request) {
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
        $owner = Owner::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'photo' => $this->uploadFile($request,'ownerImages','photo'),
            ]
        ));
        return response()->json([
            'message' => 'owner successfully registered',
            'owner' => new ownerResource($owner),
        ], 201);
    }
    public function updateOwner(Request $request, $owner_id){
        $owner = Owner::find($owner_id);
        if ($owner) {
            $photo = $request->photo;
            if ($photo && $owner->photo) {
                $this->deleteFile($owner->photo);
                $photo = $this->uploadFile($request, 'ownerImages', 'photo');
            } elseif ($photo != null && $owner->photo == null) {
                $photo = $this->uploadFile($request, 'ownerImages', 'photo');
            } else {
                $photo = $owner->photo;
            }
            $newData = [
                'name' => $request->name?$request->name:$owner->name,
                'password' => $request->password?$request->password:$owner->password,
                'country' => $request->country?$request->country:$owner->country,
                'religion' => $request->religion?$request->religion:$owner->religion,
                'gender' => $request->gender?$request->gender:$owner->gender,
                'phone' => $request->phone?$request->phone:$owner->phone,
                'photo' => $photo,
            ];
            $owner->update($newData);
        }
        return $this->response(new ownerResource($owner), 'owner updated successfully', 201);
    }
    public function getOwnerPhoto($owner_id){
        $owner=Owner::find($owner_id);
        if($owner){
            if($owner->photo){
                return $this->getFile($owner->photo);
            }
            return $this->response("", "This Patient doesn't has photo",404);
        }
        return $this->response( "", 'this Pateint_id not found',401);
    }







}
