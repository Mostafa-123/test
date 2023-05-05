<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\adminResource;
use App\Http\responseTrait;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTraits;
use App\Models\Admin;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\Providers\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{






    use GeneralTraits;
    use responseTrait;
    public function registerAdmin(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'photo',


        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $admin = Admin::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'photo' => $this->uploadFile($request,'adminImages','photo'),
            ]
        ));
        return response()->json([
            'message' => 'admin successfully registered',
            'admin' => new adminResource($admin),
        ], 201);
    }

    public function getAdminPhoto($admin_id){
        $admin=Admin::find($admin_id);
        if($admin){
            if($admin->photo){
                return $this->getFile($admin->photo);
            }
            return $this->response("", "This admin doesn't has photo",404);
        }
        return $this->response( "", 'this admin_id not found',401);
    }

    public function adminProfile() {
        $admin=Auth::guard('admin-api')->user();
        return response()->json(new adminResource($admin));
    }

    public function updateAdmin(Request $request, $admin_id)
    {
        $admin = Admin::find($admin_id);
        if ($admin) {
            $photo = $request->photo;
            if ($photo && $admin->photo) {
                $this->deleteFile($admin->photo);
                $photo = $this->uploadFile($request, 'adminsImages', 'photo');
            } elseif ($photo != null && $admin->photo == null) {
                $photo = $this->uploadFile($request, 'adminsImages', 'photo');
            } else {
                $photo = $admin->photo;
            }
            $newData = [
                'name' => $request->name?$request->name:$admin->name,
                'password' => $request->password?$request->password:$admin->password,
                'photo' => $photo,
            ];
            $admin->update($newData);
        }
        return $this->response(new adminResource($admin), 'admin updated successfully', 201);
    }

    public function adminLogin(Request $request){


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

            $token = Auth::guard('admin-api')->attempt($credentials);

            if (!$token){
                return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
            }
        $admin = Auth::guard('admin-api')->user();
        $admin->api_token = $token;
        //return token
        return $this->returnData('admin',new adminResource($admin),"data have returned");


        }

            catch (\Exception $ex) {
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }


    }

    public function adminLogout(Request $request)
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
