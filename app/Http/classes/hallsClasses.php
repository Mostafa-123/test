<?php

namespace App\Http\classes;

use App\Http\Resources\hallResource;
use App\Http\responseTrait;
use App\Models\Hall;
/* use App\Models\halls; */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait hallsClasses
{
    use responseTrait;
    public function getHall($id){
        $result=Hall::with(['comments','likes'])->find($id);
        if(!$result){
            return $this->response(null,'hall not found',404);
        }

        return $this->response(new hallResource( $result),'ok',200);
    }
    public function getHalls(){
        $result= hallResource::collection( Hall::with(['comments','likes'])->get());

        return $this->response($result,'ok',200);
    }
    public function getConfirmedHalls(){
        $result= hallResource::collection( Hall::where('status', 'unconfirmed')->get());

        return $this->response($result,'ok',200);
    }

    public function getHallscounts(){
        $result= hallResource::collection( Hall::with(['comments','likes'])->get());

        return $this->response($result,'ok',200);
    }

    public function addHall(Request $req){
        $result=Hall::create($req->all());
        if($result){
            return $this->response(new hallResource( $result),'done',201);
        }else{
            return $this->response(null,'halls is not saved',405);
        }
    }
    public function updateAnyInfoInHall(Request $req,$id){
        $result=Hall::find($id);

        if(!$result){
            return $this->response(null,'The hall Not Found',404);
        }

        $result->update([
            'name'=>$req->name?$req->name:$result->name,
            'address'=>$req->address?$req->address:$result->address,
            'country'=>$req->country?$req->country:$result->country,
            'city'=>$req->city?$req->city:$result->city,
            'street'=>$req->street?$req->street:$result->street,

            'rooms'=>$req->rooms?$req->rooms:$result->rooms,
            'chairs'=>$req->chairs?$req->chairs:$result->chairs,
            'price'=>$req->price?$req->price:$result->price,
            'hours'=>$req->hours?$req->hours:$result->hours,
            'tables'=>$req->tables?$req->tables:$result->tables,
            'type'=>$req->type?$req->type:$result->type,
            'capacity'=>$req->capacity?$req->capacity:$result->capacity,
            'available'=>$req->available?$req->available:$result->available,
            'person_id'=>$req->person_id?$req->person_id:$result->person_id,
        ]);

        if($result){
            return $this->response(new hallResource($result),'The hall update',201);
        }
    }
    public function updateAllInfoOfHall(Request $req ,$id){
        $result=Hall::find($id);

        if(!$result){
            return $this->response(null,'The hall Not Found',404);
        }

        $result->update($req->all());

        if($result){
            return $this->response(new hallResource($result),'The hall update',201);
        }else{
            return $this->response(null,'The hall not update',400);
        }
    }
    public function destroyHall($id){

        $result=Hall::find($id);

        if(!$result){
            return $this->response(null,'The post Not Found',404);
        }

        $result->delete($id);

        if($result){
            return $this->response(null,'The post deleted',200);
        }else{
            return $this->response(null,'The post not deleted',405);

        }

    }
}
