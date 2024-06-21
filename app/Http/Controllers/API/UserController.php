<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        if(Auth::attempt(compact('email','password')))
        {
            $user = auth()->user();
            $access_token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'status'=>true,
                'message'=>"User Authenticated Successfully",
                'token'=>$access_token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone
                ]
            ]);
        } else {
            return response()->json([
                'status'=>false,
                'message'=>"Invalid Username or Password"
            ]);
        }
    }

    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->get('name');
        $user->phone = $request->get('phone');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->save();

        $access_token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status'=>true,
            'message'=>"User Registered Successfully",
            'token'=>$access_token
        ]);
    }

    public function show($id)
    {
        // Find the user by ID
        $user = User::find($id);

        if ($user) {
            // Return the user data as a JSON response
            return response()->json($user);
        } else {
            // Return a 404 not found response if the user does not exist
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function changeAccountInfo($id, $phone = null, $name = null)
    {
        // Define validation rules
        $rules = [
            'phone' => 'sometimes|string|max:15', // Assuming phone is stored as a string
            'name' => 'sometimes|string|max:255',
        ];

        // Create data array for validation
        $data = [
            'phone' => $phone,
            'name' => $name,
        ];

        // Validate the request data
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update user information
        if (!is_null($name)) {
            $user->name = $name;
        }

        if (!is_null($phone)) {
            $user->phone = $phone;
        }

        $user->save();

        // Return a success response
        return response()->json(['message' => 'Account information updated successfully', 'user' => $user]);
    }
}
