<?php

namespace App\Http\Controllers;

use App\Http\classes\hallsClasses;
use App\Http\Resources\hallResource;
use App\Http\responseTrait;
use App\Models\halls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class hallsController extends Controller
{
    use responseTrait;
    use hallsClasses;
    public function getHallController($id){
      return  $this->getHall($id);
    }
    public function getHallsController(){
      return  $this->getHalls();
    }
    public function addHallController(Request $req){

        $validator=Validator::make($req->all(),[
            'name'=>'required|max:255',
            'address'=>'required|max:255',
            'country'=>'required|max:255',
            'city'=>'required|max:255',
            'street'=>'required|max:255',
            'rooms'=>'required',
            'chairs'=>'required',
            'price'=>'required',
            'hours'=>'required',
            'tables'=>'required',
            'type'=>'required|max:255',
            'capacity'=>'required',
            'available'=>'required',
            'owner_id'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->response(null,$validator->errors(),400);
        }
        return $this->addHall($req);
    }

    public function updateAnyInfoInHallController(Request $req,$id){
      return  $this->updateAnyInfoInHall($req,$id);
    }
    public function updateAllInfoOfHallController(Request $req ,$id){
        $validator = Validator::make($req->all(), [
            'name'=>'required|max:255',
            'address'=>'required|max:255',
            'country'=>'required|max:255',
            'city'=>'required|max:255',
            'street'=>'required|max:255',
            'rooms'=>'required',
            'chairs'=>'required',
            'price'=>'required',
            'hours'=>'required',
            'tables'=>'required',
            'type'=>'required|max:255',
            'capacity'=>'required',
            'available'=>'required',
            'owner_id'=>'required',
        ]);

        if ($validator->fails()) {
            return $this->response(null,$validator->errors(),400);
        }
       return $this->updateAllInfoOfHall($req,$id);

    }
    public function destroyHallController($id){
      return  $this->destroyHall($id);
    }
}
