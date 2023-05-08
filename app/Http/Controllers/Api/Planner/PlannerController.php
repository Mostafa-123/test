<?php

namespace App\Http\Controllers\Api\Planner;
use Illuminate\Http\Request;
use App\Http\Resources\PlanResource;

use App\Models\Plan;
use App\Models\PlanRequest;
use App\Models\PlanPhoto;
use App\Http\Traits\GeneralTraits;
use App\Http\Controllers\Controller;
use App\Http\responseTrait;
use App\Models\Planner;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class PlannerController extends Controller
{
    use GeneralTraits;

    use responseTrait;
    public function addPlan(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'price' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->response(null, $validator->errors(), 400);
        }

        try {
            $planner = Auth::guard('planner-api')->user();
            if (!$planner) {
                throw new JWTException('Invalid token');
            }
            $planner_id = $planner->id;
        } catch (JWTException $e) {
            // handle the exception, such as logging it or returning an error response to the client
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {
            // handle other exceptions
            // ...
        }

        $planner = Auth::guard('planner-api')->user();
        $planner_id = $planner->id;


        try {
            DB::beginTransaction();
            $result = Plan::create([
                'planner_id' =>$planner_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);
            if ($request->photos) {
                $request->photos[0];
                for ($i = 0; $i < count($request->photos); $i++) {
                    $path = $this->uploadMultiFile($request, $i, 'planPhotos', 'photos');
                    PlanPhoto::create([
                        'photoname' => $path,
                        'plan_id' => $result->id,
                    ]);
                }
            }
            DB::commit();
            if ($result) {
                return $this->response($this->planResources($result), 'done', 201);
            } else {
                return $this->response(null, 'plan is not saved', 405);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->response('', $e, 401);
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
    public function getPlanPhoto($plan_id,$photo_id){
        $plan=Plan::find($plan_id);
        if($plan){
            $photo=PlanPhoto::find($photo_id);
            if($photo){
                return $this->getFile($photo->photoname);
            }
            return $this->response('', "This plan doesn't has photo",401);
        }
        return $this->response('', 'this plan_id not found',401);
    }
    public function updatePlan(Request $request, $plan_id)
    {
        $plan = plan::find($plan_id);
        if ($plan) {
            try {
                DB::beginTransaction();
                $photos = $plan->planPhotos;
                if ($request->photos[0]) {
                    if ($photos) {
                        for ($i = 0; $i < count($photos); $i++) {
                            $path = $photos[$i]->photoname;
                            $photo = PlanPhoto::find($photos[$i]->id);
                            $photo->delete();
                            $this->deleteFile($path);
                        }
                        for ($i = 0; $i < count($request->photos); $i++) {
                            $path = $this->uploadMultiFile($request, $i, 'planPhotos', 'photos');
                            PlanPhoto::create([
                                'photoname' => $path,
                                'plan_id' => $plan->id,
                            ]);
                        }
                    } else if ($photos == null) {
                        for ($i = 0; $i < count($request->photos); $i++) {
                            $path = $this->uploadMultiFile($request, $i, 'planPhotos', 'photos');
                            PlanPhoto::create([
                                'photoname' => $path,
                                'plan_id' => $plan->id,
                            ]);
                        }
                    }
                }
                $newData = [
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price
                ];
                $plan->update($newData);
                DB::commit();
                $plann = plan::find($plan_id);
                return $this->response($this->planResources($plann), 'plan updated successfully', 200);

            } catch (Exception $e) {
                DB::rollback();
                return $this->response('0', $e, 401);
            }
        } else {
            return $this->response('', 'plan not  found', 404);
        }
    }

    public function addPhotoToMyplan(Request $request, $plan_id)
    {
        $plan = plan::find($plan_id);
        if ($plan) {
            if ($request->photos) {
                $request->photos[0];
                for ($i = 0; $i < count($request->photos); $i++) {
                    $path = $this->uploadMultiFile($request, $i, 'planPhotos', 'photos');
                    PlanPhoto::create([
                        'photoname' => $path,
                        'plan_id' => $plan->id,
                    ]);
                }
            }
        } else {
            return $this->response('', 'plan not founded successfully', 200);
        }
        return $this->response($this->planResources($plan), 'photos added successfully', 200);
    }

    public function getplan($plan_id) {
        $plan=Plan::find($plan_id);
        if($plan){
            return $this->response($this->planResources($plan),"a plan Data",201);
        }
        return $this->response('',"this plan_id not found",401);
    }


    public function getAllPlannerPlans($planner_id){
        $planner=Planner::find($planner_id);
        if($planner){
            $plans=$planner->plan;
            if($plans){
                foreach($plans as $plan){
                    $data[]=$this->planResources($plan);
                }
                return $this->response($data,"planner plans",201);
            }return $this->response('',"This planner dosnt have plans",404);

        }return $this->response('',"This planner id not found",401);
    }
    public function getAllPlans(){
        $plans=Plan::get();
        if($plans){
                foreach($plans as $plan){
                    $data[]=$this->planResources($plan);
            }
            return $this->response($data,"plans returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }








    public function viewConfirmedBookingsPlans()
    {
        $bookingplans = PlanRequest::where('status', 'confirmed')->get();

        return response()->json([
                'message' => 'Pending bookings for plans retrieved successfully',
                'data' => $bookingplans], 200);

    }
    public function viewCancelledBookingsPlans()
    {
        $bookingplans = PlanRequest::where('status', 'cancelled')->get();

        return response()->json([
                'message' => 'Pending bookings for plans retrieved successfully',
                'data' => $bookingplans], 200);

    }
    public function viewBookingsplans()
    {
        $bookingplans = PlanRequest::where('status', 'unconfirmed')->get();

        return response()->json([
                'message' => 'Pending bookings for plans retrieved successfully',
                'data' => $bookingplans], 200);

    }

    public function confirmBookingPlan($bookingplanId)
    {
         $bookingplan = PlanRequest::findOrFail($bookingplanId);

       $planner  = $bookingplan->planner_id;

       try {
        $planner = Auth::guard('planner-api')->user();
        if (!$planner) {
            throw new JWTException('Invalid token');
        }
        $planner_id = $planner->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }

    $actor_id = Auth::guard('planner-api')->user()->id;

    if($planner_id==$actor_id){


        $bookingplan->status = 'confirmed';
        $bookingplan->save();

        return response()->json([
            'message' => 'Booking confirmed successfully',
            'data' => $bookingplan
        ], 200);

    }        return response()->json([
        'message' => 'Unauthorized',
    ], 200);

    }

    public function rejectBookingPlan($bookingplanId)
    {
         $bookingplan = PlanRequest::findOrFail($bookingplanId);

       $planner  = $bookingplan->planner_id;

       try {
        $planner = Auth::guard('planner-api')->user();
        if (!$planner) {
            throw new JWTException('Invalid token');
        }
        $planner_id = $planner->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }

    $actor_id = Auth::guard('planner-api')->user()->id;

    if($planner_id==$actor_id){


        $bookingplan->status = 'cancelled';
        $bookingplan->save();

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'data' => $bookingplan
        ], 200);

    }        return response()->json([
        'message' => 'Unauthorized',
    ], 200);

    }





}
