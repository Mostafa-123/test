<?php

namespace App\Http;

use App\Models\Hall;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use  App\Http\Resources\ownerResource;
use App\Http\Resources\plannersResource;

trait responseTrait
{
    public function response($data=null,$message=null,$status=null){
        $array=[
            'data'=>$data,
            'message'=>$message,
            'status'=>$status
        ];
        return response($array);
    }
    public function uploadFile(Request $request,$folderName,$fileName){
        if($request->hasFile($fileName)&& $request->$fileName != Null){
            $path = $request->file($fileName)->store($folderName,'custom');
            return $path;
        }
        return Null;
    }
    public function uploadMultiFile(Request $request,$i,$folderName,$fileName){
        if($request->hasfile($fileName)&& $request->$fileName[$i] != Null ){
            $path = $request->file($fileName)[$i]->store($folderName,'custom');
                return $path;
            }
            return Null;
        }
    public function getFile($path){
        return response()->file(storage_path($path));
    }
    public function deleteFile($path){
        if (file_exists(storage_path($path))) {
            return File::delete(storage_path($path));
        }
       // if(Storage::exists($path)){
       //     Storage::delete($path);
       // }
        return null;//apiResponse(401,'',"File doesn't exists");
    }
    public function planResources(Plan $plan){
        $photo[]=null;
        $photos=$plan->planPhotos;
        $planner=new plannersResource($plan->planner);
            if($photos){
                $i=0;
                for($i=0;$i<count($photos);$i++){
                    $photo[$i]="http://127.0.0.1:8000/planner/plan/planphoto/".$plan->id."/".$photos[$i]->id;
                }
            }
        return [
            'id'=>$plan->id,
            'planner'=>$planner,
            'name'=>$plan->name,
            'price'=>$plan->price,
            'description'=>$plan->description,
            'photos'=>$photo,
        ];
    }
    public function hallResources(Hall $hall){
        $photo[]=null;
        $video[]=null;
        $service[]=null;
        $show[]=null;

        $photos=$hall->photos;
            if($photos){
                $i=0;
                for($i=0;$i<count($photos);$i++){
                    $photo[$i]="http://127.0.0.1:8000/owner/hall/hallphoto/".$hall->id."/".$photos[$i]->id;
                }
            }
            $videos=$hall->videos;
            if($videos){
                $i=0;
                for($i=0;$i<count($videos);$i++){
                    $video[$i]="http://127.0.0.1:8000/owner/hall/hallvideo/".$hall->id."/".$videos[$i]->id;
                }
            }
            $shows=$hall->shows;
            if($shows){
                $i=0;
                for($i=0;$i<count($shows);$i++){
                    $show[$i]=$shows[$i]->showname;
                }
            }
            $services=$hall->services;
            if($services){
                $i=0;
                for($i=0;$i<count($services);$i++){
                    $service[$i]=$services[$i]->servicename;
                }
            }
        return [
            'id'=>$hall->id,
            'name'=>$hall->name,
            'address'=>$hall->address,
            'country'=>$hall->country,
            'city'=>$hall->city,
            'street'=>$hall->street,
            'rooms'=>$hall->rooms,
            'chairs'=>$hall->chairs,
            'price'=>$hall->price,
            'hours'=>$hall->hours,
            'tables'=>$hall->tables,
            'type'=>$hall->type,
            'capacity'=>$hall->capacity,
            'available'=>$hall->available,
            'start_party'=>$hall->start_party,
            'end_party'=>$hall->end_party,
            'owner_id' =>$hall->owner(),
            'verified'=>$hall->verified,
            'photos'=>$photo,
            'videos'=>$video,
            'show'=>$show,
            'service'=>$service,
            'comments_count'=> $hall->comments()->count(),
            'comments'=> $hall->comments,
            'likes_count'=> $hall->likes()->count(),
        ];
    }
}
