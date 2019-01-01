<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class PassportController extends Controller
{
    public $successStatus = 200;

    public function register(Request $request) {
    	$validator = Validator::make($request->all(), [
    		'name' => 'required',
    		'email' => 'required|email',
    		'password' => 'required',
    		'confirm_password' => 'required|same:password'
    	]);

    	// check if validation fails
    	if ($validator->fails()) {
    		return response()->json(['error' => $validator->errors()], 401);
    	}

    	// if validation is successful
    	$input = $request->all();

    	// encrypt password before sending to db
    	$input['password'] = bcrypt($input['password']);

    	// create the user registration
    	$user = User::create($input);

    	$success['token'] = $user->createToken('EventsManager')->accessToken;
    	$success['name'] = $user->name;
    	$success['email'] = $user->email;

    	return response()->json(['success' => $success, 'status_code' => $this->successStatus, 'status_message' => 'Success'], $this->successStatus);
    }

    public function login(Request $request) {
    	if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
    		$user = Auth::user();

    		$success['token'] = $user->createToken('EventsManager')->accessToken;
	    	$success['email'] = $user->email;

	    	return response()->json(['success' => $success, 'status_code' => $this->successStatus, 'status_message' => 'Success'], $this->successStatus);
    	}

    	// return unauthorized error if login fails
    	return response()->json(['error' => 'Unauthorized'], 401);

    }
}
