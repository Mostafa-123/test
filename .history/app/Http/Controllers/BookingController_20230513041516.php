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




/*
            'planner_id'=> 'required|integer',
            'planner_name'=> 'required|string',
            'user_id'=> 'required|integer',
            'user_name'=> 'required|string',
            'plan_id'=> 'required|integer',
            'plan_name'=> 'required|string',

            'price'=> 'required|integer',
            'status'=> 'required|string', */



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
