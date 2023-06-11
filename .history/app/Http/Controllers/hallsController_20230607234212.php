<?php

namespace App\Http\Controllers;

use App\Http\classes\hallsClasses;
use App\Http\Resources\hallResource;
use App\Http\responseTrait;
use App\Models\halls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


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





    public function recommendHalls(Request $request)
    {
        $location = $request->input('location');
        $description = $request->input('description');

        // Sanitize and validate the input parameters as needed

        // Execute the Python script using the wrapper function
        $command = "python C:\xampp\htdocs\test\recco3.py " . escapeshellarg($location) . " " . escapeshellarg($description);
        $output = shell_exec($command);

        // Process the output from the Python script

        // ...

        // Return the recommended halls as a JSON response
        return response()->json($output);
    }




}
