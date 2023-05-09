<?php

namespace App\Http\Controllers\Api\Owner;



use Illuminate\Http\Request;

use App\Models\Hall;
use App\Models\halls;
use App\Models\Booking;
use App\Http\Traits\GeneralTraits;
use App\Http\Controllers\Controller;
use App\Http\responseTrait;
use App\Models\Owner;
use App\Models\Photo;
use App\Models\Service;
use App\Models\Show;
use App\Models\Video;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;







class OwnerController extends Controller
{

    use responseTrait;

    use GeneralTraits;

    public function addHallRequests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'address' => 'required|max:255',
            'country'=>'required|max:255',
            'city'=>'required|max:255',
            'street'=>'required|max:255',
            'rooms' => 'required',
            'chairs' => 'required',
            'price' => 'required',
            'hours' => 'required',
            'tables' => 'required',
            'type' => 'required|max:255',
            'capacity' => 'required',
            'available' => 'required',
            // 'start_party' => 'required',
            // 'end_party' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response(null, $validator->errors(), 400);
        }

        $result = Hall::create([
            'name' => $request->name,
            'address' => $request->address, 'country' => $request->country,
            'city' => $request->city,
            'street' => $request->street,
            'rooms' => $request->rooms,

            'chairs' => $request->chairs, 'price' => $request->price,
            'hours' => $request->hours, 'tables' => $request->tables,
            'type' => $request->type, 'capacity' => $request->capacity,
            'available' => $request->available,
            'owner_id' => Auth::guard('api')->user()->id,
            'start_party' => $request->start_party,
            'end_party' => $request->end_party
        ]);


       $result->load('owner');
        $comment->load('halls');
        $result->save();



        if ($request->photos[0]) {
            for ($i = 0; $i < count($request->photos); $i++) {
                $path = $this->uploadMultiFile($request, $i, 'hallPhotos', 'photos');
                Photo::create([
                    'photoname' => $path,
                    'hall_id' => $result->id,
                ]);
            }
        }
        if ($request->videos[0]) {
            for ($i = 0; $i < count($request->videos); $i++) {
                $path = $this->uploadMultiFile($request, $i, 'hallVideos', 'videos');
                Video::create([
                    'videoname' => $path,
                    'hall_id' => $result->id,
                ]);
            }
        }
        if ($request->services[0]) {
            for ($i = 0; $i < count($request->services); $i++) {
                $services = $request->services;
                Service::create([
                    'servicename' => $services[$i],
                    'hall_id' => $result->id,
                ]);
            }
        }
        if ($request->shows[0]) {
            for ($i = 0; $i < count($request->shows); $i++) {
                $shows = $request->shows;
                Show::create([
                    'showname' => $shows[$i],
                    'hall_id' => $result->id,
                ]);
            }
        }

        if ($result) {
            return $this->response($this->hallResources($result), 'done', 201);
        } else {
            return $this->response(null, 'halls is not saved', 405);
        }
    }

    public function getHallPhoto($hall_id, $photo_id)
    {
        $hall = Hall::find($hall_id);
        if ($hall) {
            $photo = Photo::find($photo_id);
            if ($photo) {
                return $this->getFile($photo->photoname);
            }
            return $this->response('', "This hall doesn't has photo", 401);
        }
        return $this->response('', 'this hall_id not found', 401);
    }
    public function getHallVideo($hall_id, $video_id)
    {
        $hall = Hall::find($hall_id);
        if ($hall) {
            $video = Video::find($video_id);
            if ($video) {
                return $this->getFile($video->videoname);
            }
            return $this->response('', "This hall doesn't has video", 401);
        }
        return $this->response('', 'this hall_id not found', 401);
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

    public function deleteAllOwnerHalls($owner_id){
        $owner=Owner::find($owner_id);
        if($owner){
            $halls=$owner->hall;
            if($halls){
                foreach($halls as $hall){
                    $this->destroyHall($hall->id);
                }
                return $this->response('',"owner halls deleted succeffully",201);
            }return $this->response('',"This owner dosnt have halls",404);

        }return $this->response('',"This owner id not found",401);
    }

    public function updateHall(Request $request, $hall_id)
    {
        $hall = Hall::find($hall_id);

        if ($hall) {
            $photos = $hall->photos;
            if ($request->photos[0]) {
                if ($photos) {
                    for ($i = 0; $i < count($photos); $i++) {
                        $path = $photos[$i]->photoname;

                        $photo = Photo::where('photoname', $path)->get();
                        // print($photo[0]);die;
                        $photo[0]->delete();
                        $this->deleteFile($path);
                    }
                    for ($i = 0; $i < count($request->photos); $i++) {
                        $path = $this->uploadMultiFile($request, $i, 'hallPhotos', 'photos');
                        Photo::create([
                            'photoname' => $path,
                            'hall_id' => $hall->id,
                        ]);
                    }
                } else if ($photos == null) {
                    for ($i = 0; $i < count($request->photos); $i++) {
                        $path = $this->uploadMultiFile($request, $i, 'hallPhotos', 'photos');
                        Photo::create([
                            'photoname' => $path,
                            'hall_id' => $hall->id,
                        ]);
                    }
                }
            }
            $shows = $hall->shows;
            if ($request->shows[0]) {
                if ($shows) {
                    for ($i = 0; $i < count($shows); $i++) {
                        $path = $shows[$i]->showname;

                        $show = Show::where('showname', $path)->get();
                        // print($photo[0]);die;
                        $show[0]->delete();
                    }
                    for ($i = 0; $i < count($request->shows); $i++) {
                        $path = $request->shows;
                        Show::create([
                            'showname' => $path[$i],
                            'hall_id' => $hall->id,
                        ]);
                    }
                } else if ($shows == null) {
                    for ($i = 0; $i < count($request->shows); $i++) {
                        $path = $request->shows;
                        Show::create([
                            'showname' => $path[$i],
                            'hall_id' => $hall->id,
                        ]);
                    }
                }
            }
            $services = $hall->services;
            if ($request->services[0]) {
                if ($services) {
                    for ($i = 0; $i < count($services); $i++) {
                        $path = $services[$i]->servicename;

                        $service = Service::where('servicename', $path)->get();
                        // print($photo[0]);die;
                        $service[0]->delete();
                    }
                    for ($i = 0; $i < count($request->services); $i++) {
                        $path = $request->services;
                        Service::create([
                            'servicename' => $path[$i],
                            'hall_id' => $hall->id,
                        ]);
                    }
                } else if ($services == null) {
                    for ($i = 0; $i < count($request->services); $i++) {
                        $path = $request->services;
                        Service::create([
                            'servicename' => $path[$i],
                            'hall_id' => $hall->id,
                        ]);
                    }
                }
            }
            $videos = $hall->videos;
            if ($request->videos[0]) {
                if ($videos) {
                    for ($i = 0; $i < count($videos); $i++) {
                        $path = $videos[$i]->videoname;

                        $video = Video::where('videoname', $path)->get();
                        // print($photo[0]);die;
                        $video[0]->delete();
                        $this->deleteFile($path);
                    }
                    for ($i = 0; $i < count($request->videos); $i++) {
                        $path = $this->uploadMultiFile($request, $i, 'hallVideos', 'videos');
                        Video::create([
                            'videoname' => $path,
                            'hall_id' => $hall->id,
                        ]);
                    }
                } else if ($videos == null) {
                    for ($i = 0; $i < count($request->videos); $i++) {
                        $path = $this->uploadMultiFile($request, $i, 'hallVideos', 'videos');
                        Video::create([
                            'videoname' => $path,
                            'hall_id' => $hall->id,
                        ]);
                    }
                }
            }
            $newData = [
                'name' => $request->name?$request->name:$hall->name,
                'address' => $request->address?$request->address:$hall->address,
                'country' => $request->country?$request->country:$hall->country,
                'city' => $request->city?$request->city:$hall->city,
                'street' => $request->street?$request->street:$hall->street,
                'rooms' => $request->rooms?$request->rooms:$hall->rooms,
                'chairs' => $request->chairs?$request->chairs:$hall->chairs,
                'price' => $request->price?$request->price:$hall->price,
                'hours' => $request->hours?$request->hours:$hall->hours,
                'tables' => $request->tables?$request->tables:$hall->tables,
                'type' => $request->type?$request->type:$hall->type,
                'capacity' => $request->capacity?$request->capacity:$hall->capacity,
                'available' => $request->available?$request->available:$hall->available,
                'start_party' => $request->start_party?$request->start_party:$hall->start_party,
                'end_party' => $request->end_party?$request->end_party:$hall->end_party,


            ];

            $hall->update($newData);
        } else {
            return $this->response('', 'hall not  found', 404);
        }
        return $this->response($this->hallResources($hall), 'hall updated successfully', 200);
    }

    public function addPhotoToMyhall(Request $request, $hall_id)
    {

        $hall = Hall::find($hall_id);

        if ($hall) {
            if ($request->photos[0]) {
                for ($i = 0; $i < count($request->photos); $i++) {
                    $path = $this->uploadMultiFile($request, $i, 'hallPhotos', 'photos');
                    Photo::create([
                        'photoname' => $path,
                        'hall_id' => $hall->id,
                    ]);
                }
            }
        } else {
            return $this->response('', 'hall not founded successfully', 404);
        }
        return $this->response($this->hallResources($hall), 'photos added successfully', 200);
    }

    public function addVideoToMyhall(Request $request, $hall_id)
    {

        $hall = Hall::find($hall_id);

        if ($hall) {
            if ($request->videos[0]) {
                for ($i = 0; $i < count($request->videos); $i++) {
                    $path = $this->uploadMultiFile($request, $i, 'hallVideos', 'videos');
                    Video::create([
                        'videoname' => $path,
                        'hall_id' => $hall->id,
                    ]);
                }
            }
        } else {
            return $this->response('', 'hall not founded successfully', 404);
        }
        return $this->response($this->hallResources($hall), 'photos added successfully', 200);
    }

    public function gethall($hall_id) {
        $hall=Hall::withcount(['likes','comments'])->find($hall_id);
        if($hall){
            return $this->response($this->hallResources($hall),"a hall Data",201);
        }
        return $this->response('',"this hall_id not found",401);
    }


    public function getAllOwnerHalls($owner_id){
        $owner=Owner::find($owner_id);
        if($owner){
            $halls=$owner->hall;
            if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
                }
                return $this->response($data,"owner halls",201);
            }return $this->response('',"This owner dosnt have halls",404);

        }return $this->response('',"This owner id not found",401);
    }
    public function getAllHalls(){
        $halls=Hall::where('verified', 'confirmed')->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function getAllHallsByPrice($minPrice,$maxPrice){
        $halls=Hall::where('verified', 'confirmed')->where('price','>',$minPrice)->where('price','<',$maxPrice)->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getAllHallsByName(Request $request){
        $halls=Hall::where('verified', 'confirmed')->where('name',$request->name)->get();

        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getAllHallsByCountry($country){
        $halls=Hall::where('verified', 'confirmed')->where('country',$country)->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getAllHallsByCity($city){
        $halls=Hall::where('verified', 'confirmed')->where('city',$city)->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getAllHallsByStreet($street){
        $halls=Hall::where('verified', 'confirmed')->where('street',$street)->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function getAllHallsByType($type){
        $halls=Hall::where('verified', 'confirmed')->where('type',$type)->get();
        if($halls){
                foreach($halls as $hall){
                    $data[]=$this->hallResources($hall);
            }
            return $this->response($data,"halls returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function DestroyAllHallRequest()
    {
        $reqs = Hall::where('status', 'cancelled')->get();

        foreach ($reqs as $req) {
            $req->delete();
        }

        return response()->json([
            'message' => 'Rejected Halls deleted successfully',
        ], 200);
    }

    public function destroyHallRequest($id){

        $result=Hall::where('status', 'cancelled')->find($id);

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
            $result->delete();
            return $this->response('','The hall request deleted',200);
            }
    }

    public function confirmBooking($bookingId){
        $booking = Booking::findOrFail($bookingId);

        $booking->status = 'confirmed';
        $booking->save();

        return response()->json([
            'message' => 'Booking confirmed successfully',
            'data' => $booking
        ], 200);    }

    public function rejectBooking($bookingId){
        $booking = Booking::findOrFail($bookingId);

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'data' => $booking
        ], 200);    }




    public function destroyRejectedBookings(){
        $bookings = Booking::where('status', 'cancelled')->get();

        foreach ($bookings as $booking) {
            $booking->delete();
        }

        return response()->json([
            'message' => 'Rejected bookings deleted successfully',
        ], 200);
    }


















    // public function updateAllInfoOfHallRequest(Request $req ,$id){
    //     $result=Hall::where('status', 'cancelled')->find($id);
    //     if(!$result){
    //         return $this->response(null,'The hall Not Found',404);
    //     }

    //     $result->update($req->all());

    //     if($result){
    //         return $this->response(new hallResource($result),'The hall update',201);
    //     }else{
    //         return $this->response(null,'The hall not update',400);
    //     }
    // }
}
