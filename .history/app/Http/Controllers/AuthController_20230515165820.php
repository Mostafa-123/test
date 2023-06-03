<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\Planner\supservicecontroller;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTraits;
use App\Http\Resources\adminResource;
use App\Http\Resources\ownerResource;
use App\Http\Resources\plannersResource;
use App\Http\Resources\personResource;
use App\Http\Resources\SupplierResource;
use App\Http\responseTrait;
use App\Models\Admin;
use App\Models\User;
use App\Models\Planner;
use App\Models\Owner;
use App\Models\Supplier;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{

    use GeneralTraits;

    use responseTrait;





    public function switchLogin(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email',
                'password' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $credentials = $request->only(['email', 'password']);

            if (Auth::guard('admin-api')->attempt($credentials)) {
                // User authenticated with admin guard
                return $this->loginAdmin($credentials);
            } elseif (Auth::guard('user-api')->attempt($credentials)) {
                // User authenticated with user guard
                return $this->loginUser($credentials);
            } elseif (Auth::guard('planner-api')->attempt($credentials)) {
                // User authenticated with planner guard
                return $this->loginPlanner($credentials);
            } elseif (Auth::guard('owner-api')->attempt($credentials)) {
                // User authenticated with owner guard
                return $this->loginOwner($credentials);
            }elseif (Auth::guard('supplier-api')->attempt($credentials)) {
                // User authenticated with owner guard
                return $this->loginSupplier($credentials);
            } else {
                // User dose not exit
                return response()->json([
                    'message' => 'Email or Password Doesn`t Exist',

                ], 201);
            }
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function switchRegister(Request $request)
    {
        try {
            // $rules = [
            //     'name' => 'required|string|between:2,100',
            //     'email' => 'required|string|email|max:100|unique:users',
            //     'password' => 'required|string|min:6',


            // ];
            // $validator = Validator::make($request->all(), $rules);
            // if ($validator->fails()) {
            //     $code = $this->returnCodeAccordingToInput($validator);
            //     return $this->returnValidationError($code, $validator);
            // }
            $role = $request->role;

            switch ($role) {

                case 'user':
                    return $this->registerUser($request);
                    break;
                case 'planner':
                    return $this->registerPlanner($request);
                    break;
                case 'hallowner':
                    return $this->registerOwner($request);
                    break;
                    case 'supplier':
                        return $this->registerSupplier($request);
                        break;
                default:
                    return response()->json([
                        'message' => 'ERORR',

                    ], 201);;
                    break;
            }
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }



    public function loginAdmin($credentials)
    {
        $token = Auth::guard('admin-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $admin = Auth::guard('admin-api')->user();
        $admin->api_token = $token;
        //return token
        return $this->returnData('data',new adminResource($admin), "data have returned");
    }
    public function loginUser($credentials)
    {
        $token = Auth::guard('user-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $user = Auth::guard('user-api')->user();
        $user->api_token = $token;
        //return token
        return $this->returnData('data',new personResource($user), "data have returned");
    }
    public function loginOwner($credentials)
    {
        $token = Auth::guard('owner-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $owner = Auth::guard('owner-api')->user();
        $owner->api_token = $token;
        //return token
        return $this->returnData('data', new ownerResource($owner), "data have returned");
    }
    public function loginPlanner($credentials)
    {
        $token = Auth::guard('planner-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $planner = Auth::guard('planner-api')->user();
        $planner->api_token = $token;
        //return token
        return $this->returnData('data',new plannersResource($planner), "data have returned");
    }
    public function loginSupplier($credentials)
    {
        $token = Auth::guard('supplier-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $supplier = Auth::guard('supplier-api')->user();
        $supplier->api_token = $token;
        //return token
        return $this->returnData('data',new SupplierResource($supplier), "data have returned");
    }














    public function registerAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners|unique:suppliers',
            'password' => 'required|string|min:6',
            'photo',


        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $admin = Admin::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'photo' => $this->uploadFile($request, 'adminsImages', 'photo'),
            ]
        ));



        return response()->json([
            'message' => 'admin successfully registered',
            'data' => new adminResource($admin),
        ], 201);
    }
    public function registerOwner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners|unique:suppliers',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:25',
            'photo',


        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $owner = Owner::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'photo' => $this->uploadFile($request, 'ownersImages', 'photo'),
            ]
        ));

        $credentials = $request->only(['email', 'password']);
        $token = Auth::guard('owner-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $ownerToken = Auth::guard('owner-api')->user();
        $ownerToken->api_token = $token;
        return response()->json([
            'message' => 'owner successfully registered',
            'data' => new ownerResource($ownerToken),
        ], 201);
    }
    public function registerPlanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners|unique:suppliers',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:20',
            'photo'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        //$x = $request->only(['email', 'password']);
        $planner = Planner::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'photo' => $this->uploadFile($request, 'plannersImages', 'photo'),
            ]
        ));

        $credentials = $request->only(['email', 'password']);
        $token = Auth::guard('planner-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $plannerToken = Auth::guard('planner-api')->user();
        $plannerToken->api_token = $token;

        return response()->json([
            'message' => 'planner successfully registered',
            'data' => new plannersResource($plannerToken),
        ], 201);
    }
    public function registerSupplier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners|unique:suppliers',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:20',
            'photo'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        //$x = $request->only(['email', 'password']);
        $supplier = Supplier::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'photo' => $this->uploadFile($request, 'suppliersImages', 'photo'),
            ]
        ));

        $credentials = $request->only(['email', 'password']);
        $token = Auth::guard('supplier-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $supplierToken = Auth::guard('supplier-api')->user();
        $supplierToken->api_token = $token;

        return response()->json([
            'message' => 'supplier successfully registered',
            'data' => new SupplierResource($supplierToken),
        ], 201);
    }
    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners|unique:suppliers',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:25',
            'photo',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'photo' => $this->uploadFile($request, 'usersImages', 'photo'),
            ]
        ));

        $credentials = $request->only(['email', 'password']);
        $token = Auth::guard('user-api')->attempt($credentials);
        if (!$token) {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
        $userToken = Auth::guard('user-api')->user();
        $userToken->api_token = $token;

        return response()->json([
            'message' => 'User successfully registered',
            'data' => new personResource($userToken),
        ], 201);
    }





    public function getAdminPhoto($admin_id)
    {
        $admin = Admin::find($admin_id);
        if ($admin) {
            if ($admin->photo) {
                return $this->getFile($admin->photo);
            }
            return $this->response("", "This admin doesn't has photo", 404);
        }
        return $this->response("", 'this admin_id not found', 401);
    }
    public function getOwnerPhoto($owner_id)
    {
        $owner = Owner::find($owner_id);
        if ($owner) {
            if ($owner->photo) {
                return $this->getFile($owner->photo);
            }
            return $this->response("", "This owner doesn't has photo", 404);
        }
        return $this->response("", 'this owner_id not found', 401);
    }
    public function getPlannerPhoto($planner_id)
    {
        $planner = Planner::find($planner_id);
        if ($planner) {
            if ($planner->photo) {
                return $this->getFile($planner->photo);
            }
            return $this->response("", "This planner doesn't has photo", 404);
        }
        return $this->response("", 'this Planner_id not found', 401);
    }
    public function getUserPhoto($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            if ($user->photo) {
                return $this->getFile($user->photo);
            }
            return $this->response("", "This user doesn't has photo", 404);
        }
        return $this->response("", 'this user_id not found', 401);
    }
    public function getSupplierPhoto($supplier_id)
    {
        $supplier = Supplier::find($supplier_id);
        if ($supplier) {
            if ($supplier->photo) {
                return $this->getFile($supplier->photo);
            }
            return $this->response("", "This supplier doesn't has photo", 404);
        }
        return $this->response("", 'this supplier_id not found', 401);
    }












    public function adminProfile()
    {
        $admin = Auth::guard('admin-api')->user();
        return response()->json(new adminResource($admin));
    }
    public function ownerProfile()
    {
        $owner = Auth::guard('owner-api')->user();
        return response()->json(new ownerResource($owner));
    }
    public function plannerProfile()
    {
        $planner = Auth::guard('planner-api')->user();
        return response()->json(new plannersResource($planner));
    }
    public function supplierProfile()
    {
        $supplier = Auth::guard('supplier-api')->user();
        return response()->json(new SupplierResource($supplier));
    }
    public function userProfile()
    {
        $user = Auth::guard('user-api')->user();
        return response()->json(new personResource($user));
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
                'name' => $request->name ? $request->name : $admin->name,
                'password' => $request->password ? bcrypt($request->password) : $admin->password,
                'photo' => $photo,
            ];
            $admin->update($newData);
        }
        return $this->response(new adminResource($admin), 'admin updated successfully', 201);
    }
    public function updateOwner(Request $request, $owner_id)
    {
        $owner = Owner::find($owner_id);
        if ($owner) {
            $photo = $request->photo;
            if ($photo && $owner->photo) {
                $this->deleteFile($owner->photo);
                $photo = $this->uploadFile($request, 'ownersImages', 'photo');
            } elseif ($photo != null && $owner->photo == null) {
                $photo = $this->uploadFile($request, 'ownersImages', 'photo');
            } else {
                $photo = $owner->photo;
            }
            $newData = [
                'name' => $request->name ? $request->name : $owner->name,
                'password' => $request->password ? bcrypt($request->password) : $owner->password,
                'country' => $request->country ? $request->country : $owner->country,
                'religion' => $request->religion ? $request->religion : $owner->religion,
                'gender' => $request->gender ? $request->gender : $owner->gender,
                'phone' => $request->phone ? $request->phone : $owner->phone,
                'photo' => $photo,
            ];
            $owner->update($newData);
        }
        return $this->response(new ownerResource($owner), 'owner updated successfully', 201);
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
                'name' => $request->name ? $request->name : $planner->name,
                'password' => $request->password ? bcrypt($request->password): $planner->password,
                'country' => $request->country ? $request->country : $planner->country,
                'religion' => $request->religion ? $request->religion : $planner->religion,
                'gender' => $request->gender ? $request->gender : $planner->gender,
                'phone' => $request->phone ? $request->phone : $planner->phone,
                'photo' => $photo,
            ];
            $planner->update($newData);
        }
        return $this->response(new plannersResource($planner), 'planner updated successfully', 201);
    }
    public function updateSupplier(Request $request, $supplier_id)
    {
        $supplier = Supplier::find($supplier_id);
        if ($supplier) {
            $photo = $request->photo;
            if ($photo && $supplier->photo) {
                $this->deleteFile($supplier->photo);
                $photo = $this->uploadFile($request, 'suppliersImages', 'photo');
            } elseif ($photo != null && $supplier->photo == null) {
                $photo = $this->uploadFile($request, 'suppliersImages', 'photo');
            } else {
                $photo = $supplier->photo;
            }
            $newData = [
                'name' => $request->name ? $request->name : $supplier->name,
                'password' => $request->password ? bcrypt($request->password): $supplier->password,
                'country' => $request->country ? $request->country : $supplier->country,
                'religion' => $request->religion ? $request->religion : $supplier->religion,
                'gender' => $request->gender ? $request->gender : $supplier->gender,
                'phone' => $request->phone ? $request->phone : $supplier->phone,
                'photo' => $photo,
            ];
            $supplier->update($newData);
        }
        return $this->response(new SupplierResource($supplier), 'supplier updated successfully', 201);
    }
    public function updateUser(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $photo = $request->photo;
            if ($photo && $user->photo) {
                $this->deleteFile($user->photo);
                $photo = $this->uploadFile($request, 'usersImages', 'photo');
            } elseif ($photo != null && $user->photo == null) {
                $photo = $this->uploadFile($request, 'usersImages', 'photo');
            } else {
                $photo = $user->photo;
            }
            $newData = [
                'name' => $request->name ? $request->name : $user->name,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'country' => $request->country ? $request->country : $user->country,
                'religion' => $request->religion ? $request->religion : $user->religion,
                'gender' => $request->gender ? $request->gender : $user->gender,
                'phone' => $request->phone ? $request->phone : $user->phone,
                'photo' => $photo,
            ];
            $user->update($newData);
        }
        return $this->response(new personResource($user), 'user updated successfully', 201);
    }










    public function logout(Request $request)
    {
        $token = $request -> header('auth-token');
        if($token){
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            }catch (TokenInvalidException $e){
                return  $this -> returnError('','some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        }else{
            $this -> returnError('','some thing went wrongs');
        }

    }

    public function adminLogout(Request $request)
    {
        $token = $request->header('auth-token');
        if ($token) {
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return  $this->returnError('', 'some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        } else {
            $this->returnError('', 'some thing went wrongs');
        }
    }
    public function logoutOwner(Request $request)
    {
        $token = $request->header('auth-token');
        if ($token) {
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return  $this->returnError('', 'some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        } else {
            $this->returnError('', 'some thing went wrongs');
        }
    }
    public function logoutPlanner(Request $request)
    {
        $token = $request->header('auth-token');
        if ($token) {
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return  $this->returnError('', 'some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        } else {
            $this->returnError('', 'some thing went wrongs');
        }
    }
    public function logoutSupplier(Request $request)
    {
        $token = $request->header('auth-token');
        if ($token) {
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return  $this->returnError('', 'some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        } else {
            $this->returnError('', 'some thing went wrongs');
        }
    }
    public function logoutUser(Request $request)
    {
        $token = $request->header('auth-token');
        if ($token) {
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return  $this->returnError('', 'some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
        } else {
            $this->returnError('', 'some thing went wrongs');
        }
    }
}
