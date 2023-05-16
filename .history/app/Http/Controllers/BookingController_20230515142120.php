<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Admin;
use App\Models\User;
use App\Models\Hall;
use App\Http\Traits\GeneralTraits;
use App\Models\PlanRequest;
use App\Models\Plan;
use App\Models\Planner;
use App\Models\Supplier;
use App\Models\SubService;
use App\Models\SubRequest;

use App\Http\responseTrait;

use Illuminate\Support\Facades\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;


class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    use responseTrait;
    use GeneralTraits;

    public function index()
    {
        //
    }





    public function getAvailableHalls(Request $request) {
        // Get all room IDs that have bookings overlapping with the check-in/out dates
        $hall_id = $request ->hall_id;
        $checkInDate= $request -> check_in_date;
        $checkOutDate = $request -> check_out_date;

        $bookedHallIds = Booking::where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                      ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                      ->orWhere(function ($query) use ($checkInDate, $checkOutDate) {
                          $query->where('check_in_date', '<', $checkInDate)
                                ->where('check_out_date', '>', $checkOutDate);
                      });
            })
            ->pluck('hall_id')
            ->toArray();

        // Get all rooms that are not booked for the specified date range
        $availableHalls[] = Hall::whereNotIn('id', $bookedHallIds)->get();


        return response()->json(['data'=>$availableHalls]);
    }

    function availablity_for_booking(Request $request){
         // Check if room is available for booking

         $hall_id = $request ->hall_id;
         $checkInDate= $request -> check_in_date;
         $checkOutDate = $request -> check_out_date;


        $avl_for_book = Booking::where('hall_id', $hall_id)
        ->where(function ($query) use ($checkInDate, $checkOutDate) {
            $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                ->orWhere(function ($query) use ($checkInDate, $checkOutDate) {
                    $query->where('check_in_date', '<', $checkInDate)
                            ->where('check_out_date', '>', $checkOutDate);
                });
        })
        ->count();


        return ($avl_for_book);
    }



    public function bookRoom(Request $request)
    {
        // validate input data
        $validatedData = $request->validate([

            'hall_id'=> 'required|integer',
            'check_in_date'=> 'required|date',
            'check_out_date'=> 'required|date|after:check_in_date',
        ]);

        try {
            $the_user_id = Auth::guard('user-api')->user()->id;
        } catch (\Exception  $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $hall = Hall::find($request->input('hall_id'));


        if (!$hall) {
            return response()->json(['error' => 'Hall not found'], 404);
        }else{

            if( $this->availablity_for_booking($request)== 0){


                    $special_offer = $hall->offers()
                    ->where('start_date', '<=', $request->input('check_in_date'))
                    ->where('end_date', '>=', $request->input('check_out_date'))
                    ->first();

                    $price = $special_offer ? $special_offer->price : $hall->price;

                    // create new booking record
                    $booking = Booking::create([
                        'user_id' => auth::guard('user-api')->user()->id,
                        'user_name' => auth::guard('user-api')->user()->name,
                        'hall_id' => $validatedData['hall_id'],
                        'hall_name'=> $hall->name,
                        'check_in_date' => $validatedData['check_in_date'],
                        'check_out_date' => $validatedData['check_out_date'],
                        'price' => $price,


                    ]);


                    // return JSON responses
                    return response()->json([
                        'message' => 'Booking created successfully',
                        'booking' => $booking,
                    ]);
                }
                else return response()->json([
                    'message' => 'Hall Is Not AVILABLE ']);
            }

        }



    public function viewBookings()
    {
        $bookings = Booking::where('status', 'unconfirmed')->get();

        return response()->json([
                'message' => 'Pending bookings retrieved successfully',
                'data' => $bookings], 200);

    }




    public function getOwnerAllBookings()
{

    try {
        $owner = Auth::guard('owner-api')->user();
        if (!$owner) {
            throw new JWTException('Invalid token');
        }
        $owner_id = $owner->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = Booking::whereHas('hall.owner', function ($query) use ($owner_id) {
        $query->where('id', $owner_id);
    })->get();

    return response()->json($bookings);
}



    public function getOwnerConfirmedBookings()
{

    try {
        $owner = Auth::guard('owner-api')->user();
        if (!$owner) {
            throw new JWTException('Invalid token');
        }
        $owner_id = $owner->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = Booking::whereHas('hall.owner', function ($query) use ($owner_id) {
        $query->where('id', $owner_id);
    })->where('status', 'confirmed')->get();

    return response()->json($bookings);
}
    public function getOwnerUnConfirmedBookings()
{

    try {
        $owner = Auth::guard('owner-api')->user();
        if (!$owner) {
            throw new JWTException('Invalid token');
        }
        $owner_id = $owner->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = Booking::whereHas('hall.owner', function ($query) use ($owner_id) {
        $query->where('id', $owner_id);
    })->where('status', 'unconfirmed')->get();

    return response()->json($bookings);
}
    public function getOwnerCancelledBookings()
{

    try {
        $owner = Auth::guard('owner-api')->user();
        if (!$owner) {
            throw new JWTException('Invalid token');
        }
        $owner_id = $owner->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = Booking::whereHas('hall.owner', function ($query) use ($owner_id) {
        $query->where('id', $owner_id);
    })->where('status', 'cancelled')->get();

    return response()->json($bookings);
}







    public function getPlannerAllBookings()
{

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




    $bookings = planRequest::whereHas('plan.planner', function ($query) use ($planner_id) {
        $query->where('id', $planner_id);
    })->get();

    return response()->json($bookings);
}



    public function getPlannerConfirmedBookings()
{

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




    $bookings = planrequest::whereHas('plan.planner', function ($query) use ($planner_id) {
        $query->where('id', $planner_id);
    })->where('status', 'confirmed')->get();

    return response()->json($bookings);
}
    public function getPlannerUnConfirmedBookings()
{

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




    $bookings = planrequest::whereHas('plan.planner', function ($query) use ($planner_id) {
        $query->where('id', $planner_id);
    })->where('status', 'unconfirmed')->get();

    return response()->json($bookings);
}
    public function getPlannerCancelledBookings()
{

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




    $bookings = planrequest::whereHas('plan.planner', function ($query) use ($planner_id) {
        $query->where('id', $planner_id);
    })->where('status', 'cancelled')->get();

    return response()->json($bookings);
}






    public function getUserAllPlanRequests()
{

    try {
        $user = Auth::guard('user-api')->user();
        if (!$user) {
            throw new JWTException('Invalid token');
        }
        $user_id = $user->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = planRequest::where('user_id', $user_id)->get();

    return response()->json($bookings);
}



    public function getUserConfirmedPlanRequests()
{

    try {
        $user = Auth::guard('user-api')->user();
        if (!$user) {
            throw new JWTException('Invalid token');
        }
        $user_id = $user->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = planrequest::where('user_id', $user_id)->where('status', 'confirmed')->get();

    return response()->json($bookings);
}
    public function getUserUnConfirmedPlanRequests()
{

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




    $bookings = planrequest::whereHas('plan.planner', function ($query) use ($planner_id) {
        $query->where('id', $planner_id);
    })->where('status', 'unconfirmed')->get();

    return response()->json($bookings);
}
    public function getUserCancelledPlanRequests()
{

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




    $bookings = planrequest::whereHas('plan.planner', function ($query) use ($planner_id) {
        $query->where('id', $planner_id);
    })->where('status', 'cancelled')->get();

    return response()->json($bookings);
}


public function getUserAllBookings()
{

    try {
        $user = Auth::guard('user-api')->user();
        if (!$user) {
            throw new JWTException('Invalid token');
        }
        $user_id = $user->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = Booking::where('user_id', $user_id)->get();

    return response()->json($bookings);
}



    public function getUserConfirmedBookings()
{

    try {
        $user = Auth::guard('user-api')->user();
        if (!$user) {
            throw new JWTException('Invalid token');
        }
        $user_id = $user->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = Booking::where('user_id', $user_id)->where('status', 'confirmed')->get();

    return response()->json($bookings);
}
    public function getUserUnConfirmedBookings()
{

    try {
        $user = Auth::guard('user-api')->user();
        if (!$user) {
            throw new JWTException('Invalid token');
        }
        $user_id = $user->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = Booking::where('user_id', $user_id)->where('status', 'unconfirmed')->get();

    return response()->json($bookings);
}
    public function getUserCancelledBookings()
{

    try {
        $user = Auth::guard('user-api')->user();
        if (!$user) {
            throw new JWTException('Invalid token');
        }
        $user_id = $user->id;
        } catch (JWTException $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 401);
        } catch (\Exception $e) {

        }




    $bookings = Booking::where('user_id', $user_id)->where('status', 'cancelled')->get();

    return response()->json($bookings);
}








    public function confirmBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->status = 'confirmed';
        $booking->save();

        return response()->json([
            'message' => 'Booking confirmed successfully',
            'data' => $booking
        ], 200);    }

    public function rejectBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'data' => $booking
        ], 200);    }




    public function destroyRejectedBookings()
    {
        $bookings = Booking::where('status', 'cancelled')->get();

        foreach ($bookings as $booking) {
            $booking->delete();
        }

        return response()->json([
            'message' => 'Rejected bookings deleted successfully',
        ], 200);
    }




    public function bookPlan(Request $request)
    {
        // validate input data
        $validatedData = $request->validate([


            'plan_id'=> 'integer',
            'check_in_date'=> 'required|date',
            'check_out_date'=> 'required|date|after:check_in_date',








        ]);

        try {
            $the_user_id = Auth::guard('user-api')->user()->id;
        } catch (\Exception  $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $plan = Plan::find($request->input('plan_id'));
        $planname = $plan->plan_name;

        $planner = Planner::findOrFail($plan->planner_id);
        $planner_name = $planner->name;



        if (!$plan) {
            return response()->json(['error' => 'Hall not found'], 404);
        }else{

            // create new Planbooking record
            $bookingPlan = PlanRequest::create([
                'planner_id' => $plan->planner_id,
                'planner_name' => $planner_name,
                'user_id' => auth::guard('user-api')->user()->id,
                'user_name' => auth::guard('user-api')->user()->name,
                'plan_id' => $validatedData['plan_id'],
                'plan_name' =>$plan->name,
                'check_in_date' => $validatedData['check_in_date'],
                'check_out_date' => $validatedData['check_out_date'],
                'price' => $plan->price,
                'status' =>'unconfirmed',


            ]);


            // return JSON responses
            return response()->json([
                'message' => 'Booking Plan created successfully',
                'booking' => $bookingPlan,
            ]);


        }

        }
    public function bookSubService(Request $request)
    {
        // validate input data
        $validatedData = $request->validate([


            'sub_id'=> 'integer',
            'check_in_date'=> 'required|date',
            'check_out_date'=> 'required|date|after:check_in_date',

        ]);

        try {
            $the_user_id = Auth::guard('user-api')->user()->id;
        } catch (\Exception  $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $subservice = SubService::find($request->input('sub_id'));
        $subname = $subservice->sub_name;

        $supplier = Supplier::findOrFail($subservice->supplier_id);
        $supplier_name = $supplier->name;



        if (!$subservice) {
            return response()->json(['error' => 'Hall not found'], 404);
        }else{

            // create new Planbooking record
            $bookingSubservice = SubRequest::create([
                'supplier_id' => $subservice->supplier_id,
                'supplier_name' => $supplier_name,
                'user_id' => auth::guard('user-api')->user()->id,
                'user_name' => auth::guard('user-api')->user()->name,
                'sub_id' => $validatedData['sub_id'],
                'sub_name' =>$subservice->name,
                'check_in_date' => $validatedData['check_in_date'],
                'check_out_date' => $validatedData['check_out_date'],
                'price' => $subservice->price,
                'status' =>'unconfirmed',


            ]);


            // return JSON responses
            return response()->json([
                'message' => 'Booking SubService created successfully',
                'booking' => $bookingSubservice,
            ]);


            }

        }











    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     //
    // }
}
