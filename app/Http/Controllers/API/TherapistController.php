<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Therapist;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class TherapistController extends Controller
{

    public function login(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        // Find the therapist by email
        $therapist = Therapist::where('email', $email)->first();

        if ($therapist && Hash::check($password, $therapist->password)) {
            // Password is correct, manually log in the therapist
            Auth::login($therapist);

            $access_token = $therapist->createToken('authToken')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Therapist Authenticated Successfully',
                'token' => $access_token,
                'therapist' => [
                    'id' => $therapist->id,
                    'name' => $therapist->name,
                    'email' => $therapist->email,
                    'phone' => $therapist->phone,
                    'application_status' => $therapist->application_status,
                    'cv_file_path' => $therapist->cv_file_path,
                    'experience' => $therapist->experience,
                    'description_profile' => $therapist->description_profile,
                    'description_registration' => $therapist->description_registration,
                    'id_front_pic' => $therapist->id_front_pic,
                    'id_back_pic' => $therapist->id_back_pic,
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Email or Password'
            ], 401); // HTTP status code 401 for unauthorized
        }
    }
    

    public function register(Request $request)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:therapists,email',
                'phone' => 'required|string|unique:therapists,phone',
                'password' => 'required|string|min:6',
                'verificationCode' => 'nullable|string',
                'cvFilePath' => 'required|string',
                'experience' => 'required|integer',
                'descriptionProfile' => 'nullable|string',
                'descriptionRegistration' => 'nullable|string',
                'id_front_pic' => 'required|string',
                'id_back_pic' => 'required|string',
            ]);
    
            // Automatically set application status to "pending"
            $validatedData['applicationStatus'] = 'pending';
    
            // Hash the password
            $validatedData['password'] = Hash::make($validatedData['password']);
    
            // Create a new therapist record
            $therapist = Therapist::create($validatedData);
    
            // Generate token for the therapist
            $token = $therapist->createToken('TherapistToken')->plainTextToken;
            $user->sendEmailVerificationNotification();
            
            // Return success response with therapist data and token
            return response()->json([
                'status' => true,
                'message' => 'Therapist registered successfully',
                'therapist' => $therapist->toArray(),
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'status' => false,
                'message' => 'Failed to register therapist. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getAllTherapists()
    {
        // Retrieve all therapists from the database
        $therapists = Therapist::all();

        // Return the list of therapists in JSON format
        return response()->json([
            'status' => true,
            'message' => 'Therapists retrieved successfully',
            'therapists' => $therapists
        ]);
    }
    
}

