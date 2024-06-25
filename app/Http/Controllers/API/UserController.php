<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



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

    public function register(Request $request) // Done
    {
        $user = new User();
        $user->name = $request->get('name');
        $user->phone = $request->get('phone');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->save();

        $access_token = $user->createToken('authToken')->plainTextToken;
        $user->sendEmailVerificationNotification();
        return response()->json([
            'status'=>true,
            'message'=>"User Registered Successfully",
            'token'=>$access_token
        ]);
    }

    public function show($id) // Done
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

    public function changeAccountInfo(Request $request, $id) // Done
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
        ]);

        // Find the user by ID
        $user = User::findOrFail($id);

        // Update user information
        if ($request->has('name')) {
            $user->name = $validatedData['name'];
        }
        if ($request->has('phone')) {
            $user->phone = $validatedData['phone'];
        }

        // Save the updated user
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Account Information updated successfully',
        ], 200);
    }

    public function changePassword(Request $request, $id) // Done
    {
        // Validate incoming request
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|different:current_password',
            'retype_password' => 'required|same:new_password',
        ]);

        // Get authenticated user
        $authenticatedUser = Auth::user();

        // Check if authenticated user is the same as the user whose password is being changed
        if ($authenticatedUser->id != $id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. You can only change your own password.'
            ], 403);
        }

        // Find user by ID
        $user = User::find($id);

        // Check if user exists
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if current password matches
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect'
            ], 401);
        }

        // Change password
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ]);
    }


    
}
