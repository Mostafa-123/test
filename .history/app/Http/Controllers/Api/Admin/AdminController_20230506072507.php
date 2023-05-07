<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\adminResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ownerResource;
use App\Http\Resources\personResource;
use App\Http\Resources\plannersResource;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Http\responseTrait;
use App\Http\Traits\GeneralTraits;
use App\Models\Hall;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Planner;
use App\Models\User;

class AdminController extends Controller
{
    use responseTrait;
    use GeneralTraits;
    public function confirmHallRequest($hall_id)
    {
        $hallreq = Hall::findOrFail($hall_id);

        $hallreq->verified = 'confirmed';
        $hallreq->save();

        return response()->json([
            'message' => 'Hall confirmed successfully',
            'data' => $this->hallResources($hallreq)
        ], 200);    }



    public function rejectHallRequest($hall_id)
    {
        $$hallreq = hallResource::findOrFail($hall_id);

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'data' => $$hallreq
        ], 200);
    }
    public function addUser(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:5|max:20',
            'national_id' => 'required|string|max:100',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'country' => $request->country?$request->country:null,
            'religion' => $request->religion?$request->religion:null,
            'gender' => $request->gender?$request->gender:null,
            'national_id' => $request->national_id?$request->national_id:null,
            'photo' => $request->photo?$this->uploadFile($request,'usersImages','photo'):null,
            ]
        ));
        return response()->json([
            'message' => 'User successfully added',
            'user' => new personResource($user),
        ], 201);
    }
    public function addPlanner(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:5|max:25',
            'national_id' => 'required|string|max:100',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        //$x = $request->only(['email', 'password']);
        $planner = Planner::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'country' => $request->country?$request->country:null,
            'religion' => $request->religion?$request->religion:null,
            'gender' => $request->gender?$request->gender:null,
            'photo' => $request->photo?$this->uploadFile($request,'plannersImages','photo'):null,
            ]
        ));
        return response()->json([
            'message' => 'planner successfully added',
            'planner' => new plannersResource($planner),
        ], 201);
    }
    public function addOwner(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners',
            'password' => 'required|string|min:6',
            'country' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
            'gender' => 'required|string|max:100',
            'phone' => 'required|string|min:5|max:25',
            'national_id' => 'required|string|max:100',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $owner = Owner::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'country' => $request->country?$request->country:null,
            'religion' => $request->religion?$request->religion:null,
            'gender' => $request->gender?$request->gender:null,
            'photo' => $request->photo?$this->uploadFile($request,'ownerImages','photo'):null,
            ]
        ));
        return response()->json([
            'message' => 'owner successfully added',
            'owner' => new ownerResource($owner),
        ], 201);
    }
    public function addAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $admin = Admin::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'photo' => $request->photo?$this->uploadFile($request,'adminImages','photo'):null,
            ]
        ));
        return response()->json([
            'message' => 'admin successfully registered',
            'admin' => new adminResource($admin),
        ], 201);
    }

    public function getAllUsers(){
        $users=User::get();
        if($users){
                foreach($users as $user){
                    $data[]=new personResource($user);
            }
            return $this->response($data,"users returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function getUserCount(){
        $users=count(User::get());
        return $users;
    }

    public function getOwnersCount(){
        $owners=count(Owner::get());
        return $owners;
    }

    public function getPlannersCount(){
        $planners=count(Planner::get());
        return $planners;
    }
    public function getAllMembersCount(){
        $planners=count(Planner::get());
        $users=count(User::get());
        $owners=count(Owner::get());
        $admins=count(Admin::get());
        $totalCount=$users+$admins+$owners+$planners;
        return $totalCount;
    }

    public function getAdminsCount(){
        $admins=count(Admin::get());
        return $admins;
    }
    public function getAllPlanners(){
        $planners=Planner::get();
        if($planners){
                foreach($planners as $planner){
                    $data[]=new plannersResource($planner);
            }
            return $this->response($data,"planners returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getAllOwners(){
        $owners=Owner::get();
        if($owners){
                foreach($owners as $owner){
                    $data[]=new ownerResource($owner);
            }
            return $this->response($data,"owners returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getAllAdmins(){
        $admins=Admin::get();
        if($admins){
                foreach($admins as $admin){
                    $data[]=new adminResource($admin);
            }
            return $this->response($data,"admins returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function deleteUser($user_id){
        $user=User::find($user_id);

        if(!$user){
            return $this->response(null,'The user  Not Found',404);
        }else if ($user){
            $photo=$user->photo;
            if($photo){
                    $this->deleteFile($photo);

                }
            $user->delete();
            return $this->response('','The user  deleted',200);
            }
    }

    public function deleteAdmin($admin_id){
        $admin=Admin::find($admin_id);

        if(!$admin){
            return $this->response(null,'The admin  Not Found',404);
        }else if ($admin){
            $photo=$admin->photo;
            if($photo){
                    $this->deleteFile($photo);
                }
            $admin->delete();
            return $this->response('','The admin  deleted',200);
            }
    }

    public function deletePlanner($planner_id){
        $planner=Planner::find($planner_id);

        if(!$planner){
            return $this->response(null,'The planner  Not Found',404);
        }else if ($planner){
            $photo=$planner->photo;
            if($photo){
                    $this->deleteFile($photo);
                }
            $planner->delete();
            return $this->response('','The planner  deleted',200);
            }
    }

    public function deleteOwner($owner_id){
        $owner=Owner::find($owner_id);

        if(!$owner){
            return $this->response(null,'The owner  Not Found',404);
        }else if ($owner){
            $photo=$owner->photo;
            if($photo){
                    $this->deleteFile($photo);
                }
            $owner->delete();
            return $this->response('','The owner  deleted',200);
            }
    }

    public function destroyHall($id){

        $result=Hall::find($id);

        if(!$result){
            return $this->response(null,'The hall request Not Found',404);
        }else if ($result){
            $photos=$result->photos;
            if($photos){
                for($i=0;$i<count($photos);$i++) {
                    $path=$photos[$i]->photoname;
                    $this->deleteFile($path);
                    }
                }
                $videos=$result->videos;
            if($videos){
                for($i=0;$i<count($videos);$i++) {
                    $path=$videos[$i]->videoname;
                    $this->deleteFile($path);
                    }
                }
            $result->delete();
            return $this->response('','The hall request deleted',200);
            }
    }

    public function deletePlan($plan_id){
        $plan=Plan::find($plan_id);
        if($plan){
            $photos=$plan->planPhotos;
            if($photos){
                for($i=0;$i<count($photos);$i++) {
                    $path=$photos[$i]->photoname;
                    $this->deleteFile($path);
                    }
                }
            $plan->delete();
            return $this->response('','plan deleted successfully',201);
        }
        return $this->response('', 'this plan_id not found',401);
    }

    public function getplan($plan_id) {
        $plan=Plan::find($plan_id);
        if($plan){
            return $this->response($this->planResources($plan),"a plan Data",201);
        }
        return $this->response('',"this plan_id not found",401);
    }

    public function gethall($hall_id) {
        $hall=Hall::find($hall_id);
        if($hall){
            return $this->response($this->hallResources($hall),"a hall Data",201);
        }
        return $this->response('',"this hall_id not found",401);
    }
    public function getConfirmedHalls(){
        $halls=Hall::where('verified', 'confirmed')->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function getUnConfirmedHalls(){
        $halls=Hall::where('verified', 'unconfirmed')->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getCanceledHalls(){
        $halls=Hall::where('verified', 'cancelled')->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getAllHalls(){
        $halls=Hall::with(['comments','likes'])->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    }
