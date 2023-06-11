<?php

namespace App\Http\Controllers;

use App\Http\classes\hallsClasses;
use App\Http\Resources\hallResource;
use App\Http\responseTrait;
use App\Models\halls;
use App\Models\User;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;




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





    public function recommendHall(Request $request)
    {
        $location = $request->input('location');
        $description = $request->input('description');

        // Sanitize and validate the input parameters as needed

        // Execute the Python script using the wrapper function
        $command = "python C:\xampp\htdocs\test\recco.py " . " " . escapeshellarg($description);
        $output = shell_exec($command);

        // Process the output from the Python script

        // ...

        // Return the recommended halls as a JSON response
        return response()->json($recommendedHalls);
    }










    public function recommendHall2(Request $request)
    {
        $location = $request->input('location');
        $description = $request->input('description');

        // Sanitize and validate the input parameters as needed

        // Execute the Python script using the wrapper function
        $command = "python C:\xampp\htdocs\test\recco.py " . " " . escapeshellarg($description);
        $output = shell_exec($command);

        // Process the output from the Python script
        $recommendedHalls = json_decode($output, true);

        // Retrieve additional data from the database based on the recommended halls
        $hallIds = array_column($recommendedHalls, 'id');

        // Connect to the database
        $connection = new \mysqli('localhost', 'username', 'password', 'database');

        // Check for connection errors

        if ($connection->connect_error) {
            // Handle the connection error
        }

        // Build and execute the SQL query to retrieve hall data
        $query = "SELECT id, name, address, rooms, capacity, tables FROM halls WHERE id IN (" . implode(",", $hallIds) . ")";
        $result = $connection->query($query);

        // Check for query errors

        if (!$result) {
            // Handle the query error
        }

        // Fetch the data and add it to the recommended halls
        $hallData = [];
        while ($row = $result->fetch_assoc()) {
            $hallData[$row['id']] = $row;
        }

        foreach ($recommendedHalls as &$hall) {
            $hallId = $hall['id'];
            if (isset($hallData[$hallId])) {
                $hall['rooms'] = $hallData[$hallId]['rooms'];
                $hall['capacity'] = $hallData[$hallId]['capacity'];
                $hall['tables'] = $hallData[$hallId]['tables'];
            }
        }

        // Close the database connection
        $connection->close();

        // Return the recommended halls as a JSON response
        return response()->json($recommendedHalls);
    }






    public function recommendHalls3(Request $request)
{
    $location = $request->input('location');
    $description = $request->input('description');

    // Sanitize and validate the input parameters as needed

    // Execute the Python script using the wrapper function
    $command = "python C:\xampp\htdocs\test\recco3.py " . " " . escapeshellarg($description);
    $output = shell_exec($command);

    // Process the output from the Python script
    $recommendedHalls = json_decode($output, true);

    // Check if recommendedHalls is null or not an array
    if (!is_array($recommendedHalls)) {
        // Handle the error or return an appropriate response
    }

    // Retrieve additional data from the database based on the recommended halls
    $hallIds = array_column($recommendedHalls, 'id');

    // Connect to the database
    $connection = new \mysqli('localhost', 'username', 'password', 'database');

    // Check for connection errors

    if ($connection->connect_error) {
        // Handle the connection error
    }

    // Build and execute the SQL query to retrieve hall data
    $query = "SELECT id, name, address, rooms, capacity, tables FROM halls WHERE id IN (" . implode(",", $hallIds) . ")";
    $result = $connection->query($query);

    // Check for query errors

    if (!$result) {
        // Handle the query error
    }

    // Fetch the data and add it to the recommended halls
    $hallData = [];
    while ($row = $result->fetch_assoc()) {
        $hallData[$row['id']] = $row;
    }

    foreach ($recommendedHalls as &$hall) {
        $hallId = $hall['id'];
        if (isset($hallData[$hallId])) {
            $hall['rooms'] = $hallData[$hallId]['rooms'];
            $hall['capacity'] = $hallData[$hallId]['capacity'];
            $hall['tables'] = $hallData[$hallId]['tables'];
        }
    }

    // Close the database connection
    $connection->close();

    // Return the recommended halls as a JSON response
    return response()->json($recommendedHalls);
}








public function recommendHalls4(Request $request)
{
    $location = $request->input('location');
    $description = $request->input('description');

    // Execute the Python script and get the recommended halls
    $process = new Process(['python', 'C:\xampp\htdocs\test\recco3.py ', $location, $description]);
    $process->run();
    $output = $process->getOutput();

    // Return the JSON response
    return response()->json($output);
}





public function recommendHalls(Request $request)
{
    $description = $request->input('description');

    // Sanitize and validate the input parameter as needed

    // Execute the Python script using the wrapper function
    $command = "python C:\xampp\htdocs\test\recco4.py " . escapeshellarg($description);
    $output = shell_exec($command);

    // Process the output from the Python script
    $recommendedHalls = json_decode($output, true);

    // Check if recommendedHalls is null or not an array
    if (!is_array($recommendedHalls)) {
        // Handle the error or return an appropriate response
    }

    // Return the recommended halls as a JSON response
    return response()->json($recommendedHalls);
}







public function getRecommendations1(Request $request)
{
    $targetUserId = Auth::guard('user-api')->id();

    // Execute the Python recommendation system code
    $process = new Process(['python', 'C:\xampp\htdocs\test\userrec.py', $targetUserId]);
    $process->run();

    if (!$process->isSuccessful()) {
        throw new \RuntimeException($process->getErrorOutput());
    }

    // Retrieve the recommended halls JSON output from the Python code
    $recommendedHallsJson = $process->getOutput();

    // Return the recommended halls JSON as the API response
    return response()->json(json_decode($recommendedHallsJson));
}








public function getRecommendations2(Request $request)
{
    $targetUserId = Auth::guard('user-api')->id();

    // Execute the Python recommendation system code
    // $scriptPath = 'userrec.py';
    $scriptPath = 'colrec3.py';
    $process = new Process(["python", $scriptPath, $targetUserId]);

    try {
        $process->mustRun();
        $recommendedHallsJson = $process->getOutput();

        // Return the recommended halls JSON as the API response
        return response()->json(json_decode($recommendedHallsJson));
    } catch (ProcessFailedException $exception) {
        throw new \RuntimeException($exception->getMessage());
    }
}




public function getRecommendations(Request $request)
{
    // Prepare the command to execute the Python script
    $command = "python3 C:\xampp\htdocs\test\userrec.py'";


    $targetUserId = Auth::guard('user-api')->id();

    // Append the user ID to the command
    $command .= " $targetUserId";

    // Execute the command and capture the output
    $output = shell_exec($command);

    // Convert the JSON output to an array
    $recommendedHalls = json_decode($output, true);

    // Return the recommended halls as a JSON response
    return response()->json($recommendedHalls);
}






}
