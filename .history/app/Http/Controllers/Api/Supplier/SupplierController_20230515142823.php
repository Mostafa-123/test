<?php

namespace App\Http\Controllers\Api\Supplier;
use Illuminate\Http\Request;

use App\Models\PlanRequest;
use App\Http\Traits\GeneralTraits;
use App\Http\Controllers\Controller;
use App\Http\responseTrait;
use App\Models\SubService;
use App\Models\SubRequest;
use App\Models\SupPhoto;
use App\Models\Supplier;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;


class SupplierController extends Controller
{
    use GeneralTraits;

    use responseTrait;
    public function addService(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'price' => 'required|numeric',
            'country' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'type' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->response(null, $validator->errors(), 400);
        }

        try {
            $supplier = Auth::guard('supplier-api')->user();
            if (!$supplier) {
                throw new JWTException('Invalid token');
            }
            $supplier_id = $supplier->id;
        } catch (JWTException $e) {
            // handle the exception, such as logging it or returning an error response to the client
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {
            // handle other exceptions
            // ...
        }

        $supplier = Auth::guard('supplier-api')->user();
        $supplier_id = $supplier->id;


        try {
            DB::beginTransaction();
            $result = SubService::create([
                'supplier_id' => $supplier_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'type' => $request->type,
            ]);
            if ($request->photos) {
                if ($request->photos[0]) {
                    for ($i = 0; $i < count($request->photos); $i++) {
                        $path = $this->uploadMultiFile($request, $i, 'servicePhotos', 'photos');
                        SupPhoto::create([
                            'photoname' => $path,
                            'service_id' => $result->id,
                        ]);
                    }
                }
            }
            DB::commit();
            if ($result) {
                return $this->response($this->supServiceResources($result), 'done', 201);
            } else {
                return $this->response(null, 'service is not saved', 405);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->response('', $e, 401);
        }
    }
    public function deleteService($service_id)
    {
        $service = SubService::find($service_id);
        if ($service) {
            $photos = $service->servicesPhoto;
            if ($photos) {
                for ($i = 0; $i < count($photos); $i++) {
                    $path = $photos[$i]->photoname;
                    $this->deleteFile($path);
                }
            }
            $service->delete();
            return $this->response('', 'service deleted successfully', 201);
        }
        return $this->response('', 'this service_id not found', 401);
    }
    public function getServicePhoto($service_id, $photo_id)
    {
        $service = SubService::find($service_id);
        if ($service) {
            $photo = SupPhoto::find($photo_id);
            if ($photo) {
                return $this->getFile($photo->photoname);
            }
            return $this->response('', "This service doesn't has photo", 401);
        }
        return $this->response('', 'this service_id not found', 401);
    }
    public function updateService(Request $request, $service_id)
    {
        $service = SubService::find($service_id);
        if ($service) {
            try {
                DB::beginTransaction();
                $photos = $service->servicesPhoto;
                if ($request->photos) {
                    if ($request->photos[0]) {
                        if ($photos) {
                            for ($i = 0; $i < count($photos); $i++) {
                                $path = $photos[$i]->photoname;
                                $photo = SupPhoto::find($photos[$i]->id);
                                $photo->delete();
                                $this->deleteFile($path);
                            }
                            for ($i = 0; $i < count($request->photos); $i++) {
                                $path = $this->uploadMultiFile($request, $i, 'servicePhotos', 'photos');
                                SupPhoto::create([
                                    'photoname' => $path,
                                    'service_id' => $service->id,
                                ]);
                            }
                        } else if ($photos == null) {
                            for ($i = 0; $i < count($request->photos); $i++) {
                                $path = $this->uploadMultiFile($request, $i, 'servicePhotos', 'photos');
                                SupPhoto::create([
                                    'photoname' => $path,
                                    'service_id' => $service->id,
                                ]);
                            }
                        }
                    }
                }
                $newData = [
                    'name' => $request->name?$request->name:$service->name,
                    'description' => $request->description?$request->description:$service->description,
                    'price' => $request->price?$request->price:$service->price,
                    'country' => $request->country?$request->country:$service->country,
                    'city' => $request->city?$request->city:$service->city,
                    'address' => $request->address?$request->address:$service->address,
                    'type' => $request->type?$request->type:$service->type,
                ];
                $service->update($newData);
                DB::commit();
                $servicee = SubService::find($service_id);
                return $this->response($this->supServiceResources($servicee), 'service updated successfully', 200);
            } catch (Exception $e) {
                DB::rollback();
                return $this->response('0', $e, 401);
            }
        } else {
            return $this->response('', 'service not  found', 404);
        }
    }

    public function addPhotoToMyService(Request $request, $service_id)
    {
        $service = SubService::find($service_id);
        if ($service) {
            if ($request->photos) {
                if ($request->photos[0]) {
                    for ($i = 0; $i < count($request->photos); $i++) {
                        $path = $this->uploadMultiFile($request, $i, 'servicePhotos', 'photos');
                        SupPhoto::create([
                            'photoname' => $path,
                            'service_id' => $service->id,
                        ]);
                    }
                }
            }
        } else {
            return $this->response('', 'service not founded successfully', 200);
        }
        return $this->response($this->supServiceResources($service), 'photos added successfully', 200);
    }

    public function getservice($service_id)
    {
        $service = SubService::find($service_id);
        if ($service) {
            return $this->response($this->supServiceResources($service), "a service Data", 201);
        }
        return $this->response('', "this service_id not found", 401);
    }


    public function getAllSupplierServices($supplier_id)
    {
        $supplier = Supplier::find($supplier_id);
        if ($supplier) {
            $services = $supplier->subService;
            if ($services) {
                foreach ($services as $service) {
                    $data[] = $this->supServiceResources($service);
                }
                return $this->response($data, "supplier services", 201);
            }
            return $this->response('', "This supplier dosnt have services", 404);
        }
        return $this->response('', "This supplier id not found", 401);
    }
    public function getAllServices()
    {
        $services = SubService::get();
        if ($services) {
            foreach ($services as $service) {
                $data[] = $this->supServiceResources($service);
            }
            return $this->response($data, "services returned successfuly", 200);
        }
        return $this->response('', "somthing wrong", 401);
    }
    public function getAllflowers()
    {
        $services = SubService::where('type', 'flowers')->get();
        if ($services) {
            foreach ($services as $service) {
                $data[] = $this->supServiceResources($service);
            }
            return $this->response($data, "services returned successfuly", 200);
        }
        return $this->response('', "somthing wrong", 401);
    }

    public function getAllzaffatAndDj()
    {
        $services = SubService::where('type', 'zaffatAndDj')->get();
        if ($services) {
            foreach ($services as $service) {
                $data[] = $this->supServiceResources($service);
            }
            return $this->response($data, "services returned successfuly", 200);
        }
        return $this->response('', "somthing wrong", 401);
    }

    public function getAllcake()
    {
        $services = SubService::where('type', 'cake')->get();
        if ($services) {
            foreach ($services as $service) {
                $data[] = $this->supServiceResources($service);
            }
            return $this->response($data, "services returned successfuly", 200);
        }
        return $this->response('', "somthing wrong", 401);
    }

    public function getAlljallery()
    {
        $services = SubService::where('type', 'jallery')->get();
        if ($services) {
            foreach ($services as $service) {
                $data[] = $this->supServiceResources($service);
            }
            return $this->response($data, "services returned successfuly", 200);
        }
        return $this->response('', "somthing wrong", 401);
    }

    public function getAllcatering()
    {
        $services = SubService::where('type', 'catering')->get();
        if ($services) {
            foreach ($services as $service) {
                $data[] = $this->supServiceResources($service);
            }
            return $this->response($data, "services returned successfuly", 200);
        }
        return $this->response('', "somthing wrong", 401);
    }

    public function getAllbodycare()
    {
        $services = SubService::where('type', 'bodycare')->get();
        if ($services) {
            foreach ($services as $service) {
                $data[] = $this->supServiceResources($service);
            }
            return $this->response($data, "services returned successfuly", 200);
        }
        return $this->response('', "somthing wrong", 401);
    }

    public function getAllcar()
    {
        $services = SubService::where('type', 'car')->get();
        if ($services) {
            foreach ($services as $service) {
                $data[] = $this->supServiceResources($service);
            }
            return $this->response($data, "services returned successfuly", 200);
        }
        return $this->response('', "somthing wrong", 401);
    }



    public function viewConfirmedBookingsPlans()
    {
        $bookingplans = PlanRequest::where('status', 'confirmed')->get();

        return response()->json([
            'message' => 'Pending bookings for plans retrieved successfully',
            'data' => $bookingplans
        ], 200);
    }
    public function viewCancelledBookingsPlans()
    {
        $bookingplans = PlanRequest::where('status', 'cancelled')->get();

        return response()->json([
            'message' => 'Pending bookings for plans retrieved successfully',
            'data' => $bookingplans
        ], 200);
    }
    public function viewBookingsplans()
    {
        $bookingplans = PlanRequest::where('status', 'unconfirmed')->get();

        return response()->json([
            'message' => 'Pending bookings for plans retrieved successfully',
            'data' => $bookingplans
        ], 200);
    }






    public function confirmSubRequest($bookingplanId)
    {
        $bookingplan = SubRequest::findOrFail($bookingplanId);

        $supplier  = $bookingplan->supplier_id;

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

        if ($planner_id == $actor_id) {


            $bookingplan->status = 'confirmed';
            $bookingplan->save();

            return response()->json([
                'message' => 'Booking confirmed successfully',
                'data' => $bookingplan
            ], 200);
        }
        return response()->json([
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

        if ($planner_id == $actor_id) {


            $bookingplan->status = 'cancelled';
            $bookingplan->save();

            return response()->json([
                'message' => 'Booking cancelled successfully',
                'data' => $bookingplan
            ], 200);
        }
        return response()->json([
            'message' => 'Unauthorized',
        ], 200);
    }
}

