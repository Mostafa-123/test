






public function adminLogin1(Request $request){


    try {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        //login

        $credentials = $request->only(['email', 'password']);

        $token = Auth::guard('admin-api')->attempt($credentials);

        if (!$token){
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
    $admin = Auth::guard('admin-api')->user();
    $admin->api_token = $token;
    //return token
    return $this->returnData('admin',new adminResource($admin),"data have returned");


    }

        catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


}
public function loginOwner1(Request $request){


    try {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        //login

        $credentials = $request->only(['email', 'password']);

        $token = Auth::guard('owner-api')->attempt($credentials);

        if (!$token){
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
    $owner = Auth::guard('owner-api')->user();
    $owner->api_token = $token;
    //return token
    return $this->returnData('owner', new ownerResource($owner),"data have returned");


    }

        catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


}
public function loginPlanner1(Request $request){


    try {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        //login

        $credentials = $request->only(['email', 'password']);

        $token = Auth::guard('planner-api')->attempt($credentials);

        if (!$token){
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
    $planner = Auth::guard('planner-api')->user();
    $planner->api_token = $token;
    //return token
    return $this->returnData('planner',new plannersResource($planner),"data have returned");




    }



        catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


}
public function loginUser1(Request $request){


    try {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        //login

        $credentials = $request->only(['email', 'password']);

        $token = Auth::guard('user-api')->attempt($credentials);

        if (!$token){
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
    $user = Auth::guard('user-api')->user();
    $user->api_token = $token;
    //return token
    return $this->returnData('user',new personResource($user),"data have returned");




    }



        catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


}











public function registerAdmin1(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|between:2,100',
        'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners',
        'password' => 'required|string|min:6',


    ]);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }
    $admin = Admin::create(array_merge(
        $validator->validated(),
        ['password' => bcrypt($request->password)]
    ));
    return response()->json([
        'message' => 'admin successfully registered',
        'admin' => $admin
    ], 201);
}
public function registerPlanner1(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|between:2,100',
        'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners',
        'password' => 'required|string|min:6',


    ]);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }
    $planner = Planner::create(array_merge(
        $validator->validated(),
        ['password' => bcrypt($request->password)]
    ));
    return response()->json([
        'message' => 'planner successfully registered',
        'planner' => $planner
    ], 201);
}
public function registerOwner1(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|between:2,100',
        'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners',
        'password' => 'required|string|min:6',


    ]);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }
    $owner = Owner::create(array_merge(
        $validator->validated(),
        ['password' => bcrypt($request->password)]
    ));
    return response()->json([
        'message' => 'owner successfully registered',
        'owner' => $owner
    ], 201);
}
public function registerUser1(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|between:2,100',
        'email' => 'required|string|email|max:100|unique:admins|unique:users|unique:owners|unique:planners',
        'password' => 'required|string|min:6',


    ]);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }
    $user = User::create(array_merge(
        $validator->validated(),
        ['password' => bcrypt($request->password)]
    ));
    return response()->json([
        'message' => 'owner successfully registered',
        'user' => $user
    ], 201);
}
