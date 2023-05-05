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

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

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

            'hall_id'=> 'integer',
            'hall_name'=> 'string',
            'check_in_date'=> 'date',
            'check_out_date'=> 'date |after:check_in_date',
            'price'=> 'integer',
            'status'=> 'string',
/*
            'hall_id'=> 'required|integer',
            'hall_name'     => 'required|string',
            'check_in_date'=> 'required|date',
            'check_out_date'=> 'required|date|after:check_in_date',
            'price'=> 'required|integer',
            'status'=> 'required|string', */



        ]);

/*         $start = $request -> check_in_date;
 */
    $hall = Hall::find($request->input('hall_id'));

    if (!$hall) {
        return response()->json(['error' => 'Hotel not found'], 404);
    }else{

        if( $this->availablity_for_booking($request)== 0){


            $special_offer = $hall->offers()
            ->where('start_date', '<=', $request->input('check_in_date'))
            ->where('end_date', '>=', $request->input('check_out_date'))
            ->first();

           $price = $special_offer ? $special_offer->price : $hall->price;

                // create new booking record
                $booking = Booking::create([
                    'user_id' => auth()->user()->id,
                    'user_name' => auth()->user()->name,
                    'hall_id' => $validatedData['hall_id'],
                    'hall_name' => $validatedData['hall_name'],
                    'check_in_date' => $validatedData['check_in_date'],
                    'check_out_date' => $validatedData['check_out_date'],
                    'price' => $price,
                    'status' => $validatedData['status'],


                ]);


                // return JSON responses
                return response()->json([
                    'message' => 'Booking created successfully',
                    'booking' => $booking,
                ]);
            }
            else return response()->json([
                'message' => 'Erorr Booking ']);
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
            'planner_id'=> 'integer',
            'planner_name'=> 'string',
            'user_id'=> 'integer',
            'user_name'=> 'string',
            'plan_id'=> 'integer',
            'plan_name'=> 'string',
            'price'=> 'integer',
            'status'=> 'string',

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

            // create new Planbooking record
            $bookingPlan = PlanRequest::create([
                'planner_id' => $validatedData['planner_id'],
                'planner_name' => $validatedData['planner_name'],
                'user_id' => $validatedData['user_id'],
                'user_name' => $validatedData['user_name'],
                'plan_id' => $validatedData['plan_id'],
                'plan_name' => $validatedData['plan_name'],
                'price' => $validatedData['price'],
                'status' => $validatedData['status'],


            ]);


            // return JSON responses
            return response()->json([
                'message' => 'Booking Plan created successfully',
                'booking' => $bookingPlan,
            ]);}











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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
