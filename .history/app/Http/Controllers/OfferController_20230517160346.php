<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hall;
use App\Models\Offer;

class OfferController extends Controller
{
    //

    public function store(Request $request)
    {
        $user = auth()->guard('admin-api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        else{
        $hall = Hall::find($request->input('hall_id'));

        if (!$hall) {
            return response()->json(['error' => 'Hall not found'], 404);
        }

        $special_offer = new Offer([
            'hall_id' => $request->input('hall_id'),
            'hall_name' => $request->input('hall_name'),
            'package_description' => $request->input('package_description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'price' => $request->input('price'),
        ]);

        $hall->offers()->save($special_offer);

        return response()->json([

            'message' => 'Special offer created',
            'offer' =>$special_offer



    ], 201);
        }
    }




    public function update(Request $request, $offer_id)
    {

        $user = auth()->guard('admin-api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        else{
        $special_offer = Offer::find($offer_id);

        if (!$special_offer) {
            return response()->json(['error' => 'Special offer not found'], 404);
        }

        $special_offer->start_date = $request->input('start_date');
        $special_offer->end_date = $request->input('end_date');
        $special_offer->price = $request->input('price');

        $special_offer->save();

        return response()->json(['message' => 'Special offer updated']);
        }
    }




    public function destroy($offer_id)
    {
        $user = auth()->guard('admin-api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        else{
        $special_offer = Offer::find($offer_id);

        if (!$special_offer) {
            return response()->json(['error' => 'Special offer not found'], 404);
        }

        $special_offer->delete();

        return response()->json(['message' => 'Special offer deleted']);
        }
    }


    public function viewOffer($id)
    {
        $offers = Offer::where('id',$id)->first();

        if(!$offers){
            return response()->json([
                'message' => 'There is no offer',
                'data' => $offers], 200);

        }else{
                return response()->json([
                        'message' => 'Offers retrieved successfully',
                        'data' => $offers], 200);
        }

    }
    public function viewAll()
    {
        $offers = Offer::get();

        return response()->json([
                'message' => 'Offers retrieved successfully',
                'data' => $offers], 200);

    }

}
